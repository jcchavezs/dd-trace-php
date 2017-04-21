<?php

namespace DdTraceTests\Builders;

use DdTrace\Buffer;
use DdTrace\Tracer;
use DdTrace\Transports\Noop as NoopTransport;
use Psr\Log\NullLogger;

final class TracerBuilder
{
    private $buffer;
    private $logger;
    private $transport;

    private function __construct()
    {
        $this->buffer = new Buffer;
        $this->logger = new NullLogger;
        $this->transport = new NoopTransport;
    }

    public static function create()
    {
        return new self();
    }

    public function build()
    {
        return new Tracer($this->buffer, $this->logger, $this->transport);
    }
}
