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
 * Define ��֤
 */

include_once 'config.php';
$pc_wap = 'wap/';
$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'cetificate/index.tpl.htm');	

// Ȩ�޼��
$check_arr = mall_check_user_permissions($yue_login_id);

// �˺��л�ʱ
if(intval($check_arr['switch']) == 1)
{
	$url='http://'.$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"]; 
	header("Location:{$url}");
	die();
}

$user_level_obj = POCO::singleton ( 'pai_user_level_class' );

// ͷ��css���
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/head.php');
// ͷ��������ʽ��js����
$wap_global_top = _get_wbc_head();
$tpl->assign('wap_global_top', $wap_global_top);

// �ײ������ļ�����
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/footer.php');
$wap_global_footer = _get_wbc_footer();
$tpl->assign('wap_global_footer', $wap_global_footer);

/**
 * �жϿͻ���
 */
$__is_weixin = stripos($_SERVER['HTTP_USER_AGENT'], 'micromessenger') ? true : false;
$__is_android = stripos($_SERVER['HTTP_USER_AGENT'], 'android') ? true : false;
$__is_iphone = stripos($_SERVER['HTTP_USER_AGENT'], 'iphone') ? true : false;  
$__is_yueyue_app = (preg_match('/yue_pai/',$_SERVER['HTTP_USER_AGENT'])) ? true : false;

/*
 * ���õȼ���ϸ
 * 
 * ����
	 upload : 1 Ҫ��ͼ
	 text : �ӿڷ�����Ϣ
	 is_check : �Ƿ������

	 uploadΪ1��is_checkΪ0 �����

 *   balance_status: yesΪ�ѳ�ֵ300��noΪδ��ֵ
 */

$level_detail = $user_level_obj->level_detail($yue_login_id);

// δ���
if($level_detail['is_check'] != 1)
{
	if($level_detail['upload'] != 1)
	{
		// �����
		$level_detail['status'] = 0;

	}
	else
	{
		// ���ʧ��
		$level_detail['status'] = -1;

	}
	
	$level_detail['set_lv'] = 2;
}
else
{
	$level_detail['status'] = 1;
	
	// ��д��֤����
	if($level_detail['balance_status'] == "no")
	{
		$level_detail['status'] = -1;
		
		// ��δ����v3��֤
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
	// ��δ����v3��֤
	$level_detail['set_lv'] = 3;
}

$lv = $level_detail['set_lv'];

$get_lv = trim($_INPUT['lv']);


$tpl->assign('data',$level_detail);
$tpl->assign('level',$lv);
$tpl->assign('title',$lv == 2?'V2ʵ����֤':'V3������֤');
//$tpl->assign('message',$lv == 2?'':'�ڽ���v3��֤ǰ��ԼԼ�������������v2��֤Ŷ��лл');
$tpl->assign('head_html',$wap_global_top);
$tpl->assign('pay_recharge.php',$__is_weixin);

if($get_lv == 'V3' && $lv == 2)
{
	echo '<script>alert("�ڽ���v3��֤ǰ��ԼԼ�������������v2��֤Ŷ��лл")</script>';
}
elseif($get_lv == 'V2' && $lv == 3)
{
	echo '<script>alert("���Ѿ���֤��V2��ԼԼ�������������v3��֤Ŷ��лл")</script>';
}

$tpl->output();


	
?>