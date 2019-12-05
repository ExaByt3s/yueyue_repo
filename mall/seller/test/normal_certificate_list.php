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
$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'normal_certificate_list.tpl.htm');


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



$mall_basic_check_obj = POCO::singleton('pai_mall_certificate_basic_class');
$mall_service_check_obj = POCO::singleton('pai_mall_certificate_service_class');
$user_basic_status_list = $mall_basic_check_obj->get_person_status_by_user_id($user_id);
$user_status_list = $mall_service_check_obj->get_service_status_by_user_id($user_id);//添加后参数，其他分类通过，约活动也通过




//print_r($user_basic_status_list);
//print_r($user_status_list);
//exit();

$task_goods_type_obj = POCO::singleton('pai_mall_goods_type_class');
$config_type_list = $task_goods_type_obj->get_type_cate();
foreach($config_type_list as $key => $value)
{
    $tmp_type_name_array[$value["id"]] = $value["name"];
}
//配置一个数组
$type_name_array = array("model"=>array(31,$tmp_type_name_array["31"]),
    "cameror"=>array(40,$tmp_type_name_array["40"]),
    "studio"=>array(12,$tmp_type_name_array["12"]),
    "teacher"=>array(5,$tmp_type_name_array["5"]),
    "dresser"=>array(3,$tmp_type_name_array["3"]),
    "diet"=>array(41,$tmp_type_name_array["41"]),
    "other"=>array(43,$tmp_type_name_array["43"]),
    "activity"=>array(42,$tmp_type_name_array["42"])
);

$status_array = array("-2"=>"no_record","0"=>"checking","1"=>"pass","2"=>"recheck","-3"=>"no_power");
$type_id_array = explode(',',$seller_info['seller_data']['profile'][0]['type_id']);
foreach($user_status_list as $key => $value)
{
    $type_list[$key]['service_belong'] = $value['service_belong'];
    $type_list[$key]['name'] = $type_name_array[$value['service_type']][1];
    $type_list[$key]['id'] = $type_name_array[$value['service_type']][0];
    $type_list[$key]['show'] = $status_array[$value['status']];

}
//服务大分类数组
$service_large_type_array = $mall_service_check_obj->get_service_type();
$service_large_type_array_new = array();
//根据类别联合构造大分类数组
foreach($service_large_type_array as $k => $v)
{
    foreach($type_list as $key => $value)
    {
        if($value['service_belong']==$v)
        {
            $service_large_type_array_new[$k]["service_belong"] = $v;
            $service_large_type_array_new[$k]["service_belong_array"][] = $value;
        }
    }
}
/*print_r($user_status_list);
print_r($service_large_type_array);
print_r($type_list);
print_r($service_large_type_array_new);
exit();*/



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

$page_title = "服务认证页";



$tpl->assign("page_title",$page_title);
$tpl->assign("service_large_type_array_new",$service_large_type_array_new);

$tpl->output();

?>