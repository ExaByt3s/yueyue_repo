<?php
/**
 * �¼�����
 * �������淶��ģ������_�¼�_ǰ/��
 * @author Henry
 * @copyright 2015-04-16
 */

class pai_mall_trigger_activity_class
{
	/**
	 * ���캯��
	 */
	public function __construct()
	{
	}

	/**
	 * �����_�ύ_��
	 * @param array $params
	 * @return boolean
	 * $params = array(
	 *   'order_sn' => '',
	 * );
	 */
	public function submit_order_after($params)
	{
		//��ȡ������Ϣ
		$order_obj = POCO::singleton('pai_mall_order_class');
		$order_sn = trim($params['order_sn']);
		$order_info = $order_obj->get_order_full_info($order_sn);
		if( empty($order_info) )
		{
			return false;
		}
		$order_id = intval($order_info['order_id']);
		$activity_info = $order_info['activity_list'][0];
		$buyer_user_id = $order_info['buyer_user_id'];
		$seller_user_id = $order_info['seller_user_id'];
		//��OA�����ŷ�֪ͨ
		if( !in_array($order_info['referer'], array('oa', 'import')) )
		{
			//������Ϣ
			//��� => �̼�
			$send_data = array();
			$send_data['media_type'] = 'card';
			$send_data['card_style'] = 1;
			$send_data['card_text1'] = "�µ��˻: " . $activity_info['activity_name'];
			$send_data['card_text2'] = '��' . $order_info['total_amount'];
			$send_data['card_title'] = '�鿴��������';

			//�̼� => ���
			$to_send_data = array();
			$to_send_data['media_type'] = 'card';
			$to_send_data['card_style'] = 1;
			$to_send_data['card_text1'] = "�µ��˻: " . $activity_info['activity_name'];
			$to_send_data['card_text2'] = '��' . $order_info['total_amount'];
			$to_send_data['card_title'] = '�ȴ�֧��';

			POCO::singleton('pai_information_push')->card_send_for_order($buyer_user_id, 'yuebuyer', $seller_user_id, 'yueseller', $send_data, $to_send_data, $order_sn);

			//΢�Ź��ں�ģ����Ϣ
			$template_data = array(
				'first' => '�����µ��ɹ�����֧��',
				'goods_name' => $order_info['activity_name'],
				'total_amount' => $order_info['total_amount'],
				'status_str' => $order_info['status_str'],
				'remark' => '������֧��',
			);
			$template_to_url = 'http://yp.yueus.com/mall/user/order/detail.php?order_sn=' . $order_sn;
			POCO::singleton('pai_weixin_pub_class')->message_template_send_by_user_id($buyer_user_id, 'G_PAI_WEIXIN_MALL_ORDER_STATUS', $template_data, $template_to_url);
		}

		/*********������Ʒ�۸����***********/
		$activity_id = intval($activity_info['activity_id']);
		if( $activity_id>0 )
		{
			$data = array(
				'bill_buy_num' => 1,
			);
			POCO::singleton('pai_mall_goods_class')->update_goods_statistical($activity_id, $data);
		}

		/*********���¶���ͳ�Ʊ���***********/
		$comment_info = '';
		POCO::singleton('pai_mall_order_log_class')->add_order_log('submit_order',$order_id,$buyer_user_id,$seller_user_id,$order_info,$comment_info);

		return true;
	}

