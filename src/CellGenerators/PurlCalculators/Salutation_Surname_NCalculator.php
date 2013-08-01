<?php

include_once("BasePurlCalculator.php");

class Salutation_Surname_NCalculator extends BasePurlCalculator{
    function calculate($row){
        $salutation = $row[$this->salutationField];
        if (!empty($salutation)) $salutation .= "-";

        return $salutation . $row[$this->surnameField] . "-" . substr($row[$this->firstnameField], 0, 1);
    }
}