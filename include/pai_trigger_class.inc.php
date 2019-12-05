<?php
/**
 * �¼�����
 * �������淶��ģ������_�¼�_ǰ/��
 * @author Henry
 * @copyright 2015-04-10
 */

class pai_trigger_class
{
	/**
	 * ���캯��
	 */
	public function __construct()
	{
	}
	
	/**
	 * �û�_ע��_��
	 * @param array $params
	 * @return boolean
	 * @tutorial
	 * $params = array(
	 *   'user_id' => 0, //�û�ID
	 * );
	 */
	public function user_reg_after($params)
	{
		//������
		$user_id = intval($params['user_id']);
		if( $user_id<1 )
		{
			return false;
		}
		
		//��ȡ�û���Ϣ
		$user_obj = POCO::singleton('pai_user_class');
		$user_info = $user_obj->get_user_info($user_id);
		if( empty($user_info) )
		{
			return false;
		}
		
		$cur_time = time();
		
		//2015��10����ע���û�
		if( strtotime('2015-10-01 00:00:00')<=$cur_time && $cur_time<=strtotime('2015-10-10 23:59:59') )
		{
			$coupon_give_obj = POCO::singleton('pai_coupon_give_class');
			$coupon_give_obj->submit_queue('Y2015M10D01_USER_REG', '', $user_id, 0);
		}
		elseif( strtotime('2015-10-11 00:00:00')<=$cur_time && $cur_time<=strtotime('2015-11-10 23:59:59') )
		{
			$coupon_give_obj = POCO::singleton('pai_coupon_give_class');
			$coupon_give_obj->submit_queue('Y2015M10D11_USER_REG', '', $user_id, 0);
		}

		if( strtotime('2015-11-11 11:00:00')<=$cur_time && $cur_time<=strtotime('2015-12-10 23:59:59') )
		{
			$coupon_give_obj = POCO::singleton('pai_coupon_give_class');
			$coupon_give_obj->submit_queue('Y2015M11D09_USER_REG', '', $user_id, 0);
		}
		
		return true;
	}
	
	/**
	 * Լ������_����_��
	 * @param array $params
	 * @return boolean
	 * @tutorial
	 * $params = array(
	 *   'date_id' => 0, //����ID
	 * );
	 */
	public function yuepai_date_end_after($params)
	{
		$date_id = intval($params['date_id']);
		if( $date_id<1 )
		{
			return false;
		}
		
		//��ȡ������Ϣ
		$date_obj = POCO::singleton('event_date_class');
		$date_info = $date_obj->get_date_info($date_id);
		if( empty($date_info) )
		{
			return false;
		}
		$cameraman_user_id = intval($date_info['from_date_id']);
		$model_user_id = intval($date_info['to_date_id']);
		$date_price = $date_info['date_price']*1;
		
		$cur_time = time();
		
		//2015��4�� ��������ȯ��4��15��ǰ����
		if( $model_user_id!=109265 ) //�ų� ԼԼ��ֵ�� ����ģ��
		{
			if( strtotime('2015-04-01 00:00:00')<=$cur_time && $cur_time<=strtotime('2015-04-15 23:59:59') )
			{
				//4��15��ǰ����
				$more_info = array('date_id'=>$date_id);
				$coupon_give_obj = POCO::singleton('pai_coupon_give_class');
				if( $date_price>=500 )
				{
					$coupon_give_obj->submit_queue('Y2015M04D01_YUEPAI_OVER_500', '', $cameraman_user_id, $date_id, $more_info);
				}
				elseif( $date_price>=300 )
				{
					$coupon_give_obj->submit_queue('Y2015M04D01_YUEPAI_OVER_300', '', $cameraman_user_id, $date_id, $more_info);
				}
				elseif( $date_price>=100 )
				{
					$coupon_give_obj->submit_queue('Y2015M04D01_YUEPAI_OVER_100', '', $cameraman_user_id, $date_id, $more_info);
				}
			}
			elseif( strtotime('2015-04-16 00:00:00')<=$cur_time && $cur_time<=strtotime('2015-04-30 23:59:59') )
			{
				//4��15�պ�����
				$more_info = array('date_id'=>$date_id);
				$coupon_give_obj = POCO::singleton('pai_coupon_give_class');
				if( $date_price>=500 )
				{
					$coupon_give_obj->submit_queue('Y2015M04D16_YUEPAI_OVER_500', '', $cameraman_user_id, $date_id, $more_info);
				}
				elseif( $date_price>=300 )
				{
					$coupon_give_obj->submit_queue('Y2015M04D16_YUEPAI_OVER_300', '', $cameraman_user_id, $date_id, $more_info);
				}
				elseif( $date_price>=100 )
				{
					$coupon_give_obj->submit_queue('Y20150M4D16_YUEPAI_OVER_100', '', $cameraman_user_id, $date_id, $more_info);
				}
			}
		}
		
		return true;
	}
	