	/**
	 * ����_�ļ�_��
	 * @param $params
	 * @return bool
	 */
	public function change_order_price($params)
	{
		//��ȡ������Ϣ
		$order_obj = POCO::singleton('pai_mall_order_class');
		$order_sn = trim($params['order_sn']);
		$order_info = $order_obj->get_order_full_info($order_sn);
		if( empty($order_info) )
		{
			return false;
		}
		$order_id = intval($order_info['order_id']);
		$activity_info = $order_info['activity_list'][0];
		$buyer_user_id = $order_info['buyer_user_id'];
		$seller_user_id = $order_info['seller_user_id'];
		//��OA�����ŷ�֪ͨ
		if( !in_array($order_info['referer'], array('oa', 'import')) )
		{
			//��� => �̼�
			$to_send_data = array();
			$to_send_data['media_type'] = 'card';
			$to_send_data['card_style'] = 1;
			$to_send_data['card_text1'] = "�޸��˶����۸�: " . $activity_info['activity_name'];
			$to_send_data['card_text2'] = '��' . $order_info['total_amount'];
			$to_send_data['card_title'] = '�鿴��������';

			//�̼� => ���
			$send_data = array();
			$send_data['media_type'] = 'card';
			$send_data['card_style'] = 1;
			$send_data['card_text1'] = "�޸��˶����۸�: " . $activity_info['activity_name'];
			$send_data['card_text2'] = '��' . $order_info['total_amount'];
			$send_data['card_title'] = '�ȴ�֧��';

			POCO::singleton('pai_information_push')->card_send_for_order($seller_user_id, 'yueseller', $buyer_user_id, 'yuebuyer', $send_data, $to_send_data, $order_sn);

			//΢�Ź��ں�ģ����Ϣ
			$template_data = array(
				'first' => '�̼��޸��˶����۸�',
				'goods_name' => $order_info['activity_name'],
				'total_amount' => $order_info['total_amount'],
				'status_str' => $order_info['status_str'],
				'remark' => '����鿴����',
			);
			$template_to_url = 'http://yp.yueus.com/mall/user/order/detail.php?order_sn=' . $order_sn;
			POCO::singleton('pai_weixin_pub_class')->message_template_send_by_user_id($buyer_user_id, 'G_PAI_WEIXIN_MALL_ORDER_STATUS', $template_data, $template_to_url);
		}

		/*********���¶���ͳ�Ʊ���***********/
		$comment_info = '';
		POCO::singleton('pai_mall_order_log_class')->add_order_log('change_order_price',$order_id,$buyer_user_id,$seller_user_id,$order_info,$comment_info);

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
		$order_obj = POCO::singleton('pai_mall_order_class');
		$order_sn = trim($params['order_sn']);
		$order_info = $order_obj->get_order_full_info($order_sn);
		if( empty($order_info) )
		{
			return false;
		}
		$order_id = intval($order_info['order_id']);
		$activity_info = $order_info['activity_list'][0];
		$buyer_user_id = $order_info['buyer_user_id'];
		$seller_user_id = $order_info['seller_user_id'];

		//��OA�����ŷ�֪ͨ
		if( !in_array($order_info['referer'], array('oa', 'import')) && $order_info['is_auto_accept']==0 )
		{
			//�̼� => ���
			$send_data = array();
			$send_data['media_type'] = 'card';
			$send_data['card_style'] = 2;
			$send_data['card_text1'] = '��֧������,��׼ʱ���ֳ�ɨ��ǩ��';
			$send_data['card_title'] = '��ʾǩ����';

			//��� => �̼�
			$to_send_data = array();
			$to_send_data['media_type'] = 'card';
			$to_send_data['card_style'] = 2;
			$to_send_data['card_text1'] = '��֧������,������ɨ��ǩ��ȷ���տ�';
			$to_send_data['card_title'] = 'ɨ��ǩ��';

			POCO::singleton('pai_information_push')->card_send_for_order($seller_user_id, 'yueseller', $buyer_user_id, 'yuebuyer', $send_data, $to_send_data, $order_sn);

			//΢�Ź��ں�ģ����Ϣ
			$template_data = array(
				'first' => '������֧������׼ʱ���ֳ�ɨ��ǩ��������ɽ���',
				'goods_name' => $order_info['activity_name'],
				'total_amount' => $order_info['total_amount'],
				'status_str' => $order_info['status_str'],
				'remark' => '�����ʾǩ����',
			);
			$template_to_url = 'http://yp.yueus.com/mall/user/order/detail.php?order_sn=' . $order_sn;
			POCO::singleton('pai_weixin_pub_class')->message_template_send_by_user_id($buyer_user_id, 'G_PAI_WEIXIN_MALL_ORDER_STATUS', $template_data, $template_to_url);
		}

		/*********������Ʒ�۸����***********/
		$activity_id = intval($activity_info['activity_id']);
		if( $activity_id>0 )
		{
			$data = array(
				'bill_pay_num' => 1,
			);
			POCO::singleton('pai_mall_goods_class')->update_goods_statistical($activity_id, $data);
		}

		/*********���¶���ͳ�Ʊ���***********/
		$comment_info = '';
		POCO::singleton('pai_mall_order_log_class')->add_order_log('pay_order',$order_id,$buyer_user_id,$seller_user_id,$order_info,$comment_info);

		return true;
	}

