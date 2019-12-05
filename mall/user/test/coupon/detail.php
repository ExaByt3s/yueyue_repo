<?php
/**
 * 
 *
 * @author hudingwen
 * @version $Id$
 * @copyright , 17 July, 2015
 * @package default
 */

/**
 * Define 优惠券详情
 */
include_once 'config.php';

$pc_wap = 'wap/';
$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'coupon/detail.tpl.html');

// 权限检查
$check_arr = mall_check_user_permissions($yue_login_id);

// 账号切换时
if(intval($check_arr['switch']) == 1)
{
	$url='http://'.$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"]; 
	header("Location:{$url}");
	die();
}

// 头部css相关
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/head.php');
// 头部公共样式和js引入
$wap_global_top = _get_wbc_head();
$tpl->assign('wap_global_top', $wap_global_top);

// 底部公共文件引入
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/footer.php');
$wap_global_footer = _get_wbc_footer();
$tpl->assign('wap_global_footer', $wap_global_footer);

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');


$coupon_obj = POCO::singleton('pai_coupon_class');

$sn = $_INPUT['sn'];


$ret = $coupon_obj -> get_coupon_detail_by_sn($sn,$yue_login_id);

$ret['scope_module_type_name'] = '「'.$ret['scope_module_type_name'].'」';
$ret['scope_order_total_amount'] = $ret['scope_order_total_amount']*1;
$ret['face_value'] = $ret['face_value']*1;
$ret['coin'] = '￥';

if($ret['scope_module_type'] == 'yuepai')
{
	$ret['scope_module_txt'] = '马上约拍， 使用优惠！';
	$ret['scope_module_btn'] = 'hot'; //首页
}
else if($ret['scope_module_type'] == 'waipai')
{
	$ret['scope_module_txt'] = '参与外拍， 使用优惠！';
	$ret['scope_module_btn'] = 'act'; //外拍
}
else if($ret['scope_module_type'] == 'task_request')
{
	$ret['scope_module_txt'] = '马上使用优惠券！';
	$ret['scope_module_btn'] = 'hot';
	
	//临时处理，不然TT优惠券会跳转去外拍首页
	$ret['scope_module_type'] = 'yuepai';
}
else if($ret['scope_module_type'] == '')
{
	$ret['scope_module_txt'] = '马上使用优惠券！';
	$ret['scope_module_btn'] = 'hot';

	//临时处理，不然通用券会跳转去外拍首页
	$ret['scope_module_type'] = 'yuepai';
}
else
{
	$ret['scope_module_txt'] = '马上使用优惠券！';
	$ret['scope_module_btn'] = 'hot';
}

$parma = mall_output_format_data($ret);
$tpl->assign('data', $parma);


$tpl->output();	
?>