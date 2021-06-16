<?php
namespace PolarBear\PhpJasperReports\Tests;

use PolarBear\PhpJasperReports\ReportManager;
use PolarBear\PhpJasperReports\ReportParameterFactory;
use \PHPUnit\Framework\TestCase;

require_once dirname(__FILE__)."/../ReportManager.php";

/**
 * Unit tests for ReportManager
 *
 * @author Andras Laczi
 */
class ReportManagerTest extends TestCase {


    public function testReportManager() {
        $manager = new ReportManager('http://127.0.0.1:8081/JavaBridge/java/Java.inc');
        $this->assertNotNull($manager);
        return $manager;
    }

    /**
     * Test the printing of a pdf
     * @depends testReportManager
     */
    public function testPdf(ReportManager $manager) {
        $result = $manager->runReport("./Resources/testReports/test.jrxml", "memory", ReportManager::REPORT_OUTPUT_PDF, $this->dummyParams(), $this->dummyDataSource(), false, true, null);
        $this->assertStringStartsWith('%PDF', $result);
    }

    /**
     * Test for generating XLS
     * @depends testReportManager
     */
    public function testXls(ReportManager $manager) {
        $result = $manager->runReport("./Resources/testReports/test.jrxml", "memory", ReportManager::REPORT_OUTPUT_XLS, $this->dummyParams(), $this->dummyDataSource(), false, true, null);
        $this->assertNotEmpty($result);
    }

    /**
     * Test the generation of a html
     * @depends testReportManager
     */
    public function testHtml(ReportManager $manager) {
        $result = $manager->runReport("./Resources/testReports/test.jrxml", "memory", ReportManager::REPORT_OUTPUT_HTML, $this->dummyParams(), $this->dummyDataSource(), false, true, null);
        $this->assertStringStartsWith('<!DOCTYPE html', $result);
    }

    /**
     * Test the generation of a rtf
     * @depends testReportManager
     */
    public function testRtf(ReportManager $manager) {
        $result = $manager->runReport("./Resources/testReports/test.jrxml", "memory", ReportManager::REPORT_OUTPUT_RTF, $this->dummyParams(), $this->dummyDataSource(), false, true, null);
        $this->assertNotEmpty($result);
    }

    /**
     * Test the generation of a csv
     * @depends testReportManager
     */
    public function testCsv(ReportManager $manager) {
        $result = $manager->runReport("./Resources/testReports/test.jrxml", "memory", ReportManager::REPORT_OUTPUT_CSV, $this->dummyParams(), $this->dummyDataSource(), false, true, null);
        $this->assertNotEmpty($result);
    }

    /**
     * Simple parameter array for the test
     */
    private function dummyParams() {
        $params = array(ReportParameterFactory::getInteger("intparam", 123456789), ReportParameterFactory::getString("stringparam", "Hello World!"));
        return $params;
    }

    /**
     * Simple datasource for the report test
     */
    private function dummyDataSource() {
        $ds = array();
        $ds[] = array(ReportParameterFactory::getInteger("id", 1), ReportParameterFactory::getString("name", "Béla"), ReportParameterFactory::getString("color", "zöld"));
        $ds[] = array(ReportParameterFactory::getInteger("id", 2), ReportParameterFactory::getString("name", "Józsi"), ReportParameterFactory::getString("color", "kék"));
        $ds[] = array(ReportParameterFactory::getInteger("id", 3), ReportParameterFactory::getString("name", "Gyuri"), ReportParameterFactory::getString("color", "piros"));
        $ds[] = array(ReportParameterFactory::getInteger("id", 4), ReportParameterFactory::getString("name", "Dezső"), ReportParameterFactory::getString("color", "lila"));
        $ds[] = array(ReportParameterFactory::getInteger("id", 5), ReportParameterFactory::getString("name", "Ernő"), ReportParameterFactory::getString("color", "sárga"));
        $ds[] = array(ReportParameterFactory::getInteger("id", 6), ReportParameterFactory::getString("name", "Győző"), ReportParameterFactory::getString("color", "okker"));
        $ds[] = array(ReportParameterFactory::getInteger("id", 7), ReportParameterFactory::getString("name", "Balázs"), ReportParameterFactory::getString("color", "íáéóöőüű"));
        return $ds;
    }
}
