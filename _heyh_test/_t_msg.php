<?php
/**
 * Created by PhpStorm.
 * User: heyaohua
 * Date: 2015/7/24
 * Time: 13:13
 */

include_once("../poco_app_common.inc.php");

$insert_sql = serialize($_REQUEST);
$sql_str = "INSERT INTO test.t_msg_tbl(`remark`) VALUES ('{$insert_sql}')";
db_simple_getdata($sql_str, TRUE, 101);


?>