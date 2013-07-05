<?php
namespace Enhance;

include_once(__ROOT_DIR__ . "src/Comparators/Filters/LowercaseFilter.php");

class TestLowercaseFilter extends TestFixture{

    public function setUp(){
    }

    public function tearDown(){
    }

    function testLowercasedOutput(){
        $filter = Core::getCodeCoverageWrapper("LowercaseFilter");
        $input = "Hello World!";
        $expected = "hello world!";
        Assert::areIdentical($expected, $filter->filter($input));
    }
}