	/**
	 * ǩ��_����_��
	 * @param array $params array('order_sn'=>'')
	 * @return boolean
	 */
	public function sign_order_after($params)
	{
		//��ȡ������Ϣ
		$order_obj = POCO::singleton('pai_mall_order_class');
		$order_sn = trim($params['order_sn']);
		$order_info = $order_obj->get_order_full_info($order_sn);
		if( empty($order_info) )
		{
			return false;
		}
        $cur_time = time();
		$order_id = intval($order_info['order_id']);
		$activity_info = $order_info['activity_list'][0];
		$buyer_user_id = $order_info['buyer_user_id'];
		$seller_user_id = $order_info['seller_user_id'];
		$order_pending_amount = $order_info['pending_amount'];

		//��OA�����ŷ�֪ͨ
		if( !in_array($order_info['referer'], array('oa', 'import')) )
		{
			//�̼� => ���
			$send_data = array();
			$send_data['media_type'] = 'card';
			$send_data['card_style'] = 2;
			$send_data['card_text1'] = '�ѳɹ�ǩ�������ɺ��������̼�Ŷ';
			$send_data['card_title'] = '�����̼�';
            if($order_pending_amount >= 100 && $cur_time>=strtotime('2015-11-10 00:00:00') && $cur_time<=strtotime('2015-12-10 23:59:59'))
            {
                $send_data['card_text1'] = '�ѳɹ�ǩ����������ۺ���߿ɵ�100Ԫ�Ż�ȯ';
                $send_data['card_title'] = '�������ۣ�100%���Ż�ȯ';
            }

			//��� => �̼�
			$to_send_data = array();
			$to_send_data['media_type'] = 'card';
			$to_send_data['card_style'] = 2;
			$to_send_data['card_text1'] = "�Ѻ�{$order_info['buyer_name']}ǩ�����˻������յ�����";
			$to_send_data['card_title'] = 'ȥ�������';

			POCO::singleton('pai_information_push')->card_send_for_order($seller_user_id, 'yueseller', $buyer_user_id, 'yuebuyer', $send_data, $to_send_data, $order_sn);

			//΢�Ź��ں�ģ����Ϣ
			$template_data = array(
				'first' => "�ѳɹ�ǩ�����������\r\n���ɺ��������̼�Ŷ",
				'goods_name' => $order_info['activity_name'],
				'total_amount' => $order_info['total_amount'],
				'status_str' => $order_info['status_str'],
				'remark' => '��������̼�',
			);
			$template_to_url = 'http://yp.yueus.com/mall/user/order/detail.php?order_sn=' . $order_sn;
			POCO::singleton('pai_weixin_pub_class')->message_template_send_by_user_id($buyer_user_id, 'G_PAI_WEIXIN_MALL_ORDER_STATUS', $template_data, $template_to_url);
		}

		/*********������Ʒ�۸����***********/
		$activity_id = intval($activity_info['activity_id']);
		$price = $activity_info['prices'];
		if( $activity_id>0 )
		{
			$data = array(
				'bill_finish_num' => 1,
				'prices' => $price,
			);
			POCO::singleton('pai_mall_goods_class')->update_goods_statistical($activity_id, $data);
		}

		/*************�����û���������**************/
		$user_obj = POCO::singleton('pai_user_data_class');
		$user_obj->add_deal_times($buyer_user_id);
		$user_obj->add_consume_ammount($buyer_user_id, $order_info['pending_amount']);

		/*********���¶���ͳ�Ʊ���***********/
		$comment_info = '';
		POCO::singleton('pai_mall_order_log_class')->add_order_log('sign_order',$order_id,$buyer_user_id,$seller_user_id,$order_info,$comment_info);

		/*********���¶���ͳ�Ʊ���***********/
		$add_user_deal = POCO::singleton('pai_mall_follow_user_class')->add_user_deal($buyer_user_id, $seller_user_id);
		pai_log_class::add_log($add_user_deal, '$cal_quantity_sku_info', 'order_service');

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
		$order_obj = POCO::singleton('pai_mall_order_class');
		$order_sn = trim($params['order_sn']);
		$order_info = $order_obj->get_order_full_info($order_sn);
		if( empty($order_info) )
		{
			return false;
		}
		$order_id = intval($order_info['order_id']);
		$type_id = intval($order_info['type_id']);
		$activity_info = $order_info['activity_list'][0];
		$buyer_user_id = $order_info['buyer_user_id'];
		$seller_user_id = $order_info['seller_user_id'];
		$order_total_amount = $order_info['total_amount'];
		$order_pending_amount = $order_info['pending_amount'];

		$cur_time = time();

		//����������OA�����ŷ�֪ͨ
		if( $params['is_anonymous']==0 && !in_array($order_info['referer'], array('oa', 'import')) )
		{
			//��� => �̼�
			$send_data = array();
			$send_data['media_type'] = 'card';
			$send_data['card_style'] = 2;
			$send_data['card_text1'] = '��������';
			$send_data['card_title'] = '�鿴��������';

			//�̼� => ���
			$to_send_data = array();
			$to_send_data['media_type'] = 'card';
			$to_send_data['card_style'] = 2;
			$to_send_data['card_text1'] = "������{$order_info['seller_name']}";
			$to_send_data['card_title'] = '�鿴��������';

			POCO::singleton('pai_information_push')->card_send_for_order($buyer_user_id, 'yuebuyer', $seller_user_id, 'yueseller', $send_data, $to_send_data, $order_sn);

			//΢�Ź��ں�ģ����Ϣ
			$template_data = array(
				'first' => "����������{$order_info['seller_name']}",
				'goods_name' => $order_info['activity_name'],
				'total_amount' => $order_info['total_amount'],
				'status_str' => $order_info['status_str'],
				'remark' => '����鿴����',
			);
			$template_to_url = 'http://yp.yueus.com/mall/user/order/detail.php?order_sn=' . $order_sn;
			POCO::singleton('pai_weixin_pub_class')->message_template_send_by_user_id($buyer_user_id, 'G_PAI_WEIXIN_MALL_ORDER_STATUS', $template_data, $template_to_url);
		}

		/*********���¶���ͳ�Ʊ���***********/
		//��ȡ�����߶��̼Ҷ�������Ʒ���ۣ�������ע���£��Ͷ��������߼���ͬ��
		$comment_info = POCO::singleton('pai_mall_comment_class')->get_seller_comment_info($order_id,$activity_info['activity_id']);
		POCO::singleton('pai_mall_order_log_class')->add_order_log('comment_order_for_buyer',$order_id,$buyer_user_id,$seller_user_id,$order_info,$comment_info);
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
		$order_obj = POCO::singleton('pai_mall_order_class');
		$order_sn = trim($params['order_sn']);
		$order_info = $order_obj->get_order_full_info($order_sn);
		if( empty($order_info) )
		{
			return false;
		}
		$order_id = intval($order_info['order_id']);
		$activity_info = $order_info['activity_list'][0];
		$buyer_user_id = $order_info['buyer_user_id'];
		$seller_user_id = $order_info['seller_user_id'];

		//����������OA�����ŷ�֪ͨ
		if( $params['is_anonymous']==0 && !in_array($order_info['referer'], array('oa', 'import')) )
		{
			//�̼� => ���
			$send_data = array();
			$send_data['media_type'] = 'card';
			$send_data['card_style'] = 2;
			$send_data['card_text1'] = '��������';
			$send_data['card_title'] = '�鿴��������';

			//��� => �̼�
			$to_send_data = array();
			$to_send_data['media_type'] = 'card';
			$to_send_data['card_style'] = 2;
			$to_send_data['card_text1'] = "������{$order_info['buyer_name']}";
			$to_send_data['card_title'] = '�鿴��������';

			POCO::singleton('pai_information_push')->card_send_for_order($seller_user_id, 'yueseller', $buyer_user_id, 'yuebuyer', $send_data, $to_send_data, $order_sn);

			//΢�Ź��ں�ģ����Ϣ
			$order_full_info = $order_obj->get_order_full_info($order_sn);
			$seller_name = get_user_nickname_by_user_id($seller_user_id);
			$template_data = array(
				'first' => "{$seller_name}��������",
				'goods_name' => $order_info['activity_name'],
				'total_amount' => $order_info['total_amount'],
				'status_str' => $order_info['status_str'],
				'remark' => '����鿴����',
			);
			$template_to_url = 'http://yp.yueus.com/mall/user/order/detail.php?order_sn=' . $order_sn;
			POCO::singleton('pai_weixin_pub_class')->message_template_send_by_user_id($buyer_user_id, 'G_PAI_WEIXIN_MALL_ORDER_STATUS', $template_data, $template_to_url);
		}

		/*********���¶���ͳ�Ʊ���***********/
		//��ȡ�̼Ҷ������߶�������Ʒ����
		$comment_info = POCO::singleton('pai_mall_comment_class')->get_buyer_comment_info($order_id,$activity_info['activity_id']);
		POCO::singleton('pai_mall_order_log_class')->add_order_log('comment_order_for_seller',$order_id,$buyer_user_id,$seller_user_id,$order_info,$comment_info);

		return true;
	}

