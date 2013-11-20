<?php
namespace PolarBear\PhpJasperReports\Exceptions;

/**
 * Invalid parameter type exception
 */
class InvalidParameterTypeException extends \Exception {

    public function __construct($message = null, $code = 0, \Exception $previous = null) {
        if ($message === null) {
            $message = 'Invalid parameter type for the parameter';
        }
        parent::__construct($message, $code, $previous);
    }
}
