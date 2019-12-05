<?php
include_once 'config.php';

// 权限检查
$check_arr = mall_check_user_permissions($yue_login_id);

// 账号切换时
if($check_arr['switch'] == 1)
{
	$url='http://'.$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"]; 
	header("Location:{$url}");
	die();
}

$pc_wap = 'wap/';
$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'home/qrcode_list.tpl.htm');

// 没有登录的处理
if(empty($yue_login_id))
{
    $output_arr['code'] = -1;
    $output_arr['msg']  = '尚未登录,非法操作';
    $output_arr['data'] = array();
    exit();
}

// 头部css相关
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/head.php');
// 头部公共样式和js引入
$pc_global_top = _get_wbc_head();
$tpl->assign('pc_global_top', $pc_global_top);

// 底部公共文件引入
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/footer.php');
$wap_global_footer = _get_wbc_footer();
$tpl->assign('wap_global_footer', $wap_global_footer);


$ret = get_api_result('customer/get_qrcode_list.php',array(
    'user_id' => $yue_login_id,
    'page' => 1 ,
    'row' => 5
    ), true); 

if ($_INPUT['print']) 
{
    print_r($ret );
}


$tpl->assign('user_id', $ret['data']['user_id']);
$tpl->assign('nickname', $ret['data']['nickname']);
$tpl->assign('icon', $ret['data']['icon']);

$tpl->output();

?>