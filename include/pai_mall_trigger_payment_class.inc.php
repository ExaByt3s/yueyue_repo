<?php
/**
 * �¼�����
 * �������淶��ģ������_�¼�_ǰ/��
 * @author Henry
 * @copyright 2015-11-16
 */

class pai_mall_trigger_payment_class
{
	/**
	 * ���캯��
	 */
	public function __construct()
	{
	}
	
	/**
	 * ����_�ύ_��
	 * @param array $params
	 * @return boolean
	 * $params = array(
	 *   'order_sn' => '',
	 * );
	 */
	public function submit_order_after($params)
	{
		//��ȡ������Ϣ
		$order_payment_obj = POCO::singleton('pai_mall_order_payment_class');
		$order_sn = trim($params['order_sn']);
		$order_info = $order_payment_obj->get_order_full_info($order_sn);
		if( empty($order_info) )
		{
			return false;
		}
		$order_id = intval($order_info['order_id']);
		$order_type = trim($order_info['order_type']);
		$buyer_user_id = intval($order_info['buyer_user_id']);
		$seller_user_id = intval($order_info['seller_user_id']);
		
		/*********���¶���ͳ�Ʊ���***********/
		$comment_info = '';
		POCO::singleton('pai_mall_order_log_class')->add_order_log('submit_order', $order_id, $buyer_user_id, $seller_user_id, $order_info, $comment_info);
		
		return true;
	}
	
	/**
	 * ���֧��_����_��
	 * @param array $params
	 * @return boolean
	 * @tutorial
	 * $params = array(
	 *   'order_sn' => '',
	 * );
	 */
	public function pay_order_after($params)
	{
		//��ȡ������Ϣ
		$order_payment_obj = POCO::singleton('pai_mall_order_payment_class');
		$order_sn = trim($params['order_sn']);
		$order_info = $order_payment_obj->get_order_full_info($order_sn);
		if( empty($order_info) )
		{
			return false;
		}
		$order_id = intval($order_info['order_id']);
		$order_type = trim($order_info['order_type']);
		$buyer_user_id = intval($order_info['buyer_user_id']);
		$seller_user_id = intval($order_info['seller_user_id']);
		
		//��OA�����ŷ�֪ͨ
		if( !in_array($order_info['referer'], array('oa')) )
		{
			//��� => �̼�
			$send_data = array();
			$send_data['media_type'] = 'card';
			$send_data['card_style'] = 1;
			$send_data['card_text1'] = '������ֱ�Ӹ���';
			$send_data['card_text2'] = '��' . $order_info['total_amount'];
			$send_data['card_title'] = '�ѵ���';
			
			//�̼� => ���
			$to_send_data = array();
			$to_send_data['media_type'] = 'card';
			$to_send_data['card_style'] = 1;
			$to_send_data['card_text1'] = '�ѶԸ��̼ҽ���ֱ�Ӹ���';
			$to_send_data['card_text2'] = '��' . $order_info['total_amount'];
			$to_send_data['card_title'] = '֧���ɹ�';
			
			POCO::singleton('pai_information_push')->card_send_for_order($buyer_user_id, 'yuebuyer', $seller_user_id, 'yueseller', $send_data, $to_send_data, $order_sn, $order_type);
		}
		
		/*********���¶���ͳ�Ʊ���***********/
		$comment_info = '';
		POCO::singleton('pai_mall_order_log_class')->add_order_log('pay_order', $order_id, $buyer_user_id, $seller_user_id, $order_info, $comment_info);
		
		return true;
	}
	
	/**
	 * �������_����_��
	 * @param array $params array('order_sn'=>'', 'is_anonymous'=>0)
	 * @return boolean
	 */
	public function comment_order_for_buyer_after($params)
	{
		//��ȡ������Ϣ
		$order_payment_obj = POCO::singleton('pai_mall_order_payment_class');
		$order_sn = trim($params['order_sn']);
		$order_info = $order_payment_obj->get_order_full_info($order_sn);
		if( empty($order_info) )
		{
			return false;
		}
		$order_id = intval($order_info['order_id']);
		$order_type = trim($order_info['order_type']);
		$buyer_user_id = intval($order_info['buyer_user_id']);
		$seller_user_id = intval($order_info['seller_user_id']);
		
		/*********���¶���ͳ�Ʊ���***********/
		//��ȡ�����߶��̼Ҷ�������Ʒ���ۣ�������ע���£��Ͷ��������߼���ͬ��
		$comment_info = POCO::singleton('pai_mall_comment_class')->get_seller_comment_info($order_id, 0);
		POCO::singleton('pai_mall_order_log_class')->add_order_log('comment_order_for_buyer', $order_id, $buyer_user_id, $seller_user_id, $order_info, $comment_info);
		
		return true;
	}
	
	/**
	 * ��������_����_��
	 * @param array $params array('order_sn'=>'','is_anonymous'=>0)
	 * @return boolean
	 */
	public function comment_order_for_seller_after($params)
	{
		//��ȡ������Ϣ
		$order_payment_obj = POCO::singleton('pai_mall_order_payment_class');
		$order_sn = trim($params['order_sn']);
		$order_info = $order_payment_obj->get_order_full_info($order_sn);
		if( empty($order_info) )
		{
			return false;
		}
		$order_id = intval($order_info['order_id']);
		$order_type = trim($order_info['order_type']);
		$buyer_user_id = intval($order_info['buyer_user_id']);
		$seller_user_id = intval($order_info['seller_user_id']);
		
		/*********���¶���ͳ�Ʊ���***********/
		//��ȡ�̼Ҷ������߶�������Ʒ����
		$comment_info = POCO::singleton('pai_mall_comment_class')->get_buyer_comment_info($order_id, 0);
		POCO::singleton('pai_mall_order_log_class')->add_order_log('comment_order_for_seller', $order_id, $buyer_user_id, $seller_user_id, $order_info, $comment_info);
		
		return true;
	}
	
}