	/**
	 * ��ҹر�_��֧������_��
	 * @param array $params
	 * @return boolean
	 * @tutorial
	 * $params = array(
	 *   'order_sn' => '',
	 * );
	 */
	public function close_wait_pay_order_for_buyer_after($params)
	{
		//��ȡ������Ϣ
		$order_obj = POCO::singleton('pai_mall_order_class');
		$order_sn = trim($params['order_sn']);
		$order_info = $order_obj->get_order_full_info($order_sn);
		if( empty($order_info) )
		{
			return false;
		}
		$order_id = intval($order_info['order_id']);
		$activity_info = $order_info['activity_list'][0];
		$buyer_user_id = $order_info['buyer_user_id'];
		$seller_user_id = $order_info['seller_user_id'];

		//��OA�����ŷ�֪ͨ
		if( !in_array($order_info['referer'], array('oa', 'import')) )
		{
			//��� => �̼�
			$send_data = array();
			$send_data['media_type'] = 'card';
			$send_data['card_style'] = 2;
			$send_data['card_text1'] = '�ѹرն���';
			$send_data['card_title'] = '����鿴����';

			//�̼� => ���
			$to_send_data = array();
			$to_send_data['media_type'] = 'card';
			$to_send_data['card_style'] = 2;
			$to_send_data['card_text1'] = '�ѹرն���';
			$to_send_data['card_title'] = '����鿴����';

			POCO::singleton('pai_information_push')->card_send_for_order($buyer_user_id, 'yuebuyer', $seller_user_id, 'yueseller', $send_data, $to_send_data, $order_sn);

			//΢�Ź��ں�ģ����Ϣ
			$template_data = array(
				'first' => '���ѹرն���',
				'goods_name' => $order_info['activity_name'],
				'total_amount' => $order_info['total_amount'],
				'status_str' => $order_info['status_str'],
				'remark' => '����鿴����',
			);
			$template_to_url = 'http://yp.yueus.com/mall/user/order/detail.php?order_sn=' . $order_sn;
			POCO::singleton('pai_weixin_pub_class')->message_template_send_by_user_id($buyer_user_id, 'G_PAI_WEIXIN_MALL_ORDER_STATUS', $template_data, $template_to_url);
		}

		/*********���¶���ͳ�Ʊ���***********/
		$comment_info = '';
		POCO::singleton('pai_mall_order_log_class')->add_order_log('close_wait_pay_order_for_buyer',$order_id,$buyer_user_id,$seller_user_id,$order_info,$comment_info);

		return true;
	}

