<?php
define("__ROOT_DIR__", "../");

define("__DEDUP_DIR__", __ROOT_DIR__ . "deduplications/");
    define("__DEDUPS_DIRS__", "dedup*");
        define("__UNIQUES_FILE__", "uniques.*");
        define("__DUPLICATES_FOLDER__", "duplicates/");
        define("__INPUTS_FOLDER__", "input/");

define("__VIEW_UNIQUES_FILE__", "uniques.php");
define("__VIEW_DEDUPS_FILE__", "deduplications.php");
define("__VIEW_DEDUP_FILE__", "dedup.php");

include_once("HTML.php");

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
    return  __READ_DUPS_GROUP__ . "?dupsGroup=" . $file;
}

function showUniquesFile(){
    $uniques_file_match = $_REQUEST["dir"] . "/" . __UNIQUES_FILE__;
    $uniques_files = glob($uniques_file_match);
    $file = $uniques_files[0];

    $uniquesLink = HTML::a($file, $file);

    echo $uniquesLink;
}

function showInputFiles(){
    $input_file_match = $_REQUEST["dir"] . "/" . __INPUTS_FOLDER__ . "*";
    $input_files = glob($input_file_match);

    foreach ($input_files as $id => $input_file){
        $input_files[$id] = HTML::a($input_file, $input_file);
    }

    echo HTML::ul($input_files);
}