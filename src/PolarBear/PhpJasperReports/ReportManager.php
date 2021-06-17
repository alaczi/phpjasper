<?php
namespace PolarBear\PhpJasperReports;

use PolarBear\PhpJasperReports\Exceptions\ExcelOverflowException;
use PolarBear\PhpJasperReports\Exceptions\NoJavaException;
use PolarBear\PhpJasperReports\Exceptions\NotSupportedFormatException;
use PolarBear\PhpJasperReports\Exceptions\ReportException;
use PolarBear\PhpJasperReports\Exceptions\ParameterConversionException;

/*
 * Just autoloads this libs classes if composer classloader is not present
 * @TODO: maybe add Zend autoloader check as well?
 */
if (!class_exists('Composer\\Autoload\\ClassLoader', false)){
    // autoload all interfaces & classes
    spl_autoload_register(function($class_name){
        if($class_name != 'ReportManager' && strpos($class_name, __NAMESPACE__) === 0)
            require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.str_replace('\\', DIRECTORY_SEPARATOR, substr($class_name, strlen(__NAMESPACE__))).'.php');
    });
}

/**
 * Reportmanager to run the reports with the help of JasperReports and \JavaBridge
 *
 * \JavaBridge url usually on the same machine:
 * http://127.0.0.1:8081/JavaBridge/java/Java.inc
 *
 */
class ReportManager {

    /**
     * Inner flag to monitor if java extension is available or not
     * @var boolean
     */
    private $isJava = false;
    /**
     * The url of javabridge include
     * @var string
     */
    private $javaBridgeUrl;

    /**
     * The url to write temporary files
     * @var string
     */
    private $tmpDir = '/tmp/';

    /**
     * Pdf output format
     */
    const REPORT_OUTPUT_PDF = "pdf";
    /**
     * HTML output format
     */
    const REPORT_OUTPUT_HTML = "html";
    /**
     * XLS output format
     */
    const REPORT_OUTPUT_XLS = "xls";
    /**
     * XSLX output format
     */
    const REPORT_OUTPUT_XLSX = "xlsx";
    /**
     * Rtf output format
     */
    const REPORT_OUTPUT_RTF = "rtf";
    /**
     * Flash output format
     */
    const REPORT_OUTPUT_FLASH = "flash";
    /**
     * CSV output format
     */
    const REPORT_OUTPUT_CSV = "csv";

    private static $_supportedFormatsAndMimeTypes = array(
        self::REPORT_OUTPUT_PDF => "application/pdf",
        self::REPORT_OUTPUT_HTML => "application/html",
        self::REPORT_OUTPUT_XLS => "application/xls",
        self::REPORT_OUTPUT_XLSX => "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
        self::REPORT_OUTPUT_RTF => "application/rtf",
        self::REPORT_OUTPUT_FLASH => "application/x-shockwave-flash",
        self::REPORT_OUTPUT_CSV => "text/csv"
    );

    /**
     * Constructor
     * @param string $javaBridgeUrl url for JavaBridge include
     * @param string $tmpDir temporary dir
     */
    public function __construct($javaBridgeUrl = null, $tmpDir = null) {
        $this->javaBridgeUrl = $javaBridgeUrl;
        if ($tmpDir !== null) {
            $this->tmpDir = $tmpDir;
        }
    }

    /**
     * Init the report manager, load the java extension from javabridge
     */
    public function init() {
        if (!extension_loaded('java')) {
            if ($this->javaBridgeUrl !== null){
                require_once($this->javaBridgeUrl);
            } else {
                throw new NoJavaException();
            }
        }
        if (function_exists("java_get_server_name")) {
            $this->isJava = true;
        } else {
            throw new NoJavaException();
        }
    }

    /**
     * Check if the given format is supported
     * @param string $format the format to check
     * @return boolean true if supported
     */
    public static function isSupportedFormat($format) {
        return in_array($format, array_keys(self::$_supportedFormatsAndMimeTypes));
    }

    /**
     * Returns the mime of the given format
     * @param string $formatString the format to check
     * @return string MIME type
     */
    public static function getMimeTypeForFormat($format) {
        if (isset(self::$_supportedFormatsAndMimeTypes[$format])) {
            return self::$_supportedFormatsAndMimeTypes[$format];
        }
        return null;
    }

