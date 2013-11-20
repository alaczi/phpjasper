<?php
namespace PolarBear\PhpJasperReports\Exceptions;

/**
 * Parameter conversion exception
 */
class ParameterConversionException extends \Exception {

    public function __construct($message = null, $code = 0, \Exception $previous = null) {
        if ($message === null) {
            $message = 'Exception during conversion of the parameter value to Java instance';
        }
        parent::__construct($message, $code, $previous);
    }
}