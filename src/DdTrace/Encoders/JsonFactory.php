<?php

namespace DdTrace\Encoders;

use DdTrace\EncoderFactory;

final class JsonFactory implements EncoderFactory
{
    private $tracesFormatter;

    public function __construct()
    {
        $this->tracesFormatter = new TracesFormatter;
    }

    public function build()
    {
        return new Json($this->tracesFormatter);
    }
}
