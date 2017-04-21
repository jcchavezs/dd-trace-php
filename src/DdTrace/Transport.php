<?php

namespace DdTrace;

use Psr\Http\Message\ResponseInterface;

interface Transport
{
    /**
     * @return ResponseInterface
     */
    public function sendTraces(TracesBuffer $traces);

    /** @return ResponseInterface */
    public function sendServices(array $services);

    public function setHeader($key, $value);
}
