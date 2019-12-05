<?php
/*
 *
 * //预览数据中转页，个人资料页跟商品服务预览
 *
 *
 *
 */
include_once 'common.inc.php';




$cache_id = trim($_INPUT['cache_id']);
$type_arr = array("person","service");
$type =  trim($_INPUT['type']);//预览的类型,person,service
$pai_mall_goods_obj = POCO::singleton('pai_mall_goods_class');



if(!in_array($type,$type_arr))
{
    exit;
}

/********根据预览类型，UI进行跳转***********/
// 设置UA常量
//$user_agent_arr = mall_get_user_agent_arr();
//print_r($user_agent_arr);
/*Array
(
    [is_weixin] => 0
    [is_android] => 0
    [is_iphone] => 0
    [is_yueyue_app] => 0
    [is_mobile] => 0
    [is_pc] => 1
)*/
if($type=="person")
{


    $jump_url = TASK_PREVIEW_ROOT."/seller/index.php?seller_user_id=".$cache_id."&preview=1";
    //die($jump_url);
    header("location:$jump_url");
}
else if($type=="service")
{


    //to do需要根据UA判断跳转了解
    $jump_url = TASK_PREVIEW_ROOT."/goods/service_detail.php?goods_id=".$cache_id."&preview=1";
    //die($jump_url);
    header("location:$jump_url");
}














?>