	/**
	 * ���ҹر�_��֧������_��
	 * @param array $params
	 * @return boolean
	 * @tutorial
	 * $params = array(
	 *   'order_sn' => '',
	 * );
	 */
	public function close_wait_pay_order_for_seller_after($params)
	{
		//��ȡ������Ϣ
		$order_obj = POCO::singleton('pai_mall_order_class');
		$order_sn = trim($params['order_sn']);
		$order_info = $order_obj->get_order_full_info($order_sn);
		if( empty($order_info) )
		{
			return false;
		}
		$order_id = intval($order_info['order_id']);
		$activity_info = $order_info['activity_list'][0];
		$buyer_user_id = $order_info['buyer_user_id'];
		$seller_user_id = $order_info['seller_user_id'];

		//��OA�����ŷ�֪ͨ
		if( !in_array($order_info['referer'], array('oa', 'import')) )
		{
			//�̼� => ���
			$send_data = array();
			$send_data['media_type'] = 'card';
			$send_data['card_style'] = 2;
			$send_data['card_text1'] = '�ѹرն���';
			$send_data['card_title'] = '����鿴����';

			//��� => �̼�
			$to_send_data = array();
			$to_send_data['media_type'] = 'card';
			$to_send_data['card_style'] = 2;
			$to_send_data['card_text1'] = '�ѹرն���';
			$to_send_data['card_title'] = '����鿴����';

			POCO::singleton('pai_information_push')->card_send_for_order($seller_user_id, 'yueseller', $buyer_user_id, 'yuebuyer', $send_data, $to_send_data, $order_sn);

			//΢�Ź��ں�ģ����Ϣ
			$template_data = array(
				'first' => '�̼��ѹرն���',
				'goods_name' => $order_info['activity_name'],
				'total_amount' => $order_info['total_amount'],
				'status_str' => $order_info['status_str'],
				'remark' => '����鿴����',
			);
			$template_to_url = 'http://yp.yueus.com/mall/user/order/detail.php?order_sn=' . $order_sn;
			POCO::singleton('pai_weixin_pub_class')->message_template_send_by_user_id($buyer_user_id, 'G_PAI_WEIXIN_MALL_ORDER_STATUS', $template_data, $template_to_url);
		}

		/*********���¶���ͳ�Ʊ���***********/
		$comment_info = '';
		POCO::singleton('pai_mall_order_log_class')->add_order_log('close_wait_pay_order_for_seller',$order_id,$buyer_user_id,$seller_user_id,$order_info,$comment_info);

		return true;
	}

