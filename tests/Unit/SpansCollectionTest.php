<?php

namespace DdTraceTests\Unit;

use DdTrace\SpansCollection;
use DdTraceTests\Builders\SpanBuilder;
use PHPUnit_Framework_TestCase;

final class SpansCollectionTest extends PHPUnit_Framework_TestCase
{
    /** @var SpansCollection */
    private $spansCollection;

    /** @var array */
    private $actualMap;

    public function testAnSpanCollectionHasTheExpectedLength()
    {
        $this->givenAnSpansCollection();
        $this->whenAddingTwoSpans();
        $this->thenTheSpansCollectionHasTwoSpans();
    }

    public function testAnSpanCollectionCanBeMapped()
    {
        $this->givenAnSpansCollection();
        $this->whenAddingTwoSpans();
        $this->whenMappingTheSpansCollection();
        $this->thenTheMapIsTheExpected();
    }

    private function givenAnSpansCollection()
    {
        $this->spansCollection = new SpansCollection;
    }

    private function whenAddingTwoSpans()
    {
        $this->spansCollection->push(SpanBuilder::create()->build());
        $this->spansCollection->push(SpanBuilder::create()->build());
    }

    private function thenTheSpansCollectionHasTwoSpans()
    {
        $this->assertEquals(2, $this->spansCollection->count());
    }

    private function whenMappingTheSpansCollection()
    {
        $this->actualMap = $this->spansCollection->map(function () {
            return 1;
        });
    }

    private function thenTheMapIsTheExpected()
    {
        $expectedMap = [1, 1];
        $this->assertEquals($expectedMap, $this->actualMap);
    }
}
