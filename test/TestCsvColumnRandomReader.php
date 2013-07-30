<?php
namespace Enhance;

include_once (__ROOT_DIR__ . "src/RandomReaders/CsvColumnRandomReader.php");

class TestCsvColumnRandomReader extends TestFixture{

    private $testDataCsv;

    public function setUp(){
        $this->testDataCsv =  __ROOT_DIR__ . 'test/sampleFiles/archivo.csv';
    }

    public function tearDown(){
    }

    private function createReader(){
        return Core::getCodeCoverageWrapper("CsvColumnRandomReader");
    }

    function testNotReadyOnCreate(){
        $reader = $this->createReader();
        Assert::isFalse($reader->isReady());
    }

    function testOpenNonExistingFile(){
        $reader = $this->createReader();
        $reader->open('');
        Assert::isFalse($reader->isReady());
    }

    function testReadEmptyFile(){
        $reader = $this->createReader();
        $reader->open(__ROOT_DIR__ . 'test/sampleFiles/test_empty_data.csv');
        Assert::areIdentical(0, $reader->getRowCount());
    }

    private function createTestReader(){
        $reader = $this->createReader();
        $reader->open($this->testDataCsv);

        return $reader;
    }

    function testOpenFile(){
        $reader = $this->createTestReader();
        Assert::isTrue($reader->isReady());
    }

    function testCountLines(){
        $reader = $this->createTestReader();
        $linesOfTheFile = 5;

        Assert::areIdentical($linesOfTheFile, $reader->getRowCount());
    }

    function testReadFirstRow(){
        $reader = $this->createTestReader();

        $expected = array(
            "ID" => "",
            "Company" => "Finchatton",
            "Salutation" => "",
            "Firstname" => "Adam",
            "Surname" => "Hunter",
            "PrintPURL" => "www.amayadesign.co.uk/AdamHunter",
            "Domain_name" => "www.amayadesign.co.uk/",
            "PURL" => "AdamHunter",
            "Active" => "Y",
            "Jobtitle" => "£�"
        );
        $current = $reader->readRow(0);

        Assert::areIdentical($expected, $current);
    }

    function testReadThirdRow(){
        $reader = $this->createTestReader();

        $expected = array(
            "ID" => "", "Company" => "タマ", "Salutation" => "いぬ", "Firstname" => "", "Surname" => "",
            "PrintPURL" => "", "Domain_name" => "",
            "PURL" => "", "Active" => "", "Jobtitle" => "£�"
        );
        $current = $reader->readRow(2);

        Assert::areIdentical($expected, $current);
    }

    function testJumpingForthToSecondRow(){
        $reader = $this->createTestReader();

        $expected = array(
            "ID" => "",
            "Company" => "Finchatton",
            "Salutation" => "",
            "Firstname" => "Adam",
            "Surname" => "Hunter",
            "PrintPURL" => "www.amayadesign.co.uk/AdamHunter",
            "Domain_name" => "www.amayadesign.co.uk/",
            "PURL" => "AdamHunter",
            "Active" => "Y",
            "Jobtitle" => "£�"
        );
        $current = $reader->readRow(3);

        Assert::areIdentical($expected, $current);

        $expected = array(
            "ID" => "", "Company" => "Luxlo", "Salutation" => "Property", "Firstname" => "Amit", "Surname" => "Chadha",
            "PrintPURL" => "www.amayadesign.co.uk/AmitChadha", "Domain_name" => "www.amayadesign.co.uk/",
            "PURL" => "AmitChadha", "Active" => "Y", "Jobtitle" => "£�"
        );
        $current = $reader->readRow(1);

        Assert::areIdentical($expected, $current);
    }
}