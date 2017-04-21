<?php

namespace DdTraceTests\Builders;

use DdTrace\Span;

class SpanBuilder
{
    private $name = '';
    private $service = '';
    private $resource = '';
    private $isFinished = false;
    private $traceId;

    public static function create()
    {
        return new self();
    }

    public function build()
    {
        $span = Span::create(TracerBuilder::create()->build(), $this->name, $this->service, $this->resource, null, $this->traceId);

        if ($this->isFinished) {
            $span->finish();
        }

        return $span;
    }

    public function asFinished()
    {
        $this->isFinished = true;
        return $this;
    }

    public function withTraceId($traceId)
    {
        $this->traceId = (int) $traceId;
        return $this;
    }

    public function withName($name)
    {
        $this->name = (string) $name;
        return $this;
    }
}
