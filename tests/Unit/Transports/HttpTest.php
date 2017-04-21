<?php

namespace DdTraceTests\Unit\Transports;

use DdTrace\Encoder;
use DdTrace\EncoderFactory;
use DdTrace\TracesBuffer;
use DdTrace\Transports\Http;
use DdTraceTests\Builders\SpanBuilder;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Response;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

final class HttpTest extends PHPUnit_Framework_TestCase
{
    const TEST_STATUS_CODE = 0;
    /** @var ClientInterface|PHPUnit_Framework_MockObject_MockObject */
    private $client;

    /** @var LoggerInterface|PHPUnit_Framework_MockObject_MockObject */
    private $logger;

    /** @var EncoderFactory|PHPUnit_Framework_MockObject_MockObject */
    private $encoderFactory;

    /** @var Encoder|PHPUnit_Framework_MockObject_MockObject */
    private $encoder;

    private $tracesBuffer;

    /** @var Response */
    private $expectedResponse;

    /** @var Response */
    private $actualResponse;

    protected function setUp()
    {
        $this->client = $this->createMock(ClientInterface::class);
        $this->logger = new NullLogger;
        $this->encoder = $this->createMock(Encoder::class);
        $this->encoderFactory = $this->createMock(EncoderFactory::class);
        $this->encoderFactory->method('build')->willReturn($this->encoder);
        parent::setUp();
    }

    public function testTracesAreSentCorrectly()
    {
        $this->givenATraceBuffer();
        $this->thenTheTracesAreBeingEncoded();
        $this->andTheEncodedTracesAreBeingSent();
        $this->whenSendingTraces();
        $this->thenTheResponseIsTheExpected();
    }

    private function givenATraceBuffer()
    {
        $span1 = SpanBuilder::create()->withTraceId(1)->build();
        $span2 = SpanBuilder::create()->withTraceId(2)->build();

        $this->tracesBuffer = new TracesBuffer;
        $this->tracesBuffer->push($span1);
        $this->tracesBuffer->push($span2);
    }

    private function whenSendingTraces()
    {
        $httpTransport = new Http(
            $this->client,
            $this->logger,
            $this->encoderFactory,
            'localhost:8126'
        );

        $this->actualResponse = $httpTransport->sendTraces($this->tracesBuffer);
    }

    private function thenTheTracesAreBeingEncoded()
    {
        $this->encoder
            ->expects($this->once())
            ->method('encodeTraces')
            ->with($this->tracesBuffer);
    }

    private function andTheEncodedTracesAreBeingSent()
    {
        $this->expectedResponse = new Response(self::TEST_STATUS_CODE);

        $this->client
            ->expects($this->once())
            ->method('send')
            ->willReturn($this->expectedResponse);
    }

    private function thenTheResponseIsTheExpected()
    {
        $this->assertEquals(self::TEST_STATUS_CODE, $this->actualResponse->getStatusCode());
    }
}
