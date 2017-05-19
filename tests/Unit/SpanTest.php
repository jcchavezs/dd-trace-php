<?php

namespace DdTraceTests\Unit;

use DdTrace\Span;
use DdTraceTests\Builders\SpanBuilder;
use Exception;
use InvalidArgumentException;
use PHPUnit_Framework_TestCase;
use stdClass;

final class SpanTest extends PHPUnit_Framework_TestCase
{
    /** @var Span */
    private $span;
    private $error;

    public function testSetErrorFailsDueToInvalidError()
    {
        $this->givenAnSpan();
        $this->andAnInvalidError();
        $this->thenAnInvalidArgumentExceptionIsThrown();
        $this->whenSettingTheError();
    }

    public function testSetErrorSuccessWithAValidError()
    {
        $this->givenAnSpan();
        $this->andAValidError();
        $this->whenSettingTheError();
        $this->thenTheErrorIsSet();
    }

    private function givenAnSpan()
    {
        $this->span = SpanBuilder::create()->build();
    }

    private function andAnInvalidError()
    {
        $this->error = new stdClass();
    }

    private function andAValidError()
    {
        $this->error = new Exception();
    }

    private function thenAnInvalidArgumentExceptionIsThrown()
    {
        $this->expectException(InvalidArgumentException::class);
    }

    private function whenSettingTheError()
    {
        $this->span->setError($this->error);
    }

    private function thenTheErrorIsSet()
    {
        $this->assertTrue($this->span->hasError());
    }
}
