<?php

namespace DdTrace\Encoders;

use DdTrace\Encoder;
use DdTrace\TracesBuffer;

class Noop implements Encoder
{

    public function encodeTraces(TracesBuffer $traces)
    {

    }

    public function encodeServices(array $services)
    {

    }

    public function read()
    {

    }

    public function contentType()
    {

    }
}
