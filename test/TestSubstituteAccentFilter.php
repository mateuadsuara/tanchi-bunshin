<?php
namespace Enhance;

include_once(__ROOT_DIR__ . "src/Comparators/Filters/SubstituteAccentsFilter.php");

class TestSubstituteAccentsFilter extends TestFixture{

    public function setUp(){
    }

    public function tearDown(){
    }

    function testUnchangedNormalSymbols(){
        $filter = new \SubstituteAccentsFilter();
        $input = "abcdfghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ~¨`";
        Assert::areIdentical($input, $filter->filter($input));
    }

    function testSubstituteAccents(){
        $filter = new \SubstituteAccentsFilter();
        $input = "áàäâªÁÀÂÄdoéèëêÉÈÊËreíìïîÍÌÏÎmióòöôÓÒÖÔfaúùüûÚÙÛÜsolñÑçÇlasi";
        $expected = "aaaaaAAAAdoeeeeEEEEreiiiiIIIImiooooOOOOfauuuuUUUUsolnNcClasi";
        Assert::areIdentical($expected, $filter->filter($input));
    }
}