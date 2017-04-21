<?php

namespace DdTrace\Encoders;

use DdTrace\Span;
use DdTrace\SpansCollection;
use DdTrace\TracesBuffer;
use Iterator;

class TracesFormatter
{
    /** @return Iterator */
    public function __invoke(TracesBuffer $tracesBuffer)
    {
        if ($tracesBuffer->count() === 0) {
            return [];
        }

        /** @var SpansCollection $spansCollection */
        foreach ($tracesBuffer as $spansCollection) {
            yield $spansCollection->map(function(Span $span) {
                return [
                        'trace_id' => $span->traceId(),
                        'span_id' => $span->spanId(),
                        'parent_id' => $span->parentId(),
                        'name' => $span->name(),
                        'resource' => $span->resource(),
                        'service' => $span->service(),
                        'type' => $span->type(),
                        'start' => $span->start()->nanotime(),
                        'duration' => $span->duration()->nanotime(),
                        'error' => $span->error()
                    ] + ($span->meta() ? ['meta' => $span->meta()] : []);
            });
        }
    }
}
