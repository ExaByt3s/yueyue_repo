<?php
/**
 * 父亲节公益活动报名
 * 星星
 *
 * 2015-6-3
 */

include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');
//取得应用操作对象实例
//使用异步添加记录
define("G_DB_GET_REALTIME_DATA",1);
//约摄对象
$pai_yueshe_topic_obj   = POCO::singleton('pai_yueshe_topic_class');

$name = trim($_INPUT['name']);
$cellphone = (int)$_INPUT['cellphone'];
$weixin = trim($_INPUT['weixin']);
$age = (int)$_INPUT['age'];
$story = trim($_INPUT['story']);

$ajax_status = 1;
$res = 0;
$error_reason = "";

if(empty($name) && $ajax_status == 1)
{
    $ajax_status = 0;
    $error_reason = "name_error";
}

if(empty($cellphone) && $ajax_status == 1)
{
    $ajax_status = 0;
    $error_reason = "cellphone_error";
}

if(empty($weixin) && $ajax_status == 1)
{
    $ajax_status = 0;
    $error_reason = "weixin_error";
}

if(empty($age) && $ajax_status == 1)
{
    $ajax_status = 0;
    $error_reason = "age_error";
}

if(empty($story) && $ajax_status == 1)
{
    $ajax_status = 0;
    $error_reason = "story_error";
}


if($ajax_status==1)
{
    $insert_data['name'] = $name;
    $insert_data['cellphone'] = $cellphone;
    $insert_data['weixin'] = $weixin;
    $insert_data['age'] = $age;
    $insert_data['story'] = $story;

    $ret = $pai_yueshe_topic_obj->add_enroll($insert_data);
    if($ret)
    {
        $res = 1;
    }
    else
    {
        $res = 0;
    }
}

$res_arr = array(
    "ajax_status"=>$ajax_status,
    "res"=>$res,
    "error_reason"=>$error_reason
);

echo json_encode($res_arr);
?>