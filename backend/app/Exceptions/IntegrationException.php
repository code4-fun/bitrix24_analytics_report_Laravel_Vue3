<?php

namespace App\Exceptions;

class IntegrationException extends ApplicationException
{
  public function __construct(
    $message = 'An error occurred while setting up integration',
    $code = 500,
    $errorType = 'integration_error')
  {
    parent::__construct($message, $code, $errorType);
  }
}
