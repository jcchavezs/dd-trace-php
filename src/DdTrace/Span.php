<?php

namespace DdTrace;

use Exception;
use InvalidArgumentException;
use Nanotime\Nanotime;
use Nanotime\NanotimeInterval;
use Throwable;
use TracingContext\TracingContext;

class Span
{
    const ERROR_MSG_KEY = 'error.msg';
    const ERROR_TYPE_KEY = 'error.type';
    const ERROR_STACK_KEY = 'error.stack';
    const ERROR_VALUE = 1;

    private $traceId = '';
    private $spanId = '';
    private $name = '';
    private $resource;
    private $service;
    private $type = '';
    private $start;

    /** @var NanotimeInterval */
    private $duration;
    private $parentId = '';
    private $error = 0;
    private $isSampled = true;
    private $meta = [];
    private $metrics = [];
    private $isFinished = false;
    private $tracer;

    private function __construct(
        Tracer $tracer,
        $name,
        $service,
        $resource,
        $spanId = null,
        $traceId = null,
        $parentId = null
    ) {

        $start = Nanotime::now();
        $spanId = $spanId ?: (string) self::randomId(); //casting as a string to keep ids consistant.
        $traceId = $traceId ?: $spanId;

        $this->name = (string) $name;
        $this->service = (string) $service;
        $this->resource = (string) $resource;
        $this->start = $start;
        $this->spanId = (string) $spanId;   //casting as a string to keep uint64 ids passed in from overflowing PHP_INT_MAX
        $this->traceId = (string) $traceId; //casting as a string to keep uint64 ids passed in from overflowing PHP_INT_MAX
        $this->parentId = (string) $parentId; //casting as a string to keep uint64 ids passed in from overflowing PHP_INT_MAX
        $this->tracer = $tracer;
    }

    public static function create(Tracer $tracer, $name, $service, $resource, $spanId = null, $traceId = null, $parentId = null)
    {
        return new self($tracer, $name, $service, $resource, $spanId, $traceId, $parentId);
    }

    public static function createAsRoot(Tracer $tracer, $name, $service, $resource)
    {
        return new self($tracer, $name, $service, $resource);
    }

    public static function createAsChildOf(Tracer $tracer, $name, Span $parent)
    {
        return new self($tracer, $name, $parent->service, $parent->resource, null, $parent->traceId, $parent->spanId);
    }

    public function spanId()
    {
        return $this->spanId;
    }

    public function parentId()
    {
        return $this->parentId;
    }

    public function traceId()
    {
        return $this->traceId;
    }

    public function isSampled()
    {
        return $this->isSampled;
    }

    public function setMeta($key, $value)
    {
        $this->meta[$key] = $value;
    }

    public function name()
    {
        return $this->name;
    }

    public function withName($name)
    {
        $this->name = $name;
    }

    public function resource()
    {
        return $this->resource;
    }

    public function service()
    {
        return $this->service;
    }

    public function forService($service)
    {
        $this->service = $service;
    }

    public function type()
    {
        return $this->type;
    }

    public function setType($type) {
        $this->type = $type;
    }

    public function start()
    {
        return $this->start;
    }

    public function duration()
    {
        return $this->duration;
    }

    public function getMeta($key)
    {
        return $this->meta[$key];
    }

    public function setMetric($key, $value)
    {
        $this->metrics[$key] = $value;
    }

    public function setError($e)
    {
        if (!($e instanceof Exception || $e instanceof Throwable)) {
            throw new InvalidArgumentException(
                sprintf('Error should be either Exception or Throwable, got %s.', gettype($e))
            );
        }

        $this->error = self::ERROR_VALUE;
        $this->setMeta(self::ERROR_MSG_KEY, $e->getMessage());
        $this->setMeta(self::ERROR_TYPE_KEY, get_class($e));
        $this->setMeta(self::ERROR_STACK_KEY, $e->getTraceAsString());
    }

    public function meta()
    {
        return $this->meta;
    }

    public function error()
    {
        return $this->error;
    }

    public function hasError()
    {
        return ($this->error == self::ERROR_VALUE);
    }

    public function finish()
    {
        if ($this->isFinished) {
            return;
        }

        $this->duration = Nanotime::now()->diff($this->start);
        $this->isFinished = true;
        $this->tracer->record($this);
    }

    public function finishWithError($e)
    {
        $this->setError($e);
        $this->finish();
    }

    public function isFinished()
    {
        return $this->isFinished;
    }

    /**
     * Context returns a copy of the given context that includes this span.
     *  This span can be accessed downstream with SpanFromContext and friends.
     *
     * @return TracingContext
     */
    public function injectIntoContext(TracingContext $context)
    {
        return $context->withValue(Context::DATA_DOG_TRACE_SPAN, $this);
    }

    public function __toString()
    {
        $lines = [
            sprintf("Name: %s", $this->name),
            sprintf("Service: %s", $this->service),
            sprintf("Resource: %s", $this->resource),
            sprintf("TraceID: %d", $this->traceId),
            sprintf("SpanID: %d", $this->spanId),
            sprintf("ParentID: %d", $this->parentId),
            sprintf("Start: %s", $this->start->nanotime()),
            sprintf("Duration: %s", $this->duration ? $this->duration->nanotime() : "") ,
            sprintf("Error: %s", $this->error),
            sprintf("Type: %s", $this->type),
            "Tags:",
        ];

        foreach ($this->meta as $key => $value) {
            $lines[] = sprintf("\t%s:%s", $key, $value);
        }

        return join("\n", $lines);
    }

    private static function randomId()
    {
        return mt_rand(0, PHP_INT_MAX);
    }
}
