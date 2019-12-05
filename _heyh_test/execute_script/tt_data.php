<?php
include_once("../poco_app_common.inc.php");

$sql_str = "SELECT request_id FROM test.task_request_tbl";
$result = db_simple_getdata($sql_str, FALSE, 101);

foreach($result AS $key=>$val)
{
    $sql_str = "SELECT add_time FROM pai_task_db.task_request_tbl WHERE request_id=$val[request_id]";
    $rs = db_simple_getdata($sql_str, TRUE, 101);

    $sql_str = "UPDATE test.task_request_tbl SET add_time=$rs[add_time] WHERE request_id=$val[request_id]";
    echo $sql_str . "<BR>";
    db_simple_getdata($sql_str, TURE, 101);
}

?>