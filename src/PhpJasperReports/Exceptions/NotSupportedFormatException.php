<?php
namespace PolarBear\PhpJasperReports\Exceptions;

/**
 * Report output format not supported exception
 */
class NotSupportedFormatException extends \Exception {

    public function __construct($message = null, $code = 0, \Exception $previous = null) {
        if ($message === null) {
            $message = 'The given output format is not supported.';
        }
        parent::__construct($message, $code, $previous);
    }
}
