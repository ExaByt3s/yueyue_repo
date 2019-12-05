<?php
include_once 'config.php';
$pc_wap = 'wap/';
$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'home/edit.tpl.html');

// 头部css相关
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/head.php');
// 头部公共样式和js引入
$wap_global_top = _get_wbc_head();
$tpl->assign('wap_global_top', $wap_global_top);

// 底部公共文件引入
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/footer.php');
$wap_global_footer = _get_wbc_footer();
$tpl->assign('wap_global_footer', $wap_global_footer);

// 权限检查
$check_arr = mall_check_user_permissions($yue_login_id);

// 账号切换时
if(intval($check_arr['switch']) == 1)
{
	$url='http://'.$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"];
	header("Location:{$url}");
	die();
}

$ret = get_api_result("customer/buyer_user_init.php",array(
        'user_id'=>$yue_login_id
        ),$decode = TRUE, $to_gbk = TRUE,TRUE);
		
if($_INPUT['print'] == 1)
{
	print_r($ret);
	die();
}		

$data = $ret['data'];
$nickname = $data['nickname'];
$intro = $data['intro'];
$location_id = $data['location_id'];
$is_display_record = $data['is_display_record'];
$avatar = $data['avatar'];
$location_id = $data['location_id'];
$location = get_poco_location_name_by_location_id ( $location_id );

//input层

$limit_num = intval($_INPUT['limit_num']);
$tpl->assign('limit_num',$limit_num);

$input_title = trim($_INPUT['input_title']);
$input_title = urldecode($input_title);
$input_title = mb_convert_encoding($input_title,'gbk','utf-8');

$type = trim($_INPUT['type']);

$input_content = trim($_INPUT['input_content']);
$input_content = urldecode($input_content);
$input_content = mb_convert_encoding($input_content,'gbk','utf-8');

$showcase = mall_output_format_data($data['showcase']);

//用户头像Icon处理2015-7-8
$icon_hash = md5($yue_login_id.'YUE_PAI_POCO!@#456');
$tpl->assign("icon_hash",$icon_hash);
$tpl->assign('showcase',$showcase);

$tpl->assign('input_content',$input_content);
$tpl->assign('input_title',$input_title);
$tpl->assign('type',$type);


$tpl->assign('expense_code',1);
$tpl->assign('nickname',$nickname);
$tpl->assign('intro',$intro);
$tpl->assign('is_display_record',$is_display_record);
$tpl->assign('avatar',$avatar);
$tpl->assign('location_id',$location_id);
$tpl->assign('location',$location);

$tpl->output();
?>