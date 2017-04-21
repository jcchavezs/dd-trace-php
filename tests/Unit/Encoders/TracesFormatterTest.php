<?php

namespace DdTraceTests\Unit\Encoders;

use DdTrace\Encoders\TracesFormatter;
use DdTrace\TracesBuffer;
use DdTraceTests\Builders\SpanBuilder;
use PHPUnit_Framework_TestCase;

final class TracesFormatterTest extends PHPUnit_Framework_TestCase
{
    private $tracesBuffer;
    private $actualFormatted;

    public function testReturnsNoTracesOnEmptyTracesBuffer()
    {
        $this->givenAnEmptyTracesBuffer();
        $this->whenFormattingTheTracesBuffer();
        $this->thenTheFormatterReturnsAndEmptyOutput();
    }

    public function testReturnsTheExpectedArray()
    {
        $this->givenATracesBufferWithTwoSpans();
        $this->whenFormattingTheTracesBuffer();
        $this->thenTheFormatterOutputsTheExpectedOutput();
    }

    private function givenAnEmptyTracesBuffer()
    {
        $this->tracesBuffer = new TracesBuffer;
    }

    private function givenATracesBufferWithTwoSpans()
    {
        $this->tracesBuffer = new TracesBuffer;
        $this->tracesBuffer->push(SpanBuilder::create()->withTraceId(1)->withName("span1")->asFinished()->build());
        $this->tracesBuffer->push(SpanBuilder::create()->withTraceId(2)->withName("span2")->asFinished()->build());
    }

    private function whenFormattingTheTracesBuffer()
    {
        $tracesFormatter = new TracesFormatter;
        $this->actualFormatted = $tracesFormatter->__invoke($this->tracesBuffer);
    }

    private function thenTheFormatterReturnsAndEmptyOutput()
    {
        foreach ($this->actualFormatted as $value) {
            $this->assertTrue(false);
        }
    }

    private function thenTheFormatterOutputsTheExpectedOutput()
    {
        foreach ($this->actualFormatted as $i => $formattedSpanArray) {
            $this->assertArraySubset([
                'trace_id' => $i + 1,
                'name' => sprintf("span%d", $i + 1)
            ], $formattedSpanArray[0]);
        }
    }
}
