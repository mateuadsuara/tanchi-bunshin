<?php
namespace Enhance;

include_once(__ROOT_DIR__ . "src/CellGenerators/UniquePURLGenerator.php");

define("SALUTATION", "1");
define("FIRSTNAME", "2");
define("SURTNAME", "3");
define("PURL", "4");

class TestUniquePURLGenerator extends TestFixture{

    public function setUp(){
    }

    public function tearDown(){
    }

    private function createGenerator($usedPurls = array()){
        return Core::getCodeCoverageWrapper("UniquePURLGenerator",
            array(FIRSTNAME, SURTNAME, SALUTATION, PURL, $usedPurls)
        );
    }

    private $testRow = array(
        "0" => "",
        SALUTATION => "Mr",
        FIRSTNAME => "Jamie",
        SURTNAME => "MacDow",
        PURL => "PURLGoingToBeOverwrited",
    );

    private $purlSuccession = array(
        "JamieMacDow",
        "JamieM",
        "JMacDow",
        "MrJamieMacDow",
        "Jamie-MacDow",
        "Jamie-M",
        "J-MacDow",
        "MrJamieM",
        "MrJMacDow",
        "MrJamie-MacDow",
        "Mr-JamieMacDow",
        "Mr-Jamie-MacDow",
        "MrJamie-M",
        "Mr-JamieM",
        "Mr-Jamie-M",
        "MrJ-MacDow",
        "Mr-JMacDow",
        "Mr-J-MacDow",
        "MacDowJamie",
        "MacDowJ",
        "MacDow-Jamie",
        "MacDow-J",
        "MrMacDowJamie",
        "MrMacDow-Jamie",
        "Mr-MacDowJamie",
        "Mr-MacDow-Jamie",
        "MrMacDowJ",
        "Mr-MacDowJ",
        "MrMacDow-J",
        "Mr-MacDow-J",
        "JamieMacDowMr",
        "JamieMMr",
        "JMacDowMr",
        "Jamie-MacDow-Mr",
        "Jamie-M-Mr",
        "J-MacDow-Mr",
        "JamieMacDow-Mr",
        "MacDowJamieMr",
        "MacDow-Jamie-Mr",
        "MacDowJamie-Mr",
        "MacDowJMr",
        "MacDowJ-Mr",
        "MacDow-J-Mr",

        "JamieMacDow-1",
        "JamieM-1",
        "JMacDow-1",
        "MrJamieMacDow-1",
        "Jamie-MacDow-1",
        "Jamie-M-1",
        "J-MacDow-1",
        "MrJamieM-1",
        "MrJMacDow-1",
        "MrJamie-MacDow-1",

        "Mr-JamieMacDow-1",
        "Mr-Jamie-MacDow-1",
        "MrJamie-M-1",
        "Mr-JamieM-1",
        "Mr-Jamie-M-1",
        "MrJ-MacDow-1",
        "Mr-JMacDow-1",
        "Mr-J-MacDow-1",

        "MacDowJamie-1",
        "MacDowJ-1",
        "MacDow-Jamie-1",
        "MacDow-J-1",
    );

    private $purlSuccessionWithoutSalutation = array(
        "JamieMacDow",
        "JamieM",
        "JMacDow",
        "Jamie-MacDow",
        "Jamie-M",
        "J-MacDow",
        "MacDowJamie",
        "MacDowJ",
        "MacDow-Jamie",
        "MacDow-J",

        "JamieMacDow-1",
        "JamieM-1",
        "JMacDow-1",
        "Jamie-MacDow-1",
        "Jamie-M-1",
        "J-MacDow-1",

        "MacDowJamie-1",
        "MacDowJ-1",
        "MacDow-Jamie-1",
        "MacDow-J-1",
    );

    function testGenerateOneCell(){
        $expected = array(
            "0" => "",
            SALUTATION => "Mr",
            FIRSTNAME => "Jamie",
            SURTNAME => "MacDow",
            PURL => "JamieMacDow",
        );

        $generator = $this->createGenerator();
        Assert::areIdentical($expected, $generator->applyTo($this->testRow));
    }

    function testFirstCombinationUsed(){
        $expected = array(
            "0" => "",
            SALUTATION => "Mr",
            FIRSTNAME => "Jamie",
            SURTNAME => "MacDow",
            PURL => "JamieM",
        );

        $generator = $this->createGenerator(array("JamieMacDow"));
        Assert::areIdentical($expected, $generator->applyTo($this->testRow));
    }

    private function assertSuccession($inputRow = array(), $purlSuccession = array()){
        for ($purlIndex = 0; $purlIndex < count($purlSuccession); $purlIndex++){
            $usedPurls = array_slice($purlSuccession, 0, $purlIndex);
            $generator = $this->createGenerator($usedPurls);

            $expectedPurl = $purlSuccession[$purlIndex];
            $generatedData= $generator->applyTo($inputRow);
            $actualPurl = $generatedData[PURL];

            Assert::areIdentical($expectedPurl, $actualPurl);
        }
    }

