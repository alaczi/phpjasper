<?php
namespace PolarBear\PhpJasperReports\Exceptions;

/**
 * Report execution exception
 */
class ReportException extends \Exception {

    public function __construct($message = null, $code = 0, \Exception $previous = null) {
        if ($message === null) {
            $message = 'Exception occured during the report generation.';
        }
        parent::__construct($message, $code, $previous);
    }
}
