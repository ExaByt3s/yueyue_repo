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
 * �����б�
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

$order_sn = intval($_INPUT['order_sn']);

$ret = $mall_order_obj -> ready_pay_order($order_sn,$yue_login_id);

$output_arr['data'] = $ret;

mall_mobile_output($output_arr,false);

?>