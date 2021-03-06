<?php
namespace Enhance;

include_once(__ROOT_DIR__ . "src/HashCalculators/Filters/FirstNameFilter.php");

class TestFirstNameFilter extends TestFixture{

    public function setUp(){
    }

    public function tearDown(){
    }

    private function createFilter(){
        return Core::getCodeCoverageWrapper("FirstNameFilter");
    }

    private function assertExpected($expected, $input){
        $filter = $this->createFilter();
        Assert::areIdentical($expected, $filter->applyTo($input));
    }

    function testCannotHaveAnySpaces(){
        $this->assertExpected("Marie", "Marie Charlotte");
    }

    function testStartWithACapitalLetter(){
        $this->assertExpected("Marie", "marie");
    }

    function testWhenAllUppercaseAfterTheFirstCapitalLetterMustBeAllLowercase(){
        $this->assertExpected("Marie", "MARIE");
    }

    function testFollowingLetterOfAHyphenCouldBeUpperOrLowercase(){
        $this->assertExpected("Marie-Charlotte", "Marie-Charlotte");
        $this->assertExpected("Marie-Charlotte", "Marie-charlotte");
    }

    function testAllTheLettersFollowingTheFirstLetterAfterTheHyphenMustBeLowercase(){
        $this->assertExpected("Marie-Charlotte", "Marie-CharLOtte");
        $this->assertExpected("Marie-Charlotte", "Marie-charLOtte");
    }

    function testAccentedCharactersAreAllowed(){
        $this->assertExpected("Márïe-Chàrlôtte", "Márïe-Chàrlôtte");
    }

    function testAccentedCharactersAreAllowedInTheFirstCapitalLetter(){
        $this->assertExpected("Ömárïe", "Ömárïe");
    }

    function testCompositeNamesShouldRemainUnaltered(){
        $this->assertExpected("MacDow", "MacDow");
    }
}