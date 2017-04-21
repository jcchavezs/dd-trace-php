<?php

namespace DdTrace\Encoders;

use DdTrace\Encoder;
use DdTrace\TracesBuffer;
use MessagePack\Packer;

final class MsgPack implements Encoder
{
    private $encodedContent;
    private $packer;
    private $tracesFormatter;

    public function __construct()
    {
        $this->packer = new Packer;
        $this->tracesFormatter = new TracesFormatter;
    }

    public function encodeTraces(TracesBuffer $traces)
    {
        $this->encodedContent = $this->packer->pack($this->tracesFormatter->__invoke($traces));
    }

    public function encodeServices(array $services)
    {
        $this->encodedContent = $this->packer->pack($services);
    }

    public function read()
    {
        return $this->encodedContent;
    }

    /** @return string */
    public function contentType()
    {
        return "application/msgpack";
    }
}
