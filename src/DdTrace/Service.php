<?php

namespace DdTrace;

final class Service
{
    private $service;
    private $app;
    private $appType;

    private function __construct($service, $app, $appType)
    {
        $this->service = $service;
        $this->app = $app;
        $this->appType = $appType;
    }

    public static function create($service, $app, $appType)
    {
        return new self($service, $app, $appType);
    }

    public function isEqual(Service $service)
    {
        return
            $this->service = $service->service
            && $this->app = $service->app
            && $this->appType = $service->appType;
    }
}
