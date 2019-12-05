<?php 
/**
 * ������Դ�ļ���λ��ע�⣡ȷ������·����ȡ
 */
$file_dir = dirname(__FILE__);
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$seller_id = (int)$_INPUT['seller_id'];
$pwd = $_INPUT['pwd'];

if($seller_id && $pwd)
{
	// ����¼����
	$payment_obj = POCO::singleton('pai_payment_class');
	$user_obj = POCO::singleton('pai_user_class');

	$user_info = $user_obj->get_user_info($seller_id);

	if(!$user_info)
	{

		$output_arr['message'] = '���˺Ų�����';
		$output_arr['code'] = 0;
		$output_arr['data'] = '';
		mobile_output($output_arr,false); 
		exit();
	}


	$user_id = $user_obj->user_login($user_info['cellphone'], $pwd);

	if(!$user_id)
	{
		$output_arr['message'] = '�˺Ż��������';
		$output_arr['code'] = 0;
		$output_arr['data'] = '';
		mobile_output($output_arr,false); 
		exit();
	}


	$check_seller = $payment_obj->get_card_seller_info($user_id);

	if(!$check_seller)
	{
		$output_arr['message'] = '�˺Ų����̼��˺�';
		$output_arr['code'] = 0;
		$output_arr['data'] = '';
		mobile_output($output_arr,false); 
		exit();
	}
	else
	{
		$check = $payment_obj->check_is_card_seller($user_id);
		if(!$check)
		{
			$output_arr['message'] = '���˺���ͣ��';
			$output_arr['code'] = 0;
			$output_arr['data'] = '';
			mobile_output($output_arr,false); 
			exit();
		}
	
		setcookie("yue_seller_admin", 1, time()+600, "/", "yueus.com");
		//��¼�ɹ�
		$output_arr['message'] = '��¼�ɹ�';
		$output_arr['code'] = 1;
		$output_arr['data'] = '';
		mobile_output($output_arr,false); 
	}
}
else
{
	if($yue_login_id && $_COOKIE['yue_seller_admin'])
	{
		header("Location: list.php");
	}

	include_once($file_dir. '/./webcontrol/head.php');
	$tpl = $my_app_pai->getView('login.tpl.html');
	// ������ʽ��js����
	$head_html = _get_wbc_head();

	$tpl ->assign('head_html',$head_html);
	$tpl->output();
}




?>