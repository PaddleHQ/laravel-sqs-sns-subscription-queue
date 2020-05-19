<?php

namespace Joblocal\LaravelSqsSnsSubscriptionQueue\Exception;

use Throwable;

class JsonDecodeException extends \Exception
{
    public function __construct($jsonPayload, $message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct(sprintf('%s - payload: %s', $message, $jsonPayload), $code, $previous);
    }
}
