<?php

namespace DdTrace\Encoders;

use DdTrace\Encoder;
use DdTrace\TracesBuffer;

final class Json implements Encoder
{
    private $encodedContent;
    private $tracesFormatter;

    public function __construct(TracesFormatter $tracesFormatter)
    {
        $this->tracesFormatter = $tracesFormatter;
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

    public function contentType()
    {
        return "application/json";
    }
}
