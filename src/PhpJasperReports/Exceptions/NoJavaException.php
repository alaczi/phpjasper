<?php
namespace PolarBear\PhpJasperReports\Exceptions;

/**
 * No java (javabridge) extension exception
 */
class NoJavaException extends \Exception {

    public function __construct($message = null, $code = 0, \Exception $previous = null) {
        if ($message === null) {
            $message = 'Javabridge functions not found, check your configuration.';
        }
        parent::__construct($message, $code, $previous);
    }
}
