<?php

namespace DdTrace;

use Psr\Http\Message\ResponseInterface;

interface Transport
{
    /**
     * @return ResponseInterface
     */
    public function sendTraces(TracesBuffer $tracesBuffer);

    /** @return ResponseInterface */
    public function sendServices(array $services);

    public function setHeader($key, $value);
}