    function testSuccessionOfGeneratedPurls(){
        $this->assertSuccession($this->testRow, $this->purlSuccession);
    }

    private function assertExceptionWhenSuccessionEnds($inputRow = array(), $purlSuccession = array()){
        $generator = $this->createGenerator($purlSuccession);

        $exceptionTrown = false;

        try {
            $generator->applyTo($inputRow);
        }catch (\Exception $e){
            $exceptionTrown = true;
        }

        Assert::isTrue($exceptionTrown);
    }

    function testFailingToGeneratePurlThrowsAnException(){
        $this->assertExceptionWhenSuccessionEnds($this->testRow, $this->purlSuccession);
    }

    function testCleaningSurname(){
        $input = array(
            "0" => "",
            SALUTATION => "Mr",
            FIRSTNAME => "Jamie",
            SURTNAME => "Ma'c Dó-w",
            PURL => "PURLGoingToBeOverwrited",
        );

        $expected = array(
            "0" => "",
            SALUTATION => "Mr",
            FIRSTNAME => "Jamie",
            SURTNAME => "Ma'c Dó-w",
            PURL => "JamieMacDow",
        );

        $generator = $this->createGenerator();
        $actual = $generator->applyTo($input);
        Assert::areIdentical($expected, $actual);
    }

    function testCleaningFirstname(){
        $input = array(
            "0" => "",
            SALUTATION => "MR",
            FIRSTNAME => "Ja'mí-e Já-son",
            SURTNAME => "MacDow",
            PURL => "PURLGoingToBeOverwrited",
        );

        $expected = array(
            "0" => "",
            SALUTATION => "MR",
            FIRSTNAME => "Ja'mí-e Já-son",
            SURTNAME => "MacDow",
            PURL => "JamieMacDow",
        );

        $generator = $this->createGenerator();
        $actual = $generator->applyTo($input);
        Assert::areIdentical($expected, $actual);
    }

    function testCleaningSalutation(){
        $input = array(
            "0" => "",
            SALUTATION => "MR",
            FIRSTNAME => "Jamie",
            SURTNAME => "MacDow",
            PURL => "PURLGoingToBeOverwrited",
        );

        $expected = array(
            "0" => "",
            SALUTATION => "MR",
            FIRSTNAME => "Jamie",
            SURTNAME => "MacDow",
            PURL => "MrJamieMacDow",
        );

        $generator = $this->createGenerator(array("JamieMacDow", "JamieM", "JMacDow"));
        $actual = $generator->applyTo($input);
        Assert::areIdentical($expected, $actual);
    }

    function testHyphenSeparatedPurlWithHyphensInSurname(){
        $input = array(
            "0" => "",
            SALUTATION => "Mr",
            FIRSTNAME => "Jamie",
            SURTNAME => "Mac-Dow",
            PURL => "PURLGoingToBeOverwrited",
        );

        $expected = array(
            "0" => "",
            SALUTATION => "Mr",
            FIRSTNAME => "Jamie",
            SURTNAME => "Mac-Dow",
            PURL => "Jamie-MacDow",
        );

        $usedPurls = array("JamieMacDow", "JamieM", "JMacDow", "MrJamieMacDow", "MrJamieM", "MrJMacDow");

        $generator = $this->createGenerator($usedPurls);
        $actual = $generator->applyTo($input);
        Assert::areIdentical($expected, $actual);
    }


    private $testRowSalutationEmpty = array(
        "0" => "",
        SALUTATION => "",
        FIRSTNAME => "Jamie",
        SURTNAME => "Mac-Dow",
        PURL => "PURLGoingToBeOverwrited",
    );

    function testNotDefinedSalutation() {
        $this->assertSuccession($this->testRowSalutationEmpty, $this->purlSuccessionWithoutSalutation);
    }

    function testFailingToGeneratePurlWhenNoSalutationThrowsException(){
        $this->assertExceptionWhenSuccessionEnds($this->testRowSalutationEmpty, $this->purlSuccessionWithoutSalutation);
    }

    private $testRowNoSalutation = array(
        "0" => "",
        FIRSTNAME => "Jamie",
        SURTNAME => "Mac-Dow",
        PURL => "PURLGoingToBeOverwrited",
    );

    function testNoSalutationColumn(){
        $this->assertSuccession($this->testRowNoSalutation, $this->purlSuccessionWithoutSalutation);
    }

    function testPURLWithMixedCaseShouldMatchTheSameInLowecase(){
        $expected = array(
            "0" => "",
            SALUTATION => "Mr",
            FIRSTNAME => "Jamie",
            SURTNAME => "MacDow",
            PURL => "JamieM",
        );

        $generator = $this->createGenerator(array("JAmiEmacDow"));
        Assert::areIdentical($expected, $generator->applyTo($this->testRow));
    }
}