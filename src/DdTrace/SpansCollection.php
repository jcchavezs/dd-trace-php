<?php

namespace DdTrace;

use Iterator;

final class SpansCollection implements Iterator
{
    private $items;
    private $pointer = 0;

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
        return $this->pointer;
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
        $this->items[] = $span;
    }

    public function count()
    {
        return count($this->items);
    }

    public function map(callable $callback)
    {
        return array_map($callback, $this->items);
    }
}
