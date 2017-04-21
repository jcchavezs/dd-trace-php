<?php

namespace DdTrace;

class Buffer
{
    /** @var SpansCollection */
    private $items;

    public function __construct()
    {
        $this->emptyItems();
    }

    public function push(Span $span)
    {
        $this->items->push($span);
    }

    public function length()
    {
        return $this->items->count();
    }

    public function pop()
    {
        $items = $this->items;
        $this->emptyItems();
        return $items;
    }

    private function emptyItems()
    {
        $this->items = new SpansCollection;
    }

}
