<?php

namespace DdTraceTests\Unit;

use DdTrace\TracesBuffer;
use DdTraceTests\Builders\SpanBuilder;
use PHPUnit_Framework_TestCase;

final class TracesBufferTest extends PHPUnit_Framework_TestCase
{
    public function testItHasTheExpectedNumberOfTraces()
    {
        $span1 = SpanBuilder::create()->withTraceId(1)->build();
        $span2 = SpanBuilder::create()->withTraceId(1)->build();
        $span3 = SpanBuilder::create()->withTraceId(2)->build();

        $tracesBuffer = new TracesBuffer;
        $tracesBuffer->push($span1);
        $tracesBuffer->push($span2);
        $tracesBuffer->push($span3);

        $this->assertEquals(2, $tracesBuffer->count());
    }

    public function testItHasTheExpectedSpans()
    {
        $span1 = SpanBuilder::create()->withTraceId(1)->build();
        $span2 = SpanBuilder::create()->withTraceId(1)->build();
        $span3 = SpanBuilder::create()->withTraceId(2)->build();

        $tracesBuffer = new TracesBuffer;
        $tracesBuffer->push($span1);
        $tracesBuffer->push($span2);
        $tracesBuffer->push($span3);

        foreach ($tracesBuffer as $trace => $spanCollection) {
            if ($trace == 1) {
                $this->assertEquals(2, $spanCollection->count());
            }

            if ($trace == 2) {
                $this->assertEquals(1, $spanCollection->count());
            }
        }
    }
}