    /**
     * Runs a report with the given parameters and datasource
     * @param string $reportName File name of the report
     * @param string $targetFile Target output file or "memory" to return the generated file content
     * @param string $targetFormat Target format see REPORT_OUTPUT_* constants
     * @param array $reportParams Array of report parameters. The elements should be instances of ReportParameter class
     * @param array $dataSource The data source of the report. @see createDatasource()
     * @param boolean $keepInCache Keep the compiled report in the memory?
     * @param boolean $forceCompile Forces the compilation of the report file
     * @param string $encoding Encoding to pass to the jasper exporter
     * @return string if $targetFile == 'memory' then the content of the generated pdf else the url of the file
     * @TODO add the possibility to pass the exporter parameters
     */
    public function runReport($reportName, $targetFile, $targetFormat, $reportParams, $dataSource, $keepInCache, $forceCompile, $encoding='UTF-8') {
        $this->init();
        $result = array();

        if (!$this->isSupportedFormat($targetFormat)){
            throw new NotSupportedFormatException(sprintf('Output format "%s" is not supported.', $targetFormat));
        }
        if ($targetFormat == ReportManager::REPORT_OUTPUT_XLS && sizeof($dataSource) > 65000) {
            throw new ExcelOverflowException();
        }
        try {
            $report = $this->compileReport($reportName, $keepInCache, $forceCompile);
            $params = $this->createParameterHashMap($reportParams);
            $ds = $this->createDatasource($dataSource);
            $fillManager = new \JavaClass("net.sf.jasperreports.engine.JasperFillManager");
            $jasperPrint = $fillManager->fillReport($report, $params, $ds);
            $reportParameter = new \JavaClass('net.sf.jasperreports.engine.JRExporterParameter');
            switch ($targetFormat) {
                case ReportManager::REPORT_OUTPUT_PDF:
                    $exporter = new \Java("net.sf.jasperreports.engine.export.JRPdfExporter");
//                    $pdfReportParameter = new \JavaClass('net.sf.jasperreports.engine.export.JRPdfExporterParameter');
//                    $exporter->setParameter($pdfReportParameter->PDF_VERSION, $pdfReportParameter->PDF_VERSION_1_7);
                    break;
                case ReportManager::REPORT_OUTPUT_RTF:
                    $exporter = new \Java("net.sf.jasperreports.engine.export.JRRtfExporter");
                    break;
                case ReportManager::REPORT_OUTPUT_XLS:
                    $exporter = new \Java("net.sf.jasperreports.engine.export.JRXlsExporter");
                    $excelReportParameter = new \JavaClass('net.sf.jasperreports.engine.export.JRXlsAbstractExporterParameter');
                    $exporter->setParameter($excelReportParameter->IS_DETECT_CELL_TYPE, true);
                    $exporter->setParameter($excelReportParameter->IS_WHITE_PAGE_BACKGROUND, false);
                    $exporter->setParameter($excelReportParameter->IS_REMOVE_EMPTY_SPACE_BETWEEN_COLUMNS, true);
                    $exporter->setParameter($excelReportParameter->IS_REMOVE_EMPTY_SPACE_BETWEEN_ROWS, true);
//                    $exporter->setParameter($excelReportParameter->IS_FONT_SIZE_FIX_ENABLED , true );
                    break;
                case ReportManager::REPORT_OUTPUT_XLSX:
                    $exporter = new \Java("net.sf.jasperreports.engine.export.ooxml.JRXlsxExporter");
                    $excelReportParameter = new \JavaClass('net.sf.jasperreports.engine.export.JRXlsAbstractExporterParameter');
                    $exporter->setParameter($excelReportParameter->IS_DETECT_CELL_TYPE, true);
                    $exporter->setParameter($excelReportParameter->IS_WHITE_PAGE_BACKGROUND, false);
                    $exporter->setParameter($excelReportParameter->IS_REMOVE_EMPTY_SPACE_BETWEEN_COLUMNS, true);
                    $exporter->setParameter($excelReportParameter->IS_REMOVE_EMPTY_SPACE_BETWEEN_ROWS, true);
//                    $exporter->setParameter($excelReportParameter->IS_FONT_SIZE_FIX_ENABLED , true );
                    break;
                case ReportManager::REPORT_OUTPUT_CSV:
                    $exporter = new \Java("net.sf.jasperreports.engine.export.JRCsvExporter");
                    break;
                case ReportManager::REPORT_OUTPUT_HTML:
                    $exporter = new \Java("net.sf.jasperreports.engine.export.HtmlExporter");
                    break;
                case ReportManager::REPORT_OUTPUT_FLASH:
//                    $exporter = new \Java("net.sf.jasperreports.engine.export.JRFlashExporter");
//                    break;
                default:
                    throw new NotSupportedFormatException(sprintf('Output format "%s" is not supported.', $targetFormat));
            }
            $exporter->setParameter($reportParameter->JASPER_PRINT, $jasperPrint);

            if ($targetFile == 'memory') {
//                $objStream = new \Java("java.io.ByteArrayOutputStream");
//                $exporter->setParameter($reportParameter->OUTPUT_STREAM, $objStream );
                $outputPath = $this->tmpDir . uniqid('jasper-') . ".tmp";
                $exporter->setParameter($reportParameter->OUTPUT_FILE_NAME, $outputPath);
            } else {
                $outputPath = $this->advrealpath($targetFile);
                $exporter->setParameter($reportParameter->OUTPUT_FILE_NAME, $outputPath);
            }
            if ($encoding != '') {
                $exporter->setParameter($reportParameter->CHARACTER_ENCODING, $encoding);
            }
            $exporter->exportReport();

            if ($targetFile == 'memory') {
                $result = implode(file($outputPath));
                unlink($outputPath);
            } else {
                $result = $outputPath;
            }
            return $result;
        } catch (\JavaException $e) {
            $trace = new \Java("java.io.ByteArrayOutputStream");
            $e->printStackTrace(new \Java("java.io.PrintStream", $trace));
            throw new ReportException(sprintf('Java exception during the report execution.\n%s', $trace));
        } catch (\Exception $e) {
            throw new ReportException('Exception during the report execution. \n'. $e->getMessage(), 0, $e);
        }
        return $result;
    }

