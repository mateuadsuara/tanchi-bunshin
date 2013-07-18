<!DOCTYPE html>
<html>
<head>
    <title></title>
    <style type="text/css">
        table td{
            border: 1px solid;
        }
    </style>
</head>
<body>
    <?php
        include_once("common.php");
        set_time_limit(0);

        include_once(__ROOT_DIR__ . "src/HashUniquesExporter.php");
        include_once(__ROOT_DIR__ . "src/RandomReaders/CsvRandomReader.php");
        include_once(__ROOT_DIR__ . "src/HashCalculators/StringHashCalculator.php");
        foreach (glob(__ROOT_DIR__ . "src/HashCalculators/Filters/*.php") as $filename){
            include_once($filename);
        }
        foreach (glob(__ROOT_DIR__ . "src/Writers/*.php") as $filename){
            include_once($filename);
        }
        foreach (glob(__ROOT_DIR__ . "src/RandomReaders/*.php") as $filename){
            include_once($filename);
        }

        function rmdir_recursive($dir) {
            $it = new RecursiveDirectoryIterator($dir);
            $it = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
            foreach($it as $file) {
                if ('.' === $file->getBasename() || '..' ===  $file->getBasename()) continue;
                if ($file->isDir()) rmdir($file->getPathname());
                else unlink($file->getPathname());
            }
            rmdir($dir);
        }

        function getPostVar($name){
            return json_decode($_POST[$name]);
        }

        $INPUT_FILES = getPostVar("inputFiles");
        $GLOBAL_FILTERS = getPostVar("globalFilters");
        $WATCH_COLUMNS = getPostVar("compareColumns");
        $UNIQUES_FILE = __DEDUP_DIR__ . getPostVar("dir") .  "/" . "uniques.csv";
        $DUPS_DIR = __DEDUP_DIR__ . getPostVar("dir") . "/" . __DUPLICATES_FOLDER__ ;

        if (is_dir($DUPS_DIR)){
            rmdir_recursive($DUPS_DIR);
        }
        mkdir($DUPS_DIR);

        if (is_file($UNIQUES_FILE)){
            unlink($UNIQUES_FILE);
        }

        function getFilterGroup($arrayNames){
            $filters = array();
            foreach ($arrayNames as $name){
                $class = $name . "Filter";
                $filters[] = new $class();
            }
            return FilterGroup::create($filters);
        }

        $memoryUsage = memory_get_usage(true) / 1024 / 1024;
        echo "<h1>Memory Usage: $memoryUsage MB</h1>";

        $startTime = microtime(true);

        $scanner = new HashUniquesExporter();

        $reader = new CsvRandomReader();
        $reader->open($INPUT_FILES[0]);
        $scanner->setReader($reader);

        $calculator = new StringHashCalculator();
        $calculator->setGlobalFilter(
            getFilterGroup($GLOBAL_FILTERS)
        );

        $calculator->watchColumns($WATCH_COLUMNS);

        $scanner->setHashCalculator($calculator);

        $uniquesWriter = new CsvWriter();
        $uniquesWriter->create($UNIQUES_FILE);
        $scanner->setUniquesWriter($uniquesWriter);


        class CustomWriterFactory implements WriterFactory{
            function createWriter($id){
                global $DUPS_DIR;

                $writer = new CsvWriter();
                $writer->create($DUPS_DIR . "$id.csv");
                if (!$writer->isReady()){
                    throw new Exception("Writer not ready!");
                }
                return $writer;
            }
        }
        $scanner->setDuplicatesWriterFactory(new CustomWriterFactory());

        $memoryUsage = memory_get_usage(true) / 1024 / 1024;
        echo "<h1>Memory Usage: $memoryUsage MB</h1>";

        $scanner->scan();

        echo "<h1>Done!</h1>";


        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $memoryUsage = memory_get_usage(true) / 1024 / 1024;

        echo "<h1>Execution Time: $executionTime seconds</h1>";
        echo "<h1>Memory Usage: $memoryUsage MB</h1>";
    ?>
</body>
</html>