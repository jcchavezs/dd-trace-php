<?php

namespace DdTraceTests\Unit;

use DdTrace\SpansCollection;
use DdTraceTests\Builders\SpanBuilder;
use PHPUnit_Framework_TestCase;

final class SpansCollectionTest extends PHPUnit_Framework_TestCase
{
    public function testAnSpanCollectionHasTheExpectedLength()
    {
        $spansCollection = new SpansCollection;
        $spansCollection->push(SpanBuilder::create()->build());
        $spansCollection->push(SpanBuilder::create()->build());
        $this->assertEquals(2, $spansCollection->count());
    }

    public function testAnSpanCollectionCanBeMapped()
    {
        $spansCollection = new SpansCollection;
        $spansCollection->push(SpanBuilder::create()->build());
        $spansCollection->push(SpanBuilder::create()->build());

        $expectedMap = [1, 1];

        $actualMap = $spansCollection->map(function() {
            return 1;
        });

        $this->assertEquals($expectedMap, $actualMap);
    }
}