    /**
     * Compiles a report or gets it from the cache
     * @param string $reportName File url of the report file (jrxml)
     * @param boolean $keepInCache Keep the complied report object in the cache
     * @param boolean $forceCompile Force the compilation of the report even if there is an instance in the cache
     * @return the compiled java instance of the report
     * @TODO: implement proper cache handling as there is a timeout in javabridge, it will drop the instance of the object after a while
     */
    public function compileReport($reportName, $keepInCache = false, $forceCompile = false) {
        $this->init();

        try {
            $report = null;
//            $report = $this->getCache()->get($reportName);
            if ($report == null || $report == '' || is_null($report) || $forceCompile) {
                $compileManager = new \JavaClass("net.sf.jasperreports.engine.JasperCompileManager");
                $reportPath = $this->advrealpath($reportName);
                $report = $compileManager->compileReport($reportPath);
            }
//            if ($keepInCache) {
//                $this->getCache()->set($reportName, $report);
//            } else {
//                $this->getCache()->delete($reportName);
//            }
            return $report;
        } catch (\JavaException $e) {
            $trace = new \Java("java.io.ByteArrayOutputStream");
            $e->printStackTrace(new \Java("java.io.PrintStream", $trace));
            throw new ReportException(sprintf('Unable to compile report: %s\n%s', $reportName, $trace->toString()));
        }
    }

    /**
     * Creates a \Java hashmap for passing it as parameter to JasperReports
     * @param array $reportParams Array of ReportParameter object instances
     * @return Object the java hashmap of the report parameters
     */
    public function createParameterHashMap($reportParams) {
        $this->init();
        try {
            $hashmap = new \Java("java.util.HashMap");
            if (sizeof($reportParams) > 0) {
                foreach ($reportParams as $param) {
                    $javaValue = $this->convertValue($param->getValue(), $param->getType());
                    $hashmap->put($param->getKey(), $javaValue);
                }
            }
            return $hashmap;
        } catch (\JavaException $e) {
            $trace = new \Java("java.io.ByteArrayOutputStream");
            $e->printStackTrace(new \Java("java.io.PrintStream", $trace));
            throw new ReportException(sprintf('Java exception during the parameter hashmap creation.\n%s', $trace));
        } catch (\Exception $e) {
            throw new ReportException('Exception during the parameter hashmap creation.', 0, $e);
        }
    }

