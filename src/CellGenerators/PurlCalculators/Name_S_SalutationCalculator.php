<?php

include_once("BasePurlCalculator.php");

class Name_S_SalutationCalculator extends BasePurlCalculator{
    function calculate($row){
        return $this->getFirstName($row) . "-" . $this->getShortSurname($row) . $this->getSalutationHyphenatedEnding($row);
    }
}