	/**
	 * ��ҹر�_��ȷ�϶���_��
	 * @param array $params
	 * @return boolean
	 * @tutorial
	 * $params = array(
	 *   'order_sn' => '',
	 * );
	 */
	public function close_wait_confirm_order_for_buyer_after($params)
	{
		//��ȡ������Ϣ
		$order_obj = POCO::singleton('pai_mall_order_class');
		$order_sn = trim($params['order_sn']);
		$order_info = $order_obj->get_order_full_info($order_sn);
		if( empty($order_info) )
		{
			return false;
		}
		$order_id = intval($order_info['order_id']);
		$activity_info = $order_info['activity_list'][0];
		$buyer_user_id = $order_info['buyer_user_id'];
		$seller_user_id = $order_info['seller_user_id'];

		//��OA�����ŷ�֪ͨ
		if( !in_array($order_info['referer'], array('oa', 'import')) )
		{
			//��� => �̼�
			$send_data = array();
			$send_data['media_type'] = 'card';
			$send_data['card_style'] = 2;
			$send_data['card_text1'] = '��ȡ������';
			$send_data['card_title'] = '�鿴��������';

			//�̼� => ���
			$to_send_data = array();
			$to_send_data['media_type'] = 'card';
			$to_send_data['card_style'] = 2;
			$to_send_data['card_text1'] = '��ȡ������';
			$to_send_data['card_title'] = '�鿴��������';

			POCO::singleton('pai_information_push')->card_send_for_order($buyer_user_id, 'yuebuyer', $seller_user_id, 'yueseller', $send_data, $to_send_data, $order_sn);

			//΢�Ź��ں�ģ����Ϣ
			$template_data = array(
				'first' => '����ȡ������',
				'goods_name' => $order_info['activity_name'],
				'total_amount' => $order_info['total_amount'],
				'status_str' => $order_info['status_str'],
				'remark' => '����鿴����',
			);
			$template_to_url = 'http://yp.yueus.com/mall/user/order/detail.php?order_sn=' . $order_sn;
			POCO::singleton('pai_weixin_pub_class')->message_template_send_by_user_id($buyer_user_id, 'G_PAI_WEIXIN_MALL_ORDER_STATUS', $template_data, $template_to_url);
		}

		/*********���¶���ͳ�Ʊ���***********/
		$comment_info = '';
		POCO::singleton('pai_mall_order_log_class')->add_order_log('close_wait_confirm_order_for_buyer',$order_id,$buyer_user_id,$seller_user_id,$order_info,$comment_info);

		/*********������Ʒ�۸����***********/
		$this->_close_order_after($activity_info);

		return true;
	}

