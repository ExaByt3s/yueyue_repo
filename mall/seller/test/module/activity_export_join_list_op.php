<?php

/**
 * 报名名单
 *
 * 2015-11-12
 *
 * author  星星
 *
 */

include_once '../common.inc.php';
include_once("/disk/data/htdocs232/poco/pai/yue_admin_v2/common/Excel_v2.class.php");//导出类
$pai_mall_goods_obj = POCO::singleton('pai_mall_goods_class');
$order_obj = POCO::singleton('pai_mall_order_class');

$goods_id = (int)$_INPUT["goods_id"];//活动ID
$type_id = trim($_INPUT["type_id"]);//场次ID


if(empty($yue_login_id))
{
    echo "<script>top.alert('没有登录');window.location.href='../normal_certificate_basic.php';</script>";
    exit();
}

$user_id = $yue_login_id;


$goods_info = $pai_mall_goods_obj->user_get_goods_info($goods_id,$user_id);

$now_goods_user_id = $goods_info['data']['goods_data']["user_id"];
if($user_id!=$now_goods_user_id)
{
    //没有进行商家认证的
    echo "<script>alert('当前商品不属于此登录用户');window.location.href='../normal_certificate_basic.php';</script>";
    exit;
}

$activity_title = $goods_info["data"]["default_data"]["titles"]["value"];
$join_list = $order_obj->get_order_list_of_paid_by_stage($goods_id,$type_id,false,"","0,99999");
$data = array();
foreach($join_list as $key => $value)
{
    $data[$key]["sequence_num"] = (int)$key+1;
    $data[$key]["buyer_user_name"] = $value["buyer_user_name"];//用户昵称
    $data[$key]["buyer_user_cellphone"] = $value["buyer_user_cellphone"];//用户手机号
    //$data[$key]["buyer_user_id"] = $value["buyer_user_id"];//用户ID
    $data[$key]["prices_spec"] = $value["prices_spec"];//规格
    $data[$key]["add_time"] = date("Y-m-d h:i",$value["add_time"]);//报名时间
    $data[$key]["quantity"] = $value["quantity"];//报名人数
    $data[$key]["description"] = $value["description"];//备注
    //$data[$key]["goods_id"] = $goods_id;//备注

}


$fileName = "活动标题：".$activity_title."--活动ID：".$goods_id;
//$headArr  = array("序号","用户昵称","用户手机号","用户ID","规格","报名时间","报名人数","备注","活动ID");
$headArr  = array("序号","用户昵称","用户手机号","规格","报名时间","报名人数","备注");
Excel_v2::start($headArr,$data,$fileName);
















?>