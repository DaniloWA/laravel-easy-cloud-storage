<?php

namespace Danilowa\LaravelEasyCloudStorage;

use Danilowa\LaravelEasyCloudStorage\Facades\EasyStorage;

class CustomMethod
{
    protected string $method;
    protected array $parameters = [];
    protected string $disk;
    protected bool $logErrors;
    protected bool $throwErrors;

    public function __construct(string $method, ?string $disk = null)
    {
        $this->method = $method;
        $this->disk = $disk ?: config('easycloudstorage.default');
        $this->logErrors = config('easycloudstorage.log_errors', true);
        $this->throwErrors = config('easycloudstorage.throw_errors', true);
    }

    public function withParameters(array $parameters): self
    {
        $this->parameters = $parameters;
        return $this;
    }

    public function execute(): mixed
    {
        $easyStorage = new EasyStorage($this->disk);
        $easyStorage->withLog($this->logErrors);
        $easyStorage->withError($this->throwErrors);
        
         return $easyStorage->executeMethod($this->method, $this->parameters, $this->disk);
    }

}