	/**
	 * ���ı���_����_��
	 * @param array $params
	 * @return boolean
	 * @tutorial
	 * $params = array(
	 *   'enroll_id' => 0, //����ID
	 * );
	 */
	public function waipai_enroll_end_after($params)
	{
		$enroll_id = intval($params['enroll_id']);
		if( $enroll_id<1 )
		{
			return false;
		}
		
		//��ȡ������Ϣ
		$enroll_obj = POCO::singleton('event_enroll_class');
		$enroll_info = $enroll_obj->get_enroll_by_enroll_id($enroll_id);
		if( empty($enroll_info) )
		{
			return false;
		}
		$event_id = intval($enroll_info['event_id']);
		$buyer_poco_id = intval($enroll_info['user_id']);
		$buyer_user_id = get_relate_yue_id($buyer_poco_id);
		
		//��ȡ���Ϣ
		$event_details_obj = POCO::singleton ('event_details_class');
		$event_info = $event_details_obj->get_event_by_event_id($event_id);
		if( empty($event_info) )
		{
			return false;
		}
		$seller_poco_id = intval($event_info['user_id']);
		$seller_user_id = get_relate_yue_id($seller_poco_id);
		$seller_nickname = get_seller_nickname_by_user_id($seller_user_id);
		
		$cur_time = time();
		
		//2015��9�� ԼԼ��Ʒ����
		if( $buyer_user_id>0 && $seller_user_id>0 && strtotime('2015-09-08 00:00:00')<=$cur_time && $cur_time<=strtotime('2015-10-12 23:59:59') )
		{
			$coupon_obj = POCO::singleton('pai_coupon_class');
			$scope_info = $coupon_obj->get_scope_info(291, 'white', 'event_user_id');
			$scope_value = trim($scope_info['scope_value']);
			$scope_value_arr = explode('#', $scope_value);
			if( !empty($scope_info) && strlen($scope_value)>0 && !empty($scope_value_arr) && in_array(trim($seller_user_id), $scope_value_arr, true) )
			{
				$coupon_give_obj = POCO::singleton('pai_coupon_give_class');
				
				//����5Ԫ�Ż�ȯ
				$more_info = array('event_id'=>$event_id, 'enroll_id'=>$enroll_id);
				$queue_id = $coupon_give_obj->submit_queue('Y2015M09D08_WAIPAI_YUEJP', '', $buyer_user_id, $enroll_id, $more_info);
				if( $queue_id>0 )
				{
					//����20Ԫ�Ż�ȯ
					$sql = "SELECT b.batch_id FROM `pai_coupon_db`.`coupon_batch_tbl` AS b LEFT JOIN `pai_coupon_db`.`coupon_scope_tbl` AS s ON b.batch_id=s.batch_id WHERE b.batch_id NOT IN (291,292) AND b.cate_id=37 AND s.scope_type='white' AND s.scope_code='seller_user_id' AND s.scope_value='{$seller_user_id}'";
					$batch_info_tmp = db_simple_getdata($sql, true, 101);
					$batch_id = intval($batch_info_tmp['batch_id']);
					if( $batch_id<1 )
					{
						//�Զ���������
						$data = array(
							'cate_id' => 37,
							'batch_name' => "{$seller_nickname}��ID:{$seller_user_id}ר����",
							'batch_desc' => "1�����Ż�ȯ�����������֣�ֻ�����ڵֿ����ѽ�\r\n<br />2�����Ż�ȯֻ�ܵ���ʹ�ã������������Ż�ȯ����ʹ�ã�\r\n<br />3����ʹ���Ż�ȯ�Ķ����������˿���Ϊ�����Ż�ȯ���Զ����ϣ�ֻ�˻�֧�����֣�\r\n<br />4��˽���Ż�ȯÿ��ֻ��ʹ��һ�ţ�ÿ��0:00Ϊ����ʱ�䣻\r\n<br />5���Ż�ȯʹ�ù������Ȩ��ԼԼ���У�\r\n<br />6���������ʣ��ɲ���ͷ����ߣ�4000-82-9003��",
							'coupon_type_id' => 1,
							'coupon_face_value' => 20,
							'coupon_start_time' => strtotime('2015-09-08 00:00:00'),
							'coupon_end_time' => strtotime('2015-10-12 23:59:59'),
							'scope_module_type_name' => 'ģ�ط���',
							'scope_order_total_amount' => 200,
							'plan_quantity' => 100,
							'is_need_cash' => 1,
							'need_cash_rate' => 100,
							'need_cash_max' => 0,
							'check_status' => 1,
						);
						$batch_id = $coupon_obj->create_batch($data);
						$coupon_obj->add_scope($batch_id, 'white' ,'module_type', 'mall_order');
						$coupon_obj->add_scope($batch_id, 'white' ,'mall_type_id', '31');
						$coupon_obj->add_scope($batch_id, 'white' ,'order_total_amount', '200');
						$coupon_obj->add_scope($batch_id, 'white' ,'seller_user_id', $seller_user_id);
					}
					if( $batch_id>0 )
					{
						$coupon_obj->give_coupon_by_create($buyer_user_id, $batch_id);
					}
				}
			}
		}
		
		return true;
	}
	
