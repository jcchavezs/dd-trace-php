<?php

namespace DdTrace\Encoders;

use DdTrace\Encoder;
use DdTrace\TracesBuffer;

final class Json implements Encoder
{
    private $encodedContent;
    private $tracesFormatter;

    public function __construct()
    {
        $this->tracesFormatter = new TracesFormatter;
    }

    public function encodeTraces(TracesBuffer $traces)
    {
        $this->encodedContent = json_encode($this->tracesFormatter->__invoke($traces));
    }

    public function encodeServices(array $services)
    {
        $this->encodedContent = json_encode($services);
    }

    public function read()
    {
        return $this->encodedContent;
    }

    /** @return string */
    public function contentType()
    {
        return "application/json";
    }
}
