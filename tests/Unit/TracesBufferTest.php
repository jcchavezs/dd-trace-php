<?php

namespace DdTraceTests\Unit;

use DdTrace\TracesBuffer;
use DdTraceTests\Builders\SpanBuilder;
use PHPUnit_Framework_TestCase;

final class TracesBufferTest extends PHPUnit_Framework_TestCase
{
    private $tracesBuffer;

    public function testItHasTheExpectedNumberOfTraces()
    {
        $this->givenATracesBuffer();
        $this->whenAddingTwoSpansFromTrace1();
        $this->andOneSpanFromTrace2();
        $this->thenTheTracesBufferHasTwoTraces();
    }

    public function testItHasTheExpectedSpans()
    {
        $this->givenATracesBuffer();
        $this->whenAddingTwoSpansFromTrace1();
        $this->andOneSpanFromTrace2();
        $this->thenTheTracesBufferHasTheExpectedSpansInTheTraces();
    }

    private function givenATracesBuffer()
    {
        $this->tracesBuffer = new TracesBuffer;
    }

    private function whenAddingTwoSpansFromTrace1()
    {
        $span1 = SpanBuilder::create()->withTraceId(1)->build();
        $this->tracesBuffer->push($span1);

        $span2 = SpanBuilder::create()->withTraceId(1)->build();
        $this->tracesBuffer->push($span2);
    }

    private function andOneSpanFromTrace2()
    {
        $span3 = SpanBuilder::create()->withTraceId(2)->build();
        $this->tracesBuffer->push($span3);
    }

    private function thenTheTracesBufferHasTwoTraces()
    {
        $this->assertEquals(2, $this->tracesBuffer->count());
    }

    private function thenTheTracesBufferHasTheExpectedSpansInTheTraces()
    {
        foreach ($this->tracesBuffer as $trace => $spanCollection) {
            if ($trace == 1) {
                $this->assertEquals(2, $spanCollection->count());
            }

            if ($trace == 2) {
                $this->assertEquals(1, $spanCollection->count());
            }
        }
    }
}
