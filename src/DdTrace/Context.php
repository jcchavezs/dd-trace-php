<?php

namespace DdTrace;

use TracingContext\TracingContext;

final class Context
{
    const DATA_DOG_TRACE_SPAN = 'datadog_trace_span';

    /**
     * SpanFromContext returns the stored *Span from the Context if it's available.
     * This helper returns also the ok value that is true if the span is present.
     *
     * @return Span
     */
    public static function spanFromContext(TracingContext $context)
    {
        return $context->value(self::DATA_DOG_TRACE_SPAN);
    }
}
