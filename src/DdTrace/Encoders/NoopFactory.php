<?php

namespace DdTrace\Encoders;

use DdTrace\EncoderFactory;

final class NoopFactory implements EncoderFactory
{
    public function build()
    {
        return new Noop;
    }
}
