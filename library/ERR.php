<?php

namespace MH17CDN;

class ERR {

  public $RES;

  /**
   * ERR constructor.
   */
  function __construct($RES)
  {
    $this->RES = $RES;
    set_error_handler([$this, 'errorHandler']);
    set_exception_handler([$this, 'exceptionHandler']);
    register_shutdown_function([$this, 'shutDownHandler']);
  }

  /**
   * handler for set_error_handler
   *
   * @param $errNo
   * @param $errStr
   * @param $errFile
   * @param $errLine
   * @return void
   */
  public function errorHandler($errNo, $errStr, $errFile, $errLine): void
  {
    ERR::log([
      'type' => 'error',
      'message' => $errStr,
      'file' => $errFile,
      'line' => $errLine
    ]);
  }

  /**
   * handler for set_exception_handler
   *
   * @param $exception
   * @return void
   */
  public function exceptionHandler($exception): void
  {
    ERR::log([
      'type' => 'exception',
      'message' => $exception->getMessage(),
      'file' => $exception->getFile(),
      'line' => $exception->getLine(),
      'trace' => $exception->getTraceAsString()
    ]);
  }

  /**
   * handler for register_shutdown_function
   *
   * @return void
   */
  public function shutDownHandler(): void
  {
    if (!is_null($error = error_get_last())) {
      ERR::log([
        'type' => 'fatal',
        'message' => $error['message'],
        'file' => $error['file'],
        'line' => $error['line']
      ]);
    }
  }

  /**
   * log the error to the log file
   * and send error response
   *
   * @param array $data
   * @return void
   */
  public function log(array $data): void
  {
    $timestamp = date('yW');
    $date = date('r');
    $file = WWW_ROOT . 'log' . DS . "error.{$timestamp}.json";
    $contents = file_exists($file)
      ? json_decode(file_get_contents($file))
      : [];

    $contents[] = array_merge(['date' => $date], $data);

    file_put_contents($file, json_encode($contents));

    $this->RES->send(500);
  }

}
