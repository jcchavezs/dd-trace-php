<?php

namespace DdTrace\Encoders;

use DdTrace\EncoderFactory;
use MessagePack\Packer;

final class MsgPackFactory implements EncoderFactory
{
    private $packer;
    private $tracesFormatter;

    public function __construct()
    {
        $this->packer = new Packer;
        $this->tracesFormatter = new TracesFormatter;
    }

    public function build()
    {
        return new MsgPack($this->tracesFormatter, $this->packer);
    }
}
