<?php

class SQL
{
    static function insert($table, $data){
        $sql = "insert into $table(";
        $sql .= implode(",", array_keys($data));
        $sql .= ") values ('";
        $sql .= implode("','", array_values($data));
        $sql .= "')";

        return $sql;
    }

    private static function where($conditions){
        $str_condiciones = "";

        if (count($conditions) > 0){
            $array_condiciones = [];
            foreach ($conditions as $clave => $valor){
                $array_condiciones[] = "$clave = '$valor'";
            }

            $str_condiciones = implode(" and ", $array_condiciones);
            $str_condiciones = " where $str_condiciones";
        }

        return $str_condiciones;
    }

    private static function limit($limitLength, $limitStart = null) {
        $str_condiciones = " limit ";

        if (is_null($limitStart)){
            $limitStart = 0;
        }

        $str_condiciones .= $limitStart . ", " . ($limitStart + $limitLength);

        return $str_condiciones;
    }

    static function select($table, $columns = null, $conditions = null,  $limitLength = null, $limitStart = null){
        $sql = "select ";

        if (empty($columns)){
            $sql .= "*";
        } else {
            if (is_array($columns)){
                $sql .= implode(",", $columns);
            } else {
                $sql .= $columns;
            }
        }

        $sql .= " from $table";
        $sql .= static::where($conditions);

        if (!is_null($limitLength)) {
            $sql .= static::limit($limitLength, $limitStart);
        }

        return $sql;
    }

    static function delete($table, $conditions){
        if ($conditions == null){
            $sql = "truncate $table";
        } else {
            $sql = "delete from $table";
            $sql .= static::where($conditions);
        }

        return $sql;
    }

    static function update($table, $data, $conditions){
        $sql = "update $table set ";

        $datos_procesados = [];
        foreach ($data as $columna => $valor){
            $datos_procesados[] = "$columna='$valor'";
        }
        $sql .= implode(", ", $datos_procesados);

        $sql .= static::where($conditions);

        return $sql;
    }

    static function createTable ($table, $data) {
        $sql = "CREATE TABLE " . $table . " (";
/*
        $i = 0;
        $dataSize = count($data);*/

        foreach ($data as $column => $value)
        {
            $processedData[] = $column . " varchar(100)";/*
            $sql .= " " . $column . " varchar(100)";
            $i++;
            if ($i < $dataSize) { $sql .= ","; };*/
        }
        $sql .= implode(", ", $processedData);

        $sql .= ")";
        print_r($sql);

        return $sql;
    }
}
