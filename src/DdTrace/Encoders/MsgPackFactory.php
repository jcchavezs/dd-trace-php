<?php

namespace DdTrace\Encoders;

use DdTrace\EncoderFactory;
use MessagePack\Packer;

final class MsgPackFactory implements EncoderFactory
{
    private $packer;

    public function __construct(Packer $packer)
    {
        $this->packer = $packer;
    }

    public function build()
    {
        return new MsgPack;
    }
}
