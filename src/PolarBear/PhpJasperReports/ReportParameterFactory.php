<?php
namespace PolarBear\PhpJasperReports;

use PolarBear\PhpJasperReports\Exceptions\InvalidParameterTypeException;

/**
 * ReportParameterFactory
 *
 * Factory class create Report parameters
 */
class ReportParameterFactory {

    /**
     * Creates a string type report parameter
     * @param string $key Parameter key
     * @param string $value Value
     */
    public static function getString($key, $value) {
        return ReportParameter::getReportParameter($key, $value, ReportParameterType::REPORT_PARAM_STRING);
    }

    /**
     * Creates an Integer type report parameter
     * @param string $key Parameter key
     * @param integer $value Value
     */
    public static function getInteger($key, $value) {
        return ReportParameter::getReportParameter($key, $value, ReportParameterType::REPORT_PARAM_INTEGER);
    }

    /**
     * Creates a Long type report parameter
     * @param string $key Parameter key
     * @param integer $value Value
     */
    public static function getLong($key, $value) {
        return ReportParameter::getReportParameter($key, $value, ReportParameterType::REPORT_PARAM_LONG);
    }

    /**
     * Creates a Short type report parameter
     * @param string $key Parameter key
     * @param integer $value Value
     */
    public static function getShort($key, $value) {
        return ReportParameter::getReportParameter($key, $value, ReportParameterType::REPORT_PARAM_SHORT);
    }

    /**
     * Creates a Double type report parameter
     * @param string $key Parameter key
     * @param string $value Value
     */
    public static function getDouble($key, $value) {
        return ReportParameter::getReportParameter($key, $value, ReportParameterType::REPORT_PARAM_DOUBLE);
    }

    /**
     * Creates a Boolean type report parameter
     * @param string $key Parameter key
     * @param boolean $value Value
     */
    public static function getBoolean($key, $value) {
        return ReportParameter::getReportParameter($key, $value, ReportParameterType::REPORT_PARAM_BOOLEAN);
    }

    /**
     * Creates a BigDecimal type report parameter
     * @param string $key Parameter key
     * @param string $value Value
     */
    public static function getBigdecimal($key, $value) {
        return ReportParameter::getReportParameter($key, $value, ReportParameterType::REPORT_PARAM_BIGDECIMAL);
    }

    /**
     * Creates a Date type report parameter
     * @param string $key Parameter key
     * @param integer $value Value
     */
    public static function getDate($key, $value) {
        return ReportParameter::getReportParameter($key, $value, ReportParameterType::REPORT_PARAM_DATE);
    }

    /**
     * Creates a Report type report parameter ( to add subreports )
     * @param string $key Parameter key
     * @param string $value (Sub-report filename)
     */
    public static function getReport($key, $value) {
        return ReportParameter::getReportParameter($key, $value, ReportParameterType::REPORT_PARAM_REPORT);
    }

    /**
     * Creates a DataSource type report parameter
     * @param string $key Parameter key
     * @param Object $value Value to pass whole data sets to the report
     */
    public static function getDataSource($key, $value) {
        return ReportParameter::getReportParameter($key, $value, ReportParameterType::REPORT_PARAM_DATASOURCE);
    }

    /**
     * Wrapper for creating a parameter
     *
     * @param string $key
     * @param mixed $value
     * @param string $type
     * @return \PolarBear\PhpJasperReports\ReportParameter
     */
    public static function getReportParameter($key, $value, $type) {
        if (!in_array($type, self::getValidParameterTypes())){
            throw new InvalidParameterTypeException(sprintf('Invalid parameter type for the parameter %s', $type));
        }
        $param = new ReportParameter();
        $param->setKey($key);
        $param->setValue($value);
        $param->setType($type);
        return $param;
    }

    /**
     * Returns the valid parameter types (classnames) for a report parameter
     * @return array
     */
    public final static function getValidParameterTypes(){
        $reflection = new \ReflectionClass('\PolarBear\PhpJasperReports\ReportParameterType');
        $constants = $reflection->getConstants();
        $types = array();
        foreach ($constants as $name => $constant){
            if (strpos($name, 'REPORT_PARAM_') === 0) {
                $types[] = $constant;
            }
        }
        return $types;
    }

}
