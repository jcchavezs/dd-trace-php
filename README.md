# DataDog Trace PHP

[![Build Status](https://travis-ci.org/jcchavezs/dd-trace-php.svg?branch=master)](https://travis-ci.org/jcchavezs/dd-trace-php)

This library contains Datadog's tracing client. It is used to trace requests as they flow across web servers, databases and microservices so that developers have visibility into bottlenecks and troublesome requests.

Package tracer has two core objects: Tracers and Spans. Spans represent a chunk of computation time. They have names, durations, timestamps and other metadata. Tracers are used to create hierarchies of spans in a request, buffer and submit them to the server.

The tracing client can perform trace sampling. While the trace agent already samples traces to reduce bandwidth usage, client sampling reduces performance overhead.

## Installation

DD Trace can be installed by composer:

```sh
composer require jcchavezs/dd-trace
```

## Example

```php

use DdTrace\Tracer;
use GuzzleHttp\Exception\RequestException;

$tracer = Tracer::noop();
$client = new GuzzleHttp\Client();

$span = $tracer->createRootSpan("http.client.request", "example.com", "/user/{id}");

$url = "http://example.com/user/123";

try {
    $response = $client->get($url);

    $span->setMeta("http.status", $response->getStatusCode());
    $span->setMeta("http.url", $url);
} catch (RequestException $e) {
    $span->setError($e);
}

$span->finish();
```

## Unit testing

Run in the source folder:

```sh
make test
```