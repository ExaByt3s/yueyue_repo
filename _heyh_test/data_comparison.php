<?php
/**
 * Created by PhpStorm.
 * User: heyaohua
 * Date: 2015/9/15
 * Time: 16:32
 */

set_time_limit(3600);
include_once ('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

echo "¿ªÊ¼";

$server_id = 777;


$sql_str = "SHOW DATABASES";
$db_array = db_simple_getdata($sql_str, FALSE, $server_id);

foreach($db_array AS $key=>$val)
{
    $database = $val['Database'];
    $sql_str = "SHOW FULL TABLES FROM `" . $database . "` WHERE table_type = 'BASE TABLE'; ";
    $table_array = db_simple_getdata($sql_str, FALSE, $server_id);
    if(count($table_array))
    {
        foreach($table_array AS $k=>$v)
        {
            $key = 'Tables_in_' . $database;

            $table_name = $database . "." . $v[$key];
            $sql_str = "SELECT COUNT(*) AS C  FROM " . $table_name;
            $count_num = db_simple_getdata($sql_str, TRUE, $server_id);

            $sql_str = "SHOW CREATE TABLE " . $table_name;
            $create_table = db_simple_getdata($sql_str, TRUE, $server_id);
           //var_dump($count_num);
            //echo "<BR>----------------------<BR>";


            $code = md5($create_table["Create Table"]);
            //echo $code;
            //exit();
            $sql_str = "INSERT INTO test.data_comparison_tbl(table_name, 237_count, 237_structure_md5)
                        VALUES ('{$table_name}', '{$count_num[C]}', '{$code}')";
            db_simple_getdata($sql_str, TRUE, 101);
        }

    }
}


$server_id = 101;


$sql_str = "SHOW DATABASES";
$db_array = db_simple_getdata($sql_str, FALSE, $server_id);

foreach($db_array AS $key=>$val)
{
    $database = $val['Database'];
    $sql_str = "SHOW FULL TABLES FROM `" . $database . "` WHERE table_type = 'BASE TABLE'; ";
    $table_array = db_simple_getdata($sql_str, FALSE, $server_id);
    if(count($table_array))
    {
        foreach($table_array AS $k=>$v)
        {
            $key = 'Tables_in_' . $database;

            $table_name = $database . "." . $v[$key];
            $sql_str = "SELECT COUNT(*) AS C  FROM " . $table_name;
            $count_num = db_simple_getdata($sql_str, TRUE, $server_id);

            $sql_str = "SHOW CREATE TABLE " . $table_name;
            $create_table = db_simple_getdata($sql_str, TRUE, $server_id);
            //var_dump($count_num);
            //echo "<BR>----------------------<BR>";
            //var_dump($create_table);

            $code = md5($create_table["Create Table"]);

            $sql_str = "UPDATE test.data_comparison_tbl SET 10_count = '{$count_num[C]}', 10_structure_md5 = '{$code}'
                        WHERE table_name = '{$table_name}'";
            db_simple_getdata($sql_str, TRUE, 101);
            if(db_simple_get_affected_rows() == 0)
            {
                $sql_str = "INSERT INTO test.data_comparison_tbl(table_name, 10_count, 10_structure_md5)
                        VALUES ('{$table_name}', '{$count_num[C]}', '{$code}')";
                db_simple_getdata($sql_str, TRUE, 101);
            }
        }

    }
}

echo "½áÊø£¡";



