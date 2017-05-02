<?php

namespace DdTraceTests\Unit;

use DdTrace\Span;
use DdTrace\Tracer;
use DdTraceTests\Builders\TracerBuilder;
use PHPUnit_Framework_TestCase;

final class TracerTest extends PHPUnit_Framework_TestCase
{
    /** @var Tracer */
    private $tracer;

    /** @var Span */
    private $span;

    /** @var array */
    private $meta;

    public function testATracerWithoutMetaValuesCreatesAnSpanWithoutMetaValues()
    {
        $this->givenATracer();
        $this->whenCreatingAnSpan();
        $this->thenSpanContainsNoMetaValues();
    }

    public function testATracerCreatesAnSpanAndPropagatesItsMetaValues()
    {
        $this->givenATracer();
        $this->thatContainsMetaValues();
        $this->whenCreatingAnSpan();
        $this->thenSpanContainsTheSameMetaValuesAsTheTracer();
    }

    private function givenATracer()
    {
        $this->tracer = TracerBuilder::create()->build();
    }

    private function thatContainsMetaValues()
    {
        $this->meta = [
            'test_key_1' => 'test_value_1',
            'test_key_2' => 'test_value_2',
        ];

        $tracer = $this->tracer;

        array_walk($this->meta, function($value, $key) use ($tracer) {
            $tracer->setMeta($key, $value);
        });
    }

    private function whenCreatingAnSpan()
    {
        $this->span = $this->tracer->createRootSpan('test_name', 'test_service', 'test_resource');
    }

    private function thenSpanContainsTheSameMetaValuesAsTheTracer()
    {
        $this->assertEquals($this->meta, $this->span->meta());
    }

    private function thenSpanContainsNoMetaValues()
    {
        $this->assertEmpty($this->span->meta());
    }
}
