<?php

/**
 * 服务认证选择详细页（跟距参数匹配对应不同模板）
 *
 *
 * 2015-6-17
 *
 *
 *  author    星星
 *
 *
 */

include_once 'common.inc.php';

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


//print_r($seller_info);
$type_id =  trim($_INPUT['type_id']);
//判断当前登录人的基本认证情况
$mall_basic_check_obj = POCO::singleton('pai_mall_certificate_basic_class');
$user_basic_status_list = $mall_basic_check_obj->get_person_status_by_user_id($user_id);
/*if($user_basic_status_list['status']==2 || $user_basic_status_list['status']==-2)
{
    header("location:./normal_certificate_choose.php");
    exit;
}*/
if($user_basic_status_list['status']==-2)
{
    header("location:./normal_certificate_choose.php");
    exit;
}


//判断该商家是否做了当前类别认证
$has_certificate_type_id_str = $seller_info['seller_data']['company'][0]['store'][0]['type_id'];
$has_certificate_type_id_array = explode(",",$has_certificate_type_id_str);


if(in_array($type_id,$has_certificate_type_id_array))
{
    //没有进行商家认证的
    header("location:./normal_certificate_basic.php");
    exit;
}




$type_id_array = array(3,5,12,31,40,41,42,43);
if(empty($type_id) || !in_array($type_id,$type_id_array))
{
    $type_id = 3;
}

//暂时服务认证不可以编辑
//类型id选取不同模板
$pc_wap = 'pc/';




//根据type_id获取不同模板
$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'service_certificate_detail_'.$type_id.'.tpl.htm');

//根据类型选择不同的代码段
$include_once_url = "module/service_certificate_detail_".$type_id.".php";


if(file_exists($include_once_url))
{

    include_once $include_once_url;
    //echo "文件存在";
    //exit;
}
else
{
    echo "文件不存在";
    exit();
}




//对配置数组进行二维化处理
function array_to_square_array($old_array)
{
    $i=0;
    foreach($old_array as $key => $value)
    {
        $new_array[$i]['value'] = $key;
        $new_array[$i]['value_name'] = $value;
        $i++;
    }
    return $new_array;
}


//统一根据type_id控制页面名称(2015-10-27)
$pai_mall_goods_obj = POCO::singleton('pai_mall_goods_class');
$page_type_name = $pai_mall_goods_obj->get_goods_typename_for_type_id($type_id);
$page_title = $page_type_name."认证";

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



$tpl->assign("type_id",$type_id);
$tpl->assign("user_id",$user_id);
$tpl->assign("page_title",$page_title);
$tpl->assign("page_type_name",$page_type_name);
$tpl->output();

?>