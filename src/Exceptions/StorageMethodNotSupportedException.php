<?php

namespace Danilowa\LaravelEasyCloudStorage\Exceptions;

use Exception;

class StorageMethodNotSupportedException extends Exception
{
    public function __construct(string $message = "Storage method not supported")
    {
        parent::__construct($message);
    }
}
