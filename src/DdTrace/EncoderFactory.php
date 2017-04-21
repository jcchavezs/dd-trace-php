<?php

namespace DdTrace;

interface EncoderFactory
{
    /** @return Encoder */
    public function build();
}
