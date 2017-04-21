<?php

namespace DdTrace;

use Iterator;

final class TracesBuffer implements Iterator
{
    private $items = [];
    private $keys = [];
    private $pointer = 0;

    public static function fromSpanCollection(SpansCollection $spans)
    {
        $self = new self();

        /** @var Span $span */
        foreach ($spans as $span) {
            $self->push($span);
        }

        return $self;
    }

    public function current()
    {
        return $this->items[$this->pointer];
    }

    public function next()
    {
        $this->pointer++;
    }

    public function key()
    {
        return $this->keys[$this->pointer];
    }

    public function valid()
    {
        return array_key_exists($this->pointer, $this->items);
    }

    public function rewind()
    {
        $this->pointer = 0;
    }

    public function push(Span $span)
    {
        if (!in_array($span->traceId(), $this->keys)) {
            $this->keys[] = $span->traceId();
            $this->items[] = new SpansCollection;
        }

        $this->items[count($this->items) - 1]->push($span);
    }

    public function count()
    {
        return count($this->keys);
    }
}
