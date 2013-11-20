<?php

namespace PolarBear\PhpJasperReports;

use PolarBear\PhpJasperReports\Exceptions;

/**
 * ReportParameter
 *
 * Utility class to define the report parameters with the correpsonding Java types
 */
class ReportParameter {

    /**
     * The parameter value
     * @var mixed
     */
    private $value;

    /**
     * The key of the parameter (name)
     * @var string
     */
    private $key;

    /**
     * The java type of the parameter. See the REPORT_* constants
     * @var string
     */
    private $type;

    /**
     * Wrapper for creating a parameter
     *
     * @param string $key
     * @param mixed $value
     * @param string $type
     * @return \PolarBear\PhpJasperReports\ReportParameter
     */
    public static function getReportParameter($key, $value, $type) {
        $retval = new ReportParameter();
        $retval->key = $key;
        $retval->value = $value;
        $retval->type = $type;
        return $retval;
    }

    /**
     * Gets the value of the parameter
     * @return mixed
     */
    public function getValue() {
        return $this->value;
    }

    /**
     * Sets the value of the parameter
     * @param mixed $value
     * @return \PolarBear\PhpJasperReports\ReportParameter
     */
    public function setValue($value) {
        $this->value = $value;
        return $this;
    }

    /**
     * Gets the key of the parameter
     * @return string
     */
    public function getKey() {
        return $this->key;
    }
    /**
     * Sets the key of the parameter
     * @param string $key
     * @return \PolarBear\PhpJasperReports\ReportParameter
     */
    public function setKey($key) {
        $this->key = $key;
        return $this;
    }

    /**
     * Gets the type
     * @return string
     */
    public function getType() {
        return $this->type;
    }

    /**
     * Sets the type
     * @param string $type
     * @return \PolarBear\PhpJasperReports\ReportParameter
     */
    public function setType($type) {
        $this->type = $type;
        return $this;
    }

}
