<?php

namespace DdTrace;

use DdTrace\Transports\Noop as NoopTransport;
use Exception;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

final class Tracer implements TracerInterface
{
    private $isEnabled = true;
    private $buffer;
    private $logger;
    private $debugLoggingEnabled;
    private $transport;
    private $servicesModified;
    private $services;
    private $meta = [];

    public function __construct(
        Buffer $buffer,
        LoggerInterface $logger,
        Transport $transport,
        $debugLoggingEnabled = false
    ) {
        $this->buffer = $buffer;
        $this->transport = $transport;
        $this->logger = $logger;
        $this->debugLoggingEnabled = $debugLoggingEnabled;
    }

    public static function noop()
    {
        return new self(new Buffer, new NullLogger, new NoopTransport);
    }

    /** @return Span */
    public function createRootSpan($name, $service, $resource)
    {
        $span = Span::createAsRoot($this, $name, $service, $resource);

        array_walk($this->meta, function($value, $key) use ($span) {
            $span->setMeta($key, $value);
        });

        return $span;
    }

    /** @return Span */
    public function createChildSpan($name, Span $parent)
    {
        $span = Span::createAsChildOf($this, $name, $parent);

        array_walk($this->meta, function($value, $key) use ($span) {
            $span->setMeta($key, $value);
        });

        return $span;
    }

    public function record(Span $span)
    {
        if ($this->isEnabled) {
            $this->buffer->push($span);
        }
    }

    /**
     * FlushTraces will push any currently buffered traces to the server.
     * XXX Note that it is currently exported because some tests use it. They
     * really should not.
     */
    public function flushTraces()
    {
        $spans = $this->buffer->pop();
        $spansCount = $spans->count();

        if ($this->debugLoggingEnabled) {
            $this->logger->debug(sprintf("Sending %d spans", $spansCount));

            foreach($spans as $span) {
                $this->logger->debug(sprintf("SPAN:\n%s", $span->__toString()));
            }
        }

        if (!$this->isEnabled() || !$this->hasTransport() || $spansCount == 0) {
            return;
        }

        $traceBuffer = TracesBuffer::fromSpanCollection($spans);

        $this->transport->sendTraces($traceBuffer);
    }

    public function flush()
    {
        $numberOfSpans = $this->buffer->length();

        try {
            $this->flushTraces();
        } catch (Exception $e) {
            $this->logger->error(sprintf("Cannot flush traces: %v. Lost %d spans"), $e->getMessage(), $numberOfSpans);
        }

        try {
            $this->flushServices();
        } catch (Exception $e) {
            $this->logger->error(sprintf("Cannot flush services: %v", $e->getMessage()));
        }
    }

    public function isEnabled()
    {
        return $this->isEnabled;
    }

    public function enable()
    {
        $this->isEnabled = true;
    }

    public function disable()
    {
        $this->isEnabled = false;
    }

    public function enableDebugLogging()
    {
        $this->debugLoggingEnabled = true;
    }

    public function disableDebugLogging()
    {
        $this->debugLoggingEnabled = false;
    }

    public function meta()
    {
        return $this->meta;
    }

    public function setMeta($key, $value)
    {
        $this->meta[(string) $key] = (string) $value;
    }

    private function flushServices()
    {
        if (!$this->isEnabled || !$this->servicesModified()) {
            return;
        }

        try {
            $this->transport->sendServices($this->services);

            $this->servicesModified = false;
        } catch (Exception $e) {
            throw $e;
        }
    }

    private function hasTransport()
    {
        return $this->transport !== null;
    }

    private function servicesModified()
    {
        return false;
    }
}
