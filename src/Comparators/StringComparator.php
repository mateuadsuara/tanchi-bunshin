<?php

include_once("Comparator.php");
include_once(__ROOT_DIR__ . "src/Comparators/Filters/Filter.php");

class StringComparator implements Comparator{
    private $filters = array();

    function addFilter(Filter $filter){
        $this->filters[] = $filter;
    }

    function areEqual($a, $b){
        return gettype($a) === 'string' && gettype($b) === 'string' &&
               $this->applyFilters($a) === $this->applyFilters($b);
    }

    private function applyFilters($text){
        $filteredText = $text;

        foreach ($this->filters as $filter){
            $filteredText = $filter->filter($filteredText);
        }

        return $filteredText;
    }
}