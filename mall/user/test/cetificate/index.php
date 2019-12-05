<?php
/**
 * 
 *
 * @author hudingwen
 * @version $Id$
 * @copyright , 15 July, 2015
 * @package default
 */

/**
 * Define 认证
 */

include_once 'config.php';
$pc_wap = 'wap/';
$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'cetificate/index.tpl.htm');	

// 权限检查
$check_arr = mall_check_user_permissions($yue_login_id);

// 账号切换时
if(intval($check_arr['switch']) == 1)
{
	$url='http://'.$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"]; 
	header("Location:{$url}");
	die();
}

$user_level_obj = POCO::singleton ( 'pai_user_level_class' );

// 头部css相关
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/head.php');
// 头部公共样式和js引入
$wap_global_top = _get_wbc_head();
$tpl->assign('wap_global_top', $wap_global_top);

// 底部公共文件引入
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/footer.php');
$wap_global_footer = _get_wbc_footer();
$tpl->assign('wap_global_footer', $wap_global_footer);

/**
 * 判断客户端
 */
$__is_weixin = stripos($_SERVER['HTTP_USER_AGENT'], 'micromessenger') ? true : false;
$__is_android = stripos($_SERVER['HTTP_USER_AGENT'], 'android') ? true : false;
$__is_iphone = stripos($_SERVER['HTTP_USER_AGENT'], 'iphone') ? true : false;  
$__is_yueyue_app = (preg_match('/yue_pai/',$_SERVER['HTTP_USER_AGENT'])) ? true : false;

/*
 * 信用等级详细
 * 
 * 返回
	 upload : 1 要传图
	 text : 接口返回信息
	 is_check : 是否已审核

	 upload为1、is_check为0 审核中

 *   balance_status: yes为已充值300，no为未充值
 */

$level_detail = $user_level_obj->level_detail($yue_login_id);

// 未审核
if($level_detail['is_check'] != 1)
{
	if($level_detail['upload'] != 1)
	{
		// 审核中
		$level_detail['status'] = 0;

	}
	else
	{
		// 审核失败
		$level_detail['status'] = -1;

	}
	
	$level_detail['set_lv'] = 2;
}
else
{
	$level_detail['status'] = 1;
	
	// 填写认证流程
	if($level_detail['balance_status'] == "no")
	{
		$level_detail['status'] = -1;
		
		// 尚未进行v3认证
		$level_detail['set_lv'] = 3;
		
		$tpl->assign('balance',300);
	}
	else
	{
		$level_detail['status'] = 0;
		
		$level_detail['set_lv'] = 3;
	}
}

if($_INPUT['print'] == 1)
{
	print_r($level_detail);
	die();
	
}

if($yue_login_id == 100001)
{
	//$level_detail['status'] = -1;
	//$tpl->assign('balance',300);
	// 尚未进行v3认证
	$level_detail['set_lv'] = 3;
}

$lv = $level_detail['set_lv'];

$get_lv = trim($_INPUT['lv']);


$tpl->assign('data',$level_detail);
$tpl->assign('level',$lv);
$tpl->assign('title',$lv == 2?'V2实名认证':'V3达人认证');
//$tpl->assign('message',$lv == 2?'':'在进行v3认证前，约约君将引导你完成v2认证哦，谢谢');
$tpl->assign('head_html',$wap_global_top);
$tpl->assign('pay_recharge.php',$__is_weixin);

if($get_lv == 'V3' && $lv == 2)
{
	echo '<script>alert("在进行v3认证前，约约君将引导你完成v2认证哦，谢谢")</script>';
}
elseif($get_lv == 'V2' && $lv == 3)
{
	echo '<script>alert("你已经认证了V2，约约君将引导你完成v3认证哦，谢谢")</script>';
}

$tpl->output();


	
?>