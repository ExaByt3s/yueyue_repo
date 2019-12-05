<?php
include_once "../poco_app_common.inc.php";

include_once "pai_score_config.inc.php";
global $operate_array_num;

$sql_str = "SELECT * FROM pai_score_db.pai_operate_queue_tbl LIMIT 0, 1000";
$result  = db_simple_getdata($sql_str, FALSE, 101);
foreach($result AS $key=>$val)
{
    if($val['operate'] == 'income')
    {
        $score = $val['operate_num'] * 1 + 50;
        $array[$val[user_id]] += (int)$score;
    }
}

foreach($array AS $key=>$val)
{
    $sql_str = "INSERT INTO pai_score_db.pai_user_score_tbl(user_id, total_score, effective_score, recently_score, level) 
                VALUES ($key, $val, $val, $val, 1)";
    db_simple_getdata($sql_str, TRUE, 101);
}


?>