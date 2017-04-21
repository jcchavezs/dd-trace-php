<?php

namespace DdTrace\Transports;

use DdTrace\Encoder;
use DdTrace\EncoderFactory;
use DdTrace\TracesBuffer;
use DdTrace\Transport;
use Exception;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Psr\Log\LoggerInterface;
use Throwable;

class Http implements Transport
{
    const DEFAULT_SERVICE_URL = 'localhost:8126';
    const STATUS_CODE_UNSUPPORTED_MEDIA_TYPE = 415;
    const STATUS_CODE_NOT_FOUND = 404;
    const STATUS_CODE_SERVER_ERROR = 500;

    private $client;
    private $logger;
    private $encoderFactory;
    private $traceUrl;
    private $serviceUrl;
    private $headers = [];

    public function __construct(
        ClientInterface $client,
        LoggerInterface $logger,
        EncoderFactory $encoderFactory,
        $serviceUrl = self::DEFAULT_SERVICE_URL
    ){
        $this->client = $client;
        $this->logger = $logger;
        $this->encoderFactory = $encoderFactory;
        $this->traceUrl = sprintf("http://%s/v0.3/traces", $serviceUrl);
        $this->serviceUrl = sprintf("http://%s/v0.3/services", $serviceUrl);
    }

    public function sendTraces(TracesBuffer $traces)
    {
        $response = null;

        try {
            $encoder = $this->encoderFactory->build();

            $encoder->encodeTraces($traces);

            $headers = $this->headers + ["Content-Type" => $encoder->contentType()];

            $request = new Request("POST", $this->traceUrl, $headers, $encoder->read());

            $response = $this->client->send($request);

            $responseStatusCode = $response->getStatusCode();

            if (
                $responseStatusCode == self::STATUS_CODE_NOT_FOUND
                || $responseStatusCode == self::STATUS_CODE_UNSUPPORTED_MEDIA_TYPE
            ) {
                /** @TODO: Downgrade for API missing. */
                $this->logger->error("calling the endpoint '%s' but received %d.", $this->traceUrl, $responseStatusCode);
            }

        } catch (BadResponseException $e) {
            $response = $e->getResponse();
        } catch (Throwable $e) {
            $response = new Response(self::STATUS_CODE_SERVER_ERROR, $e->getMessage());
        } finally {
            return $response;
        }
    }

    public function sendServices(array $services)
    {
        try {
            $encoder = $this->encoderFactory->build();

            $encoder->encodeServices($services);

            $headers = $this->headers + ["Content-Type" => $encoder->contentType()];

            $request = new Request("POST", $this->serviceUrl, $headers, $encoder->read());

            $response = $this->client->send($request);

            $responseStatusCode = $response->getStatusCode();

            if (
                $responseStatusCode == self::STATUS_CODE_NOT_FOUND
                || $responseStatusCode == self::STATUS_CODE_UNSUPPORTED_MEDIA_TYPE
            ) {
                /** @TODO: Downgrade for API missing. */
                $this->logger->error("calling the endpoint '%s' but received %d.", $this->traceUrl, $responseStatusCode);
            }
        } catch (BadResponseException $e) {
            $response = $e->getResponse();
        } catch (Exception $e) {
            $response = new Response(self::STATUS_CODE_SERVER_ERROR, $e->getMessage());
        } finally {
            return $response;
        }
    }

    public function setHeader($key, $value)
    {
        $this->headers[$key] = $value;
    }
}
