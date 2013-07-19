<?php
define("__ROOT_DIR__", "../");

define("__DEDUP_DIR__", __ROOT_DIR__ . "deduplications/");
    define("__DEDUPS_DIRS__", "dedup*");
        define("__UNIQUES_FILE__", "uniques.*");
        define("__DUPLICATES_FOLDER__", "duplicates/");
        define("__INPUTS_FOLDER__", "input/");
        define("__IDENTIFYING_VALUES_FILE__", "identifyingValues.csv");

define("__FILTERS_DIR__", __ROOT_DIR__ . "src/HashCalculators/Filters/");

define("__VIEW_UNIQUES_FILE__", "uniques.php");
define("__VIEW_DEDUPS_FILE__", "deduplications.php");
define("__VIEW_DEDUP_FILE__", "dedup.php");
define("__VIEW_DUPS_GROUP_FILE__", "editDupsGroup.php");

include_once("HTML.php");
include_once(__ROOT_DIR__ . "src/RandomReaders/CsvRandomReader.php");

function getNotExistingDedupDirName(){
    $i = 0;
    while (is_dir(__DEDUP_DIR__ . "dedup$i")){
        $i++;
    }
    return __DEDUP_DIR__ . "dedup$i/";
}

function getViewDedupLink($dirToDedup){
    return  __VIEW_DEDUP_FILE__ . "?dir=" . $dirToDedup;
}

function getViewDupsGroupLink($file){
    return  __VIEW_DUPS_GROUP_FILE__ . "?dupsGroup=" . $file;
}

function getUniquesFile(){
    $uniques_file_match = $_REQUEST["dir"] . "/" . __UNIQUES_FILE__;
    $uniques_files = glob($uniques_file_match);

    $file =isset($uniques_files[0])? $uniques_files[0]: "";

    return $file;
}

function getUniquesFileLinkHTML(){
    $file = getUniquesFile();
    $uniquesLink = HTML::a($file, $file);

    return $uniquesLink;
}

function getInputFiles(){
    $input_file_match = $_REQUEST["dir"] . "/" . __INPUTS_FOLDER__ . "*";
    $input_files = glob($input_file_match);

    return $input_files;
}

function getInputFilesListHTML(){
    $input_files = getInputFiles();
    foreach ($input_files as $id => $input_file){
        $input_files[$id] = HTML::a($input_file, $input_file);
    }

    return HTML::ul($input_files);
}

function getInputFilePreviewHTML($inputFiles, $rowCount){
    if (!is_array($inputFiles)){
        $inputFiles = array($inputFiles);
    }

    $html = "";
    foreach ($inputFiles as $file){
        $reader = new CsvRandomReader();
        $reader->open($file);

        $rows = array();
        for ($i = 0; $i < $rowCount && $reader->getRowCount() > $i; $i++){
            $rows[] = $reader->readRow($i);
        }

        $html .= HTML::table($rows);
    }

    return $html;
}

function getInputFileColumns($inputFile){
    $reader = new CsvRandomReader();
    $reader->open($inputFile);
    if ($reader->getRowCount() > 0){
        $row = $reader->readRow(0);
        return array_keys($row);
    } else {
        return array();
    }
}

function showDupGroups(){
    $dedups_match = $_REQUEST["dir"] . "/" . __DUPLICATES_FOLDER__ . "*";
    $dedups = glob($dedups_match);

    foreach ($dedups as $id => $dedup){
        $link = getViewDupsGroupLink($dedup);
        $dedups[$id] = HTML::a($dedup, $link);
    }

    echo HTML::ul($dedups);
}

function getAvailableFilters(){
    $filters_match = __FILTERS_DIR__ . "*Filter.php";
    $filters = glob($filters_match);
    $excludedFilters = array("No", "");

    foreach ($filters as $key => $filter){
        $parts = explode("/", $filter);
        $filter = $parts[count($parts)-1];
        $parts = explode("Filter.php", $filter);
        $filter = $parts[0];

        if (in_array($filter, $excludedFilters)){
            unset($filters[$key]);
        } else {
            $filters[$key] = $filter;
        }
    }

    return $filters;
}