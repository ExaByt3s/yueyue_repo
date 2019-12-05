<?php
/**
 * 
 *
 * @author hudingwen
 * @version $Id$
 * @copyright , 14 July, 2015
 * @package default
 */

/**
 * Define �ύ����
 */
define('G_SIMPLE_INPUT_CLEAN_VALUE',1);
include_once('../common.inc.php');	

$order_sn = trim($_INPUT['order_sn']);
$table_id = trim($_INPUT['table_id']);

// �����������̼�
if(!empty($order_sn))
{
	// �����ࣺpai_mall_order_class
	$mall_order_obj = POCO::singleton('pai_mall_order_class');

	// ������
	$mall_comment_obj = POCO::singleton('pai_mall_comment_class');

	// ��ѯ��������

	/**
	* ��ȡ������Ϣ
	* @param string $order_sn
	* @return array
	*/
	$order_full_info = $mall_order_obj->get_order_full_info($order_sn);
    

	$insert_data['from_user_id'] = $yue_login_id;  //�������û�ID
	$insert_data['to_user_id'] =  $order_full_info['seller_user_id']; //���������û�ID
	$insert_data['order_id'] = $order_full_info['order_id'];
	
	$insert_data['overall_score'] = intval($_INPUT['overall_score']); //�������۷���
	$insert_data['match_score'] = intval($_INPUT['match_score']); //��Ʒ���Ϸ���
	$insert_data['manner_score'] = intval($_INPUT['manner_score']); //̬������
	$insert_data['quality_score'] = intval($_INPUT['quality_score']);  //��������
	$insert_data['comment'] = iconv("UTF-8", "gbk//TRANSLIT", trim($_INPUT['comment']));  //��������
	$insert_data['is_anonymous'] = intval($_INPUT['is_anonymous']);  //���� 1Ϊtrue

	// ���ڻ����
	if($order_full_info['order_type'] == 'activity')
	{
		$insert_data['stage_id'] = $order_full_info['activity_list'][0]['stage_id'];
		$insert_data['goods_id'] = $order_full_info['activity_list'][0]['activity_id'];

		if($yue_login_id == 100000)
		{
			//print_r($insert_data);
			//die();
		}
		/*
		 * ���������̼�
		 */
		$ret = $mall_comment_obj->add_seller_comment($insert_data);
		
	}
	// �����̳�����
	elseif($order_full_info['order_type'] == 'detail')
	{
		$insert_data['goods_id'] = $order_full_info['detail_list'][0]['goods_id'];

		/*
		 * ���������̼�
		 */
		$ret = $mall_comment_obj->add_seller_comment($insert_data);
	}
	// �����渶
	elseif($order_full_info['order_type'] == 'payment')
	{
		if($yue_login_id == $order_full_info['seller_user_id'])
		{
			$insert_data['from_user_id'] = $yue_login_id;  //�������û�ID
			$insert_data['to_user_id'] =  $order_full_info['buyer_user_id']; //���������û�ID
			/*
			 * �̼����������ߣ������渶
			 */
			$ret = $mall_comment_obj->add_buyer_comment($insert_data);
		} 
		else
		{
			/*
			 * ���������̼�
			 */
			$ret = $mall_comment_obj->add_seller_comment($insert_data);
		}
		
	}

	

	$output_arr['code'] = $ret['result'];
	
	$output_arr['msg'] = $ret['message'];
	
	if($output_arr['code'] == 1)
	{
		$redirect_url = trim($_INPUT['redirect_url']);

		if($order_full_info['order_type'] == 'payment')
		{
			$output_arr['data'] = array(
				'url'=>urldecode($redirect_url)
			);
		}	
		else
		{
			$output_arr['data'] = array(
				'url'=>'../order/detail.php?order_sn='.$order_sn
			);
		}
		
		
	}
	else
	{
		$output_arr['data'] = array();
	}
}
// ���ۻ
else if(!empty($table_id))
{
	
	$output_arr['code'] = -1;
	
	$output_arr['msg'] = '�ɰ����۽ӿ�������';

	$output_arr['data'] = array();
}










mall_mobile_output($output_arr,false);

?>