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
$control_type = $_INPUT['type'];



switch($control_type)
{
	case 'close' : $ret = $mall_order_obj->close_order_for_buyer($order_sn, $yue_login_id);break;
	case 'cancel' : $ret = $mall_order_obj->close_order_for_buyer($order_sn, $yue_login_id);break;
	case 'delete' : $ret = $mall_order_obj->del_order_for_buyer($order_sn, $yue_login_id);break;
	case 'refund' : $ret = $mall_order_obj->refund_order_for_buyer($order_sn, $yue_login_id);break;
}


$output_arr['data'] = $ret;

mall_mobile_output($output_arr,false);

?>