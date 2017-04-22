<?php

namespace DdTrace\Encoders;

use DdTrace\Encoder;
use DdTrace\TracesBuffer;
use MessagePack\Packer;

final class MsgPack implements Encoder
{
    private $packer;
    private $tracesFormatter;
    private $encodedContent;

    public function __construct(TracesFormatter $tracesFormatter, Packer $packer)
    {
        $this->tracesFormatter = $tracesFormatter;
        $this->packer = $packer;
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

    public function contentType()
    {
        return "application/msgpack";
    }
}
