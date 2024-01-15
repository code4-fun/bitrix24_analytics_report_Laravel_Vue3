<?php

namespace App\Exceptions;

class ReportException extends ApplicationException
{
  public function __construct(
    $message = 'An error occurred while generating the analytics report',
    $code = 500,
    $errorType = 'report_error')
  {
    parent::__construct($message, $code, $errorType);
  }
}