    /**
     * Creates a new DataSource from the given associative array
     * @param array $dataSource The data source array. This is a 2 dimensional array, each row represents a row on the report.
     * Each row is an associative array of column elements as ReportParameter instances
     */
    public function createDatasource($dataSource) {
        $this->init();
        if (!$this->isJava) {
            throw new NoJavaException();
        }
        try {
            if (sizeof($dataSource) == 0) {
                return new \Java("net.sf.jasperreports.engine.JREmptyDataSource");
            } else {
                $vector = new \Java("java.util.Vector");
                foreach ($dataSource as $row) {
                    $hashmap = new \Java("java.util.HashMap");
                    if (sizeof($row) > 0) {
                        foreach ($row as $item) {
                            $javaValue = $this->convertValue($item->getValue(), $item->getType());
                            $hashmap->put($item->getKey(), $javaValue);
                        }
                        $vector->add($hashmap);
                    }
                }
                return new \Java("net.sf.jasperreports.engine.data.JRMapCollectionDataSource", $vector);
            }
        } catch (\JavaException $e) {
            $trace = new \Java("java.io.ByteArrayOutputStream");
            $e->printStackTrace(new \Java("java.io.PrintStream", $trace));
            throw new ReportException(sprintf('Java exception during the datasource creation.\n%s', $trace));
        } catch (\Exception $e) {
            throw new ReportException('Exception during the datasource creation.', 0, $e);
        }
    }

    /**
     * Converts the php values to the corresponding \Java class instances
     * @param mixed $value Value
     * @param string $className java class name
     * @returns the java class instance
     * @throws Exception if the conversion was unsuccessful
     */
    public function convertValue($value, $className) {
        $this->init();

        try {
            if (is_null($value) || $value == '') {
                return null;
            }
            switch ($className) {
                case ReportParameterType::REPORT_PARAM_STRING:
                case ReportParameterType::REPORT_PARAM_BOOLEAN:
                case ReportParameterType::REPORT_PARAM_INTEGER:
                case ReportParameterType::REPORT_PARAM_LONG:
                case ReportParameterType::REPORT_PARAM_SHORT:
                case ReportParameterType::REPORT_PARAM_DOUBLE:
                case ReportParameterType::REPORT_PARAM_BIGDECIMAL:
                    return new \Java($className, $value);
                case ReportParameterType::REPORT_PARAM_DATE:
                    if ($value instanceof \DateTime || $value instanceof \Date) {
                        $value = $value->getTimestamp();
                    }
//                    $value = $this->convertValue($value, ReportParameter::REPORT_PARAM_STRING);
//                    $temp = new \Java("java.text.DateFormat");
//                    $javaObject = $temp->parse($value);
                    $value = $this->convertValue(bcmul($value, '1000'), ReportParameterType::REPORT_PARAM_BIGDECIMAL);
                    return new \Java($className, $value->longValue());
                case ReportParameterType::REPORT_PARAM_DATASOURCE:
                case ReportParameterType::REPORT_PARAM_REPORT:
                    // This case, the ReportParameter should contain the proper \Java instance as value!
                    return $value;
                default:
                    throw new ParameterConversionException(sprintf('%s class is not supported', $className));
            }
        } catch (\JavaException $e) {
            $trace = new \Java("java.io.ByteArrayOutputStream");
            $e->printStackTrace(new \Java("java.io.PrintStream", $trace));
            throw new ParameterConversionException(sprintf('Java exception during conversion of parameter %s\n%s', $className, $trace));
        } catch (\Exception $e) {
            throw new ParameterConversionException(sprintf('Exception during conversion of parameter %s\n', $className), 0, $e);
        }
    }

    /**
     * Advanced realpath procedure for local files
     * @param string $file File or dir path
     * @returns string the real file path
     */
    public static function advrealpath($file) {
        if (substr($file, 0, 1) != "/") {
            $file = realpath(".") . "/" . $file;
        } else {
            $file = realpath($file);
        }
        return $file;
    }

}
