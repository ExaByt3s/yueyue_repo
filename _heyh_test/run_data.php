<?php
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
include_once('/disk/data/htdocs232/poco/pai/yue_admin/cms/cms_common.inc.php');
include_once('/disk/data/htdocs232/poco/pai/event_act.php');

$cms_del_obj = POCO::singleton('pai_cms_parse_class');
$cms_del_obj->del_goods_id(100008);


$cms_obj = new cms_system_class();
$task_goods_obj = POCO::singleton('pai_mall_goods_class');
$mall_seller_obj = POCO::singleton('pai_mall_seller_class');

$sql_str = "SELECT * FROM  pai_cms_db.cms_record_tbl_last_issue WHERE user_id>350919";
$result = db_simple_getdata($sql_str, FALSE, 101);

foreach($result AS $key=>$val)
{
    $r_data = array(
        'keywords' => $val['user_id'],
    );
    $tmp_result = $task_goods_obj->user_search_goods_list($r_data, '0,1');
    if(!$tmp_result[total] || $tmp_result['data'][0]['is_show'] != 1)
    {
        //echo $val['user_id'];
        $sql_str = "DELETE FROM pai_cms_db.cms_record_tbl_last_issue WHERE log_id=$val[log_id]";
        echo $sql_str . "<BR>";
        db_simple_getdata($sql_str, TRUE, 101);
    }
}

$sql_str = "SELECT * FROM  pai_cms_db.cms_record_tbl WHERE user_id>350919";
$result = db_simple_getdata($sql_str, FALSE, 101);

foreach($result AS $key=>$val)
{
    $r_data = array(
        'keywords' => $val['user_id'],
    );
    $tmp_result = $task_goods_obj->user_search_goods_list($r_data, '0,1');
    if(!$tmp_result[total] || $tmp_result['data'][0]['is_show'] != 1)
    {
        //echo $val['user_id'];
        $sql_str = "DELETE FROM pai_cms_db.cms_record_tbl WHERE log_id=$val[log_id]";
        echo $sql_str . "<BR>";
        db_simple_getdata($sql_str, TRUE, 101);
    }
}

?>