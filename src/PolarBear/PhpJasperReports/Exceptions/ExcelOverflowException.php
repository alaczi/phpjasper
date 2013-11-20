<?php
namespace PolarBear\PhpJasperReports\Exceptions;

/**
 * Excel overflow exception
 */
class ExcelOverflowException extends \Exception {

    public function __construct($message = null, $code = 0, \Exception $previous = null) {
        if ($message === null) {
            $message = 'Excel only support 65k rows.';
        }
        parent::__construct($message, $code, $previous);
    }
}
