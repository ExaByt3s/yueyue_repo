<?php

/**
 * 基本认证页面，认证类别选择的页面
 *
 *
 * 2015-6-18
 *
 *
 *  author    星星
 *
 *
 */

include_once 'common.inc.php';
$pc_wap = 'pc/';
$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'normal_certificate_basic.tpl.htm');


//echo 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
if(empty($yue_login_id))
{
    $r_url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    $r_url = urlencode($r_url);
    echo "<script type='text/javascript'>location.href='http://www.yueus.com/pc/login.php?r_url=".$r_url."';</script>";
    exit;
    //echo "not login error";
    //exit();
}

//校验用户角色,非商家报错
$user_id = $yue_login_id;
/*
$mall_obj = POCO::singleton('pai_mall_seller_class');
$seller_info=$mall_obj->get_seller_info($user_id,2);
$seller_name=$seller_info['seller_data']['name'];*/

//获取基础认证信息认证信息
$mall_basic_check_obj = POCO::singleton('pai_mall_certificate_basic_class');
$user_basic_status_list = $mall_basic_check_obj->get_person_status_by_user_id($user_id);
$end_basic_certificate = 0;//控制是否出商家服务按钮
if($user_basic_status_list['status']==0)
{

    $basic_certificate = "checking";//审核中
    $end_basic_certificate = "1";//控制是否出商家服务按钮
}
else if($user_basic_status_list['status']==1)
{
    $basic_certificate = "pass";//通过了审核
    $end_basic_certificate = "1";//控制是否出商家服务按钮
}
else if($user_basic_status_list['status']==2)
{
    if($user_basic_status_list['basic_type']=="person")
    {
        $basic_certificate = "recheck_person";//需要审个人
    }
    else
    {
        $basic_certificate = "recheck_company";//需要审公司
    }
    $end_basic_certificate = "1";//控制是否出商家服务按钮
}
else
{
    $basic_certificate = "no_record";//没有记录
}

//print_r($user_basic_status_list);


//检查用户的服务认证信息
$mall_service_check_obj = POCO::singleton('pai_mall_certificate_service_class');
$user_status_list = $mall_service_check_obj->get_service_status_by_user_id($user_id,true);


/*if($yue_login_id==100004 || $yue_login_id==101615)
{
    print_r($user_status_list);
}*/




//配置一个数组
$type_name_array = array("model"=>array(31,"模特服务"),
    "cameror"=>array(40,"摄影服务"),
    "studio"=>array(12,"影棚租赁"),
    "teacher"=>array(5,"摄影培训"),
    "dresser"=>array(3,"化妆服务"),
    "diet"=>array(41,"美食达人"),
    "other"=>array(43,"其他服务")
);

$status_array = array("-2"=>"no_record","0"=>"checking","1"=>"pass","2"=>"recheck","-3"=>"no_power");
//$type_id_array = explode(',',$seller_info['seller_data']['profile'][0]['type_id']);
//匹配出当前人的服务认证的最终状态
$service_end_status_array = array();
foreach($user_status_list as $key => $value)
{
    $type_list[$key]['id'] = $type_name_array[$value['service_type']][0];
    $end_status_array[] = $status_array[$value['status']];


}

$can_publish_service = false;
if(in_array("no_power",$end_status_array))
{
    $service_end_status = "no_power";
}
else if(in_array("pass",$end_status_array))
{
    $service_end_status = "pass";
    $can_publish_service = true;
}
else if(in_array("checking",$end_status_array))
{
    $service_end_status = "checking";
    $can_publish_service = true;
}
else if(in_array("recheck",$end_status_array))
{
    $service_end_status = "recheck";
}
else
{
    $service_end_status = "no_record";
}

//系统消息
$system_msg = "【系统通知】完成实名和服务认证后，买家才能看到你的商品哦~";

//引导信息
$hide_gudie = 0;
if(isset($_COOKIE["normal_certificate_basic_guide"]))
{
    $hide_gudie = 1;
}
//系统消息显示
$hide_system_msg = 1;//默认是显示提示
/*if(isset($_COOKIE["normal_certificate_basic_system_msg"]))
{
    $hide_system_msg = 1;
}*/

//根据基本状态跟服务认证状态出系统消息
$basic_certificate_arr = array("recheck_person","recheck_company","no_record");
$service_end_status_arr = array("no_record","recheck");
if(in_array($basic_certificate,$hide_system_msg_arr))
{
    $hide_system_msg = 0;
}
else
{
    //判断服务认证最高权限
    if(in_array($service_end_status,$service_end_status_arr))
    {
        $hide_system_msg = 0;
    }
}


// 头部css相关
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/head.php');

// 顶部栏
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/global-top-bar.php');

// 底部
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/footer.php');
// 头部公共样式和js引入
$pc_global_top = _get_wbc_head();
$tpl->assign('pc_global_top', $pc_global_top);


// 头部bar
$global_top_bar = _get_wbc_global_top_bar();
$tpl->assign('global_top_bar', $global_top_bar);


// 底部
$footer = _get_wbc_footer();
$tpl->assign('footer', $footer);

$page_title = "基本认证页";



$tpl->assign("page_title",$page_title);

//$tpl->assign("basic_type",$user_basic_status_list['basic_type']);//认证类型
$tpl->assign("basic_certificate",$basic_certificate);
$tpl->assign("service_end_status",$service_end_status);
$tpl->assign("can_publish_service",$can_publish_service);
$tpl->assign("system_msg",$system_msg);
$tpl->assign("hide_gudie",$hide_gudie);
$tpl->assign("hide_system_msg",$hide_system_msg);
$tpl->assign("end_basic_certificate",$end_basic_certificate);



$tpl->output();

?>