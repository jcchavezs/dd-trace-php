<?php

namespace DdTraceTests\Unit\Encoders;

use DdTrace\Encoders\TracesFormatter;
use DdTrace\TracesBuffer;
use DdTraceTests\Builders\SpanBuilder;
use PHPUnit_Framework_TestCase;

final class TracesFormatterTest extends PHPUnit_Framework_TestCase
{
    public function testReturnsEmptyOnEmptyTracesBuffer()
    {
        $tracesBuffer = new TracesBuffer;
        $tracesFormatter = new TracesFormatter;
        $actualFormatted = $tracesFormatter->__invoke($tracesBuffer);

        foreach ($actualFormatted as $value) {
            $this->assertTrue(false);
        }
    }

    public function testReturnsTheExpectedArray()
    {
        $tracesBuffer = new TracesBuffer;
        $tracesBuffer->push(SpanBuilder::create()->withTraceId(1)->withName("span1")->asFinished()->build());
        $tracesBuffer->push(SpanBuilder::create()->withTraceId(2)->withName("span2")->asFinished()->build());
        $tracesFormatter = new TracesFormatter;
        $actualFormatted = $tracesFormatter->__invoke($tracesBuffer);

        foreach ($actualFormatted as $i => $formattedSpanArray) {
            $this->assertArraySubset([
                'trace_id' => $i + 1,
                'name' => sprintf("span%d", $i + 1)
            ], $formattedSpanArray[0]);
        }
    }
}
