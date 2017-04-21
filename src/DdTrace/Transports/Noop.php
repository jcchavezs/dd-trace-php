<?php

namespace DdTrace\Transports;

use DdTrace\TracesBuffer;
use DdTrace\Transport;

class Noop implements Transport
{

    public function sendTraces(TracesBuffer $tracesBuffer)
    {

    }

    public function sendServices(array $services)
    {

    }

    public function setHeader($key, $value)
    {

    }
}
