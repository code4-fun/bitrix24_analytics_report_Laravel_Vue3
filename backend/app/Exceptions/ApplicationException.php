<?php

namespace App\Exceptions;

use Exception;

class ApplicationException extends Exception
{
  protected string $errorType;

  public function __construct($message, $code = 500, $errorType = null)
  {
    parent::__construct($message, $code);
    $this->errorType = $errorType;
  }

  public function getErrorType()
  {
    return $this->errorType;
  }
}