	/**
	 * OA����_�ύ_��
	 * @param array $params
	 * @return boolean
	 * @tutorial
	 * $params = array(
	 *   'order_id' => 0, //OA����ID
	 * );
	 */
	public function requirement_submit_after($params)
	{
		$order_id = intval($params['order_id']);
		if( $order_id<1 )
		{
			return false;
		}
		
		//��ȡOA������Ϣ
		$model_oa_order_obj = POCO::singleton('pai_model_oa_order_class');
		$oa_order_info = $model_oa_order_obj->get_order_info($order_id);
		if( empty($oa_order_info) )
		{
			return false;
		}
		$cameraman_phone = trim($oa_order_info['cameraman_phone']);
		
		$cur_time = time();
		
		//2015��4�� ��������ȯ
		if( strtotime('2015-04-01 00:00:00')<=$cur_time && $cur_time<=strtotime('2015-04-15 23:59:59') )
		{
			//4��15��ǰ�ύ
			$more_info = array('remark'=>$order_id);
			$coupon_give_obj = POCO::singleton('pai_coupon_give_class');
			$coupon_give_obj->submit_queue('Y2015M04D01_REQUIREMENT_FIRST', $cameraman_phone, 0, 0, $more_info);
		}
		elseif( strtotime('2015-04-16 00:00:00')<=$cur_time && $cur_time<=strtotime('2015-04-30 23:59:59') )
		{
			//4��15�պ��ύ
			$more_info = array('remark'=>$order_id);
			$coupon_give_obj = POCO::singleton('pai_coupon_give_class');
			$queue_count = $coupon_give_obj->get_queue_list(-1, true, "give_code='Y2015M04D01_REQUIREMENT_FIRST' AND cellphone='".($cameraman_phone*1)."' AND ref_id=0");
			if( $queue_count<1 )
			{
				$coupon_give_obj->submit_queue('Y2015M04D16_REQUIREMENT_FIRST', $cameraman_phone, 0, 0, $more_info);
			}
		}
		
		return true;
	}
	
	/**
	 * APP_����_��
	 * @param array $params
	 * @return boolean
	 * @tutorial
	 * $params = array(
	 *   'share_id' => 0, //����ID��Ŀǰû��
	 *   'user_id' => 0, //����ID
	 *   'cellphone' => '', //�ֻ�����
	 *   'event_id'=> 0, //�ID��Ŀǰû��
	 *   'url' => '', //��������
	 * );
	 */
	public function app_share_after($params)
	{
        $user_id = intval($params['user_id']);
        $cellphone = trim($params['cellphone']);
        $url = trim($params['url']);

        $cur_time = time();

        //2015��11��16����2015��11��21�� ������ȯ http://yp.yueus.com/mall/user/act/detail.php?event_id=60559
        $url_info = parse_url($url);
        $query = explode('&', $url_info['query']);
        if( preg_match('/^share_event_id=60559$/isU', $query[0]) && strtotime('2015-11-16 00:00:00')<=$cur_time && $cur_time<=strtotime('2015-11-21 23:59:59') )
        {
            $more_info = array('remark'=>$url);
            $coupon_give_obj = POCO::singleton('pai_coupon_give_class');
            $coupon_give_obj->submit_queue('Y2015M11D16_SHARE_EVENT_60559', $cellphone, $user_id, 60559, $more_info);
        }

        return true;
	}
	
