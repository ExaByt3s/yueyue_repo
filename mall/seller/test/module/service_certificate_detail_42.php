<?php
/*
 *
 * 服务认证模块
 *
 *
 *
 *
 *
 */

//摄影服务

//配置特定数组
$certificate_activity_do_well = pai_mall_load_config('certificate_activity_do_well');//擅长的活动
//配置数组已经是二维，区别其他分类，不做构造二维处理
$certificate_activity_is_past_lead = pai_mall_load_config('certificate_activity_is_past_lead');//以前组织的活动
//配置数组已经是二维，区别其他分类，不做构造二维处理

$tpl->assign("certificate_activity_do_well",$certificate_activity_do_well);
$tpl->assign("certificate_activity_is_past_lead",$certificate_activity_is_past_lead);

//获取个人介绍资料
//print_r($seller_info);

$introduce = str_replace(array('&lt;','&gt;','&quot;'),array('<','>','"'),$seller_info["seller_data"]["profile"][0]["introduce"]);

$tpl->assign("introduce",$introduce);

?>