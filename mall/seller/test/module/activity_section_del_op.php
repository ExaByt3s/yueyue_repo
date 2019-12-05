<?php

/**
 * 异步删除活动场次
 *
 * 2015-11-3
 *
 * author  星星
 *
 */

include_once '../common.inc.php';

$pai_mall_goods_obj = POCO::singleton('pai_mall_goods_class');

$type_id = (int)$_INPUT["type_id"];
$goods_id = (int)$_INPUT["goods_id"];

$user_id = $yue_login_id;

$ajax_status = 1;
if(empty($user_id))
{
    $ajax_status = 0;
}
if($ajax_status==1)
{
    //相应操作
    $ret = $pai_mall_goods_obj->user_delete_goods_prices_detail_for_42($goods_id,$type_id,$user_id);
    if($ret["result"] > 0)
    {
        $ajax_status = 1;
        $msg = iconv('gbk//IGNORE','utf-8', $ret["message"]);

    }
    else
    {
        $ajax_status = 0;
        $msg = iconv('gbk//IGNORE','utf-8', $ret["message"]);
    }
}



$arr["ajax_status"] = $ajax_status;
$arr["msg"] = $msg;

echo json_encode($arr);
exit;

?>