	/**
	 * ���ҹر�_��ȷ�϶���_��
	 * @param array $params array('order_sn'=>'')
	 * @return boolean
	 */
	public function close_wait_confirm_order_for_seller_after($params)
	{
		//��ȡ������Ϣ
		$order_obj = POCO::singleton('pai_mall_order_class');
		$order_sn = trim($params['order_sn']);
		$order_info = $order_obj->get_order_full_info($order_sn);
		if( empty($order_info) )
		{
			return false;
		}
		$order_id = intval($order_info['order_id']);
		$activity_info = $order_info['activity_list'][0];
		$buyer_user_id = $order_info['buyer_user_id'];
		$seller_user_id = $order_info['seller_user_id'];

		//��OA�����ŷ�֪ͨ
		if( !in_array($order_info['referer'], array('oa', 'import')) )
		{
			//�̼� => ���
			$send_data = array();
			$send_data['media_type'] = 'card';
			$send_data['card_style'] = 2;
			$send_data['card_text1'] = '�Ѿܾ�����';
			$send_data['card_title'] = '�鿴��������';

			//��� => �̼�
			$to_send_data = array();
			$to_send_data['media_type'] = 'card';
			$to_send_data['card_style'] = 2;
			$to_send_data['card_text1'] = '�Ѿܾ�����';
			$to_send_data['card_title'] = '�鿴��������';

			POCO::singleton('pai_information_push')->card_send_for_order($seller_user_id, 'yueseller', $buyer_user_id, 'yuebuyer', $send_data, $to_send_data, $order_sn);

			//΢�Ź��ں�ģ����Ϣ
			$template_data = array(
				'first' => '�������ܾ�',
				'goods_name' => $order_info['activity_name'],
				'total_amount' => $order_info['total_amount'],
				'status_str' => $order_info['status_str'],
				'remark' => '����鿴����',
			);
			$template_to_url = 'http://yp.yueus.com/mall/user/order/detail.php?order_sn=' . $order_sn;
			POCO::singleton('pai_weixin_pub_class')->message_template_send_by_user_id($buyer_user_id, 'G_PAI_WEIXIN_MALL_ORDER_STATUS', $template_data, $template_to_url);
		}

		/*********���¶���ͳ�Ʊ���***********/
		$comment_info = '';
		POCO::singleton('pai_mall_order_log_class')->add_order_log('close_wait_confirm_order_for_seller',$order_id,$buyer_user_id,$seller_user_id,$order_info,$comment_info);

		/*********������Ʒ�۸����***********/
		$this->_close_order_after($activity_info);

		return true;
	}

	/**
	 * ��ҹر�_��ǩ������_��
	 * @param array $params
	 * @return boolean
	 * @tutorial
	 * $params = array(
	 *   'order_sn' => '',
	 * );
	 */
	public function close_wait_sign_order_for_buyer_after($params)
	{
		//��ȡ������Ϣ
		$order_obj = POCO::singleton('pai_mall_order_class');
		$order_sn = trim($params['order_sn']);
		$order_info = $order_obj->get_order_full_info($order_sn);
		if( empty($order_info) )
		{
			return false;
		}
		$order_id = intval($order_info['order_id']);
		$activity_info = $order_info['activity_list'][0];
		$buyer_user_id = $order_info['buyer_user_id'];
		$seller_user_id = $order_info['seller_user_id'];

		//��OA�����ŷ�֪ͨ
		if( !in_array($order_info['referer'], array('oa', 'import')) )
		{
			//��� => �̼�
			$send_data = array();
			$send_data['media_type'] = 'card';
			$send_data['card_style'] = 2;
			$send_data['card_text1'] = '������Чʱ���������˿�';
			$send_data['card_title'] = '�鿴��������';

			//�̼� => ���
			$to_send_data = array();
			$to_send_data['media_type'] = 'card';
			$to_send_data['card_style'] = 2;
			$to_send_data['card_text1'] = '������Чʱ���������˿�';
			$to_send_data['card_title'] = '�鿴��������';

			POCO::singleton('pai_information_push')->card_send_for_order($buyer_user_id, 'yuebuyer', $seller_user_id, 'yueseller', $send_data, $to_send_data, $order_sn);

			//΢�Ź��ں�ģ����Ϣ
			$template_data = array(
				'first' => '������Чʱ���������˿�ɹ�',
				'goods_name' => $order_info['activity_name'],
				'total_amount' => $order_info['total_amount'],
				'status_str' => $order_info['status_str'],
				'remark' => '����鿴����',
			);
			$template_to_url = 'http://yp.yueus.com/mall/user/order/detail.php?order_sn=' . $order_sn;
			POCO::singleton('pai_weixin_pub_class')->message_template_send_by_user_id($buyer_user_id, 'G_PAI_WEIXIN_MALL_ORDER_STATUS', $template_data, $template_to_url);
		}

		/*********���¶���ͳ�Ʊ���***********/
		$comment_info = '';
		POCO::singleton('pai_mall_order_log_class')->add_order_log('close_wait_sign_order_for_buyer',$order_id,$buyer_user_id,$seller_user_id,$order_info,$comment_info);

		/*********������Ʒ�۸����***********/
		$this->_close_order_after($activity_info);

		return true;
	}