	/**
	 * ΢��_����_��
	 * @param array $params
	 * @return boolean
	 * @tutorial
	 * $params = array(
	 *   'share_id' => 0, //����ID��Ŀǰû��
	 *   'cellphone' => '', //�ֻ�����
	 *   'event_id'=> 0, //�ID
	 *   'url' => '', //��������
	 * );
	 */
	public function weixin_share_after($params)
	{
        $user_id = intval($params['user_id']);
        $cellphone = trim($params['cellphone']);
        $url = trim($params['url']);

        $cur_time = time();

        //2015��11��16����2015��11��21�� ������ȯ http://yp.yueus.com/mall/user/act/detail.php?event_id=60559
        // $url = "http://yp.yueus.com/mall/user/act/detail.php?event_id=60559";
        $url_info = parse_url($url);
        $query = explode('&', $url_info['query']);
        if( preg_match('/^event_id=60559$/isU', $query[0]) && strtotime('2015-11-16 00:00:00')<=$cur_time && $cur_time<=strtotime('2015-11-21 23:59:59') )
        {
            $more_info = array('remark'=>$url);
            $coupon_give_obj = POCO::singleton('pai_coupon_give_class');
            $coupon_give_obj->submit_queue('Y2015M11D16_SHARE_EVENT_60559', $cellphone, $user_id, 60559, $more_info);
        }

        return true;
	}
	
	/**
	 * App Store����_ǰ
	 * @param array $params
	 */
	public function app_store_comment_before($params)
	{
        $user_id = intval($params['user_id']);
        $cur_time = time();

        if( strtotime('2015-09-01 00:00:00')<=$cur_time && $cur_time<=strtotime('2015-09-01 23:59:59') )
        {
            $more_info = array('remark'=>'');
            $coupon_give_obj = POCO::singleton('pai_coupon_give_class');
            $coupon_give_obj->submit_queue('Y2015M09D07_COMMENT_APP_1', '', $user_id, 0, $more_info);
        }

        return true;
	}
	
	/**
	 * �Ż�ȯ_�һ�_��
	 * @param array $params
	 * @return boolean
	 * @tutorial
	 * $params = array(
	 *   'user_id' => 0, //�û�ID
	 *   'package_sn' => '', //�һ���
	 *   'coupon_sn_arr'=> 0, //�Ż�ȯ
	 * );
	 */
	public function coupon_exchange_package_after($params)
	{
		$user_id = intval($params['user_id']);
		$package_sn = trim($params['package_sn']);
		if( $user_id<1 || strlen($package_sn)<1 )
		{
			return false;
		}
		
		//��ȡ�һ�����Ϣ
		$coupon_package_obj = POCO::singleton('pai_coupon_package_class');
		$package_info = $coupon_package_obj->get_package_info($package_sn);
		
		//����֪ͨ
		$msg_content = trim($package_info['msg_content']);
		$msg_url = trim($package_info['msg_url']);
		if( !empty($package_info) && strlen($msg_content)>0 )
		{
			//ԼԼС����
			$send_data = array();
			if( strlen($msg_url)>0 )
			{
				$send_data['media_type'] = 'notify';
				$link_url = 'http://yp.yueus.com' . $msg_url;
				$wifi_link_url = 'http://yp-wifi.yueus.com' . $msg_url;
				$send_data['link_url'] = 'yueyue://goto?type=inner_web&showtitle=2&url=' . urlencode($link_url) . '&wifi_url=' . urlencode($wifi_link_url);
			}
			else
			{
				$send_data['media_type'] = 'text';
			}
			$send_data['content'] = $msg_content;
			$push_obj = POCO::singleton('pai_information_push');
			$push_obj->message_sending_for_system($user_id, $send_data, 10002);
			
			//send_message_for_10002($user_id, $msg_content, $msg_url, 'yuebuyer');
			
			//΢�Ź��ں�ģ����Ϣ
			$template_data = array(
				'title' => '�Ż���Ϣ����',
				'content' => $msg_content,
			);
			$template_to_url = '';
			if( strlen($msg_url)>0 ) $template_to_url = 'http://yp.yueus.com' . $msg_url;
			POCO::singleton('pai_weixin_pub_class')->message_template_send_by_user_id($user_id, 'G_PAI_WEIXIN_SYSTEM_NOTICE', $template_data, $template_to_url);
		}
		
		return true;
	}
}
