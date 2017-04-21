<?php

namespace DdTrace\Encoders;

use DdTrace\EncoderFactory;

final class JsonFactory implements EncoderFactory
{
    public function build()
    {
        return new Json;
    }
}
