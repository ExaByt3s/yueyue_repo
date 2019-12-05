<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/4/20
 * Time: 16:09
 */
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$user_id = $_GET['user_id'];

$code = 0;
$return_str = "正常";

$chat_obj = POCO::singleton('pai_chat_user_info');
if($user_id)
{
    if(!$chat_obj->redis_get_user_info($user_id))
    {
        $code = '-1';
        $return_str = "更新失败";
    }
}else{
    $code = '-1';
    $return_str = "没有user_id";
}

json_msg($code, $return_str);
?>