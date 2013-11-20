<?php
namespace PolarBear\PhpJasperReports;

/**
 * ReportParameterTypes class
 *
 * Just holds the valid types of the ReportParameter to be performance wise
 */
final class ReportParameterType {

    /**
     * java.lang.String
     */
    const REPORT_PARAM_STRING = "java.lang.String";
    /**
     * java.lang.Integer
     */
    const REPORT_PARAM_INTEGER = "java.lang.Integer";
    /**
     * java.lang.Long
     */
    const REPORT_PARAM_LONG = "java.lang.Long";
    /**
     * java.lang.Boolean
     */
    const REPORT_PARAM_BOOLEAN = "java.lang.Boolean";
    /**
     * java.lang.Short
     */
    const REPORT_PARAM_SHORT = "java.lang.Short";
    /**
     * java.lang.Double
     */
    const REPORT_PARAM_DOUBLE = "java.lang.Double";
    /**
     * java.math.BigDecimal
     */
    const REPORT_PARAM_BIGDECIMAL = "java.math.BigDecimal";
    /**
     * java.util.Date
     */
    const REPORT_PARAM_DATE = "java.util.Date";
    /**
     * net.sf.jasperreports.engine.JasperReport
     */
    const REPORT_PARAM_REPORT = "net.sf.jasperreports.engine.JasperReport";
    /**
     * net.sf.jasperreports.engine.JRDataSource
     */
    const REPORT_PARAM_DATASOURCE = "net.sf.jasperreports.engine.JRDataSource";

}
