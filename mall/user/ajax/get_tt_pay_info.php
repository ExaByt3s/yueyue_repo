<?php

/**
 *  ��ȡtt֧����Ϣ
 */

include_once('../common.inc.php');

// Ȩ�޼��
mall_check_user_permissions($yue_login_id);

if(empty($yue_login_id))
{
	$output_arr['code'] = -1;
	$output_arr['msg']  = '��δ��¼,�Ƿ�����';
	$output_arr['data'] = array();
	exit();
}

$order_sn = trim($_INPUT['order_sn']);


// �����ࣺpai_mall_order_class
$mall_order_obj = POCO::singleton('pai_mall_order_class');

//��ȡ������Ϣ

/**
* ��ȡ������Ϣ
* @param string $order_sn
* @return array
*/	 
// 13241194
$order_full_info = $mall_order_obj->get_order_full_info($order_sn);

$detail_list = $order_full_info['detail_list'];

// ������Ϣ
$goods_promotion_info = $order_full_info['goods_promotion_info'];
$detail_info['goods_promotion_info'] = '<span style="color:#fe9220;" class="ml5">'.$goods_promotion_info['type_name'].
'</span><span class="ml15">��ʡ��<label style="color:#fe9220;">'.$goods_promotion_info['cal_save_amount'].'</label></span>';

//��ȡ������Ϣ
$obj = POCO::singleton('pai_user_class');
$ret = $obj->get_user_info_by_user_id($yue_login_id);

$detail_info['user_id'] = $ret['user_id'];
$detail_info['available_balance'] =  $ret['available_balance']; //$ret['available_balance'];
$detail_info['bail_available_balance'] = $ret['bail_available_balance'];
$detail_info['balance'] = $ret['balance'];  

/**
 * �жϿͻ���
 */
$__is_weixin = stripos($_SERVER['HTTP_USER_AGENT'], 'micromessenger') ? true : false;
$__is_android = stripos($_SERVER['HTTP_USER_AGENT'], 'android') ? true : false;
$__is_iphone = stripos($_SERVER['HTTP_USER_AGENT'], 'iphone') ? true : false;  
$__is_yueyue_app = (preg_match('/yue_pai/',$_SERVER['HTTP_USER_AGENT'])) ? true : false; 

$detail_info['is_weixin'] = $__is_weixin;
$detail_info['is_yueyue_app'] = $__is_yueyue_app;    

if(!$__is_yueyue_app && !$__is_weixin )
{
	$detail_info['is_weixin'] = false;
	$detail_info['is_yueyue_app'] = false;	
	$detail_info['is_zfb_wap'] = true;    
}

// ���������б�
$detail_info['detail_list'] = $detail_list;  
// ֧���ܼ�
$detail_info['total_amount'] = $order_full_info['total_amount'];

$output_arr['code'] = 1;
$output_arr['msg']  = '';
$output_arr['data'] = $detail_info;

 
mall_mobile_output($output_arr,false);

?>