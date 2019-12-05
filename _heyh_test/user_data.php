<?php
set_time_limit(3600);
/**
 * Created by PhpStorm.
 * User: yaohua_he
 * Date: 15/6/2
 * Time: 14:21
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

//$sql_str = "SELECT * FROM pai_db.pai_user_tbl WHERE role='cameraman' ";
//$result = db_simple_getdata($sql_str, FALSE, 101);
//
//$user_obj = POCO::singleton('pai_user_level_class');
//
//$level_array = '';
//
//foreach($result AS $key=>$val)
//{
//   $user_level_info =  $user_obj->get_user_level($val['user_id']);
//
//    $sql_str = "INSERT INTO test.user_level_tbl(user_id, level)
//                VALUES ($val[user_id], $user_level_info)";
//    db_simple_getdata($sql_str, TRUE, 101);
//}
//
//print_r($level_array);

$sql_str = "SELECT * FROM test.user_level_tbl";
$result = db_simple_getdata($sql_str, FALSE, 101);

foreach($result AS $key=>$val)
{
    $liaotian = get_liaotian_list($val[user_id]);
    $pay      = get_pay_list($val[user_id]);

    $liaotian_num = count($liaotian)?count($liaotian):0;
    $pay_num    = count($pay)?count($pay):0;

    $sql_str = "UPDATE test.user_level_tbl SET pay_num=$pay_num, liaotian_num=$liaotian_num WHERE user_id=$val[user_id]";
    db_simple_getdata($sql_str, TRUE, 101);
}

function get_pay_list($user_id)
{
    $sql_str = "SELECT * FROM event_db.event_date_tbl WHERE from_date_id=$user_id";
    $result = db_simple_getdata($sql_str, FALSE);
    return $result;
}

function get_liaotian_list($user_id)
{
    $sql_str = "SELECT * FROM test._tmp_yueyue_contact_tbl_0408_05_31
                WHERE from_user_id = $user_id";
    $result = db_simple_getdata($sql_str, FALSE, 22);
    return $result;
}

function get_liaotian_info($from_user_id, $to_user_id)
{
    $gmclient= new GearmanClient();
    $gmclient->addServers("113.107.204.233:9555");
    do
    {
        $query_str = $from_user_id . "?" . $to_user_id;
        $result= $gmclient->do("query_chatlog", $query_str);
    }
    while($gmclient->returnCode() != GEARMAN_SUCCESS);
    return $result;
}
?>