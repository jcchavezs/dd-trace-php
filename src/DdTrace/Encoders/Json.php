<?php

namespace DdTrace\Encoders;

use DdTrace\Encoder;
use DdTrace\TracesBuffer;
use DdTrace\Span;
use DdTrace\SpansCollection;

final class Json implements Encoder
{
    private $encodedContent;
    private $tracesFormatter;

    public function __construct(TracesFormatter $tracesFormatter)
    {
        $this->tracesFormatter = $tracesFormatter;
    }

    public function encodeTraces(TracesBuffer $traces)
    {
        $traceIds = []; 
        $traces->map(function(SpansCollection $spansCollection) use (&$traceIds) {
            $spansCollection->map(function(Span $span) use (&$traceIds) {
                $traceIds[$span->traceId()] = true;
                $traceIds[$span->spanId()] = true;
                $traceIds[$span->parentId()] = true;
            
            });
        });

        $this->encodedContent = $this->formatIds(json_encode($this->tracesFormatter->__invoke($traces)), $traceIds);
    }

    public function encodeServices(array $services)
    {
        $this->encodedContent = $this->formatIds(json_encode($services));
    }

    public function read()
    {
        return $this->encodedContent;
    }

    public function contentType()
    {
        return "application/json";
    }

    private function formatIds($encodedContext, $traceIds) {
         foreach(array_keys($traceIds) as $traceId) {
             if ($traceId === "") {
                $encodedContext = str_replace('"'.$traceId.'"', 0, $encodedContext);
             } else {
                $encodedContext = str_replace('"'.$traceId.'"', $traceId, $encodedContext);
             }
        }
        return $encodedContext;
    }
}
