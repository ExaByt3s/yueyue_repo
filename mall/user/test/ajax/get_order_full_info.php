<?php
/**
 * 
 *
 * @author hudingwen
 * @version 1.0
 * @copyright , 30 June, 2015
 * @package default
 */	
	
/**
 * ��ȡ��������������Ϣ
 */	

include_once('../common.inc.php');

// û�е�¼�Ĵ���
if(empty($yue_login_id))
{
	$output_arr['code'] = -1;
	$output_arr['msg']  = '��δ��¼,�Ƿ�����';
	$output_arr['data'] = array();
	exit();
}

// ������
$mall_order_obj = POCO::singleton('pai_mall_order_class');

// ����sn
$order_sn = trim($_INPUT['order_sn']);

// ��ȡ��������
$ret = $mall_order_obj->get_order_full_info($order_sn);

if($ret)
{
	$output_arr['code'] = 1;
	$output_arr['msg']  = 'Success';
}
else
{
	$output_arr['code'] = 0;
	$output_arr['msg']  = 'Error';
}

$output_arr['data'] = $ret;

mall_mobile_output($output_arr,false);	
?>