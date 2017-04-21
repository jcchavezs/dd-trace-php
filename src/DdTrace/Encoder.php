<?php

namespace DdTrace;

use Psr\Http\Message\StreamInterface;

interface Encoder
{
    public function encodeTraces(TracesBuffer $traces);

    public function encodeServices(array $services);

    /** @return resource|string|null|int|float|bool|StreamInterface|callable */
    public function read();

    /** @return string */
    public function contentType();
}