	/**
	 * ϵͳ�ر�_��ǩ������_�󣨹���Ա�رվ���ʱ�䲻��ʮ��Сʱ��
	 * @param array $params
	 * @return boolean
	 * @tutorial
	 * $params = array(
	 *   'order_sn' => '',
	 * );
	 */
	public function close_wait_sign_order_for_system_after($params)
	{
		//��ȡ������Ϣ
		$order_obj = POCO::singleton('pai_mall_order_class');
		$order_sn = trim($params['order_sn']);
		$order_info = $order_obj->get_order_full_info($order_sn);
		$activity_info = $order_info['activity_list'][0];
		if( empty($order_info) )
		{
			return false;
		}
		$order_id = intval($order_info['order_id']);
		$buyer_user_id = $order_info['buyer_user_id'];
		$seller_user_id = $order_info['seller_user_id'];
		$buy_user_phone = POCO::singleton('pai_user_class')->get_phone_by_user_id($buyer_user_id);
		$seller_user_phone = POCO::singleton('pai_user_class')->get_phone_by_user_id($seller_user_id);

		//��OA�����ŷ�֪ͨ
		if( !in_array($order_info['referer'], array('oa', 'import')) )
		{
			//ԼԼС����=>���
			$send_data = array();
			$send_data['media_type'] = 'notify';
			$send_data['content']    = '��Ķ����ѱ��رգ������ţ�'.$order_sn.'�����Ѹ�����Զ�ת������˻�������ա�������������ϵԼԼ�ͷ���';
			$link_url = 'http://yp.yueus.com/mall/user/order/detail.php?order_sn=' . $order_sn;
			$wifi_link_url = 'http://yp-wifi.yueus.com/mall/user/order/detail.php?order_sn=' . $order_sn;
			$send_data['link_url']   = 'yueyue://goto?type=inner_web&showtitle=2&url=' . urlencode($link_url) . '$wifi_url=' . urlencode($wifi_link_url);
			POCO::singleton('pai_information_push')->message_sending_for_system($buyer_user_id, $send_data, 10002);

			//ԼԼС����=>�̼�
			$send_data = array();
			$send_data['media_type'] = 'notify';
			$send_data['content']    = '����һ�ʶ����ѱ��رգ������ţ�'.$order_sn.'����������������ϵԼԼ�ͷ���';
			$send_data['link_url']   = 'yueseller://goto?order_sn=' . $order_sn . '&pid=1250022&type=inner_app';
			POCO::singleton('pai_information_push')->message_sending_for_system($seller_user_id, $send_data, 10002, 'yueseller');
		}

		//���Ͷ���
		$pai_sms_obj = POCO::singleton('pai_sms_class');
		//ϵͳ�رն����������̼�
		$sms_data = array(
			'order_sn' => $order_sn,
		);
		$pai_sms_obj->send_sms($seller_user_phone, 'G_PAI_MALL_ORDER_CLOSE_WAIT_SIGN_FOR_SYSTEM_SELLER', $sms_data);
		//ϵͳ�رն���������������
		$sms_data = array(
			'order_sn' => $order_sn,
		);
		$pai_sms_obj->send_sms($buy_user_phone, 'G_PAI_MALL_ORDER_CLOSE_WAIT_SIGN_FOR_SYSTEM_BUYER', $sms_data);

		/*********���¶���ͳ�Ʊ���***********/
		$comment_info = '';
		POCO::singleton('pai_mall_order_log_class')->add_order_log('close_wait_sign_order_for_system',$order_id,$buyer_user_id,$seller_user_id,$order_info,$comment_info);

		/*********������Ʒ�۸����***********/
		$this->_close_order_after($activity_info);

		return true;
	}

	/**
	 * @param $activity_info
	 * @return bool
	 */
	public function _close_order_after($activity_info)
	{
		/*********������Ʒ�۸����***********/
		$activity_id = intval($activity_info['activity_id']);
		if( $activity_id>0 )
		{
			$data = array(
				'bill_pay_num' => -1,
			);
			POCO::singleton('pai_mall_goods_class')->update_goods_statistical($activity_id, $data);
		}
		return true;
	}
}
