<?php
/**
 * 事件触发
 * 方法名规范：模块名称_事件_前/后
 * @author Henry
 * @copyright 2015-04-10
 */

class pai_trigger_class
{
	/**
	 * 构造函数
	 */
	public function __construct()
	{
	}
	
	/**
	 * 用户_注册_后
	 * @param array $params
	 * @return boolean
	 * @tutorial
	 * $params = array(
	 *   'user_id' => 0, //用户ID
	 * );
	 */
	public function user_reg_after($params)
	{
		//检查参数
		$user_id = intval($params['user_id']);
		if( $user_id<1 )
		{
			return false;
		}
		
		//获取用户信息
		$user_obj = POCO::singleton('pai_user_class');
		$user_info = $user_obj->get_user_info($user_id);
		if( empty($user_info) )
		{
			return false;
		}
		
		$cur_time = time();
		
		//2015年10月起，注册用户
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
	 * 约拍邀请_结束_后
	 * @param array $params
	 * @return boolean
	 * @tutorial
	 * $params = array(
	 *   'date_id' => 0, //邀请ID
	 * );
	 */
	public function yuepai_date_end_after($params)
	{
		$date_id = intval($params['date_id']);
		if( $date_id<1 )
		{
			return false;
		}
		
		//获取邀请信息
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
		
		//2015年4月 消费满赠券，4月15日前消费
		if( $model_user_id!=109265 ) //排除 约约充值卡 虚拟模特
		{
			if( strtotime('2015-04-01 00:00:00')<=$cur_time && $cur_time<=strtotime('2015-04-15 23:59:59') )
			{
				//4月15日前消费
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
				//4月15日后消费
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
	 * 外拍报名_结束_后
	 * @param array $params
	 * @return boolean
	 * @tutorial
	 * $params = array(
	 *   'enroll_id' => 0, //报名ID
	 * );
	 */
	public function waipai_enroll_end_after($params)
	{
		$enroll_id = intval($params['enroll_id']);
		if( $enroll_id<1 )
		{
			return false;
		}
		
		//获取报名信息
		$enroll_obj = POCO::singleton('event_enroll_class');
		$enroll_info = $enroll_obj->get_enroll_by_enroll_id($enroll_id);
		if( empty($enroll_info) )
		{
			return false;
		}
		$event_id = intval($enroll_info['event_id']);
		$buyer_poco_id = intval($enroll_info['user_id']);
		$buyer_user_id = get_relate_yue_id($buyer_poco_id);
		
		//获取活动信息
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
		
		//2015年9月 约约精品人像
		if( $buyer_user_id>0 && $seller_user_id>0 && strtotime('2015-09-08 00:00:00')<=$cur_time && $cur_time<=strtotime('2015-10-12 23:59:59') )
		{
			$coupon_obj = POCO::singleton('pai_coupon_class');
			$scope_info = $coupon_obj->get_scope_info(291, 'white', 'event_user_id');
			$scope_value = trim($scope_info['scope_value']);
			$scope_value_arr = explode('#', $scope_value);
			if( !empty($scope_info) && strlen($scope_value)>0 && !empty($scope_value_arr) && in_array(trim($seller_user_id), $scope_value_arr, true) )
			{
				$coupon_give_obj = POCO::singleton('pai_coupon_give_class');
				
				//赠送5元优惠券
				$more_info = array('event_id'=>$event_id, 'enroll_id'=>$enroll_id);
				$queue_id = $coupon_give_obj->submit_queue('Y2015M09D08_WAIPAI_YUEJP', '', $buyer_user_id, $enroll_id, $more_info);
				if( $queue_id>0 )
				{
					//赠送20元优惠券
					$sql = "SELECT b.batch_id FROM `pai_coupon_db`.`coupon_batch_tbl` AS b LEFT JOIN `pai_coupon_db`.`coupon_scope_tbl` AS s ON b.batch_id=s.batch_id WHERE b.batch_id NOT IN (291,292) AND b.cate_id=37 AND s.scope_type='white' AND s.scope_code='seller_user_id' AND s.scope_value='{$seller_user_id}'";
					$batch_info_tmp = db_simple_getdata($sql, true, 101);
					$batch_id = intval($batch_info_tmp['batch_id']);
					if( $batch_id<1 )
					{
						//自动创建批次
						$data = array(
							'cate_id' => 37,
							'batch_name' => "{$seller_nickname}（ID:{$seller_user_id}专属）",
							'batch_desc' => "1、本优惠券不能用于提现，只能用于抵扣消费金额；\r\n<br />2、本优惠券只能单独使用，不能与其他优惠券叠加使用；\r\n<br />3、如使用优惠券的订单产生的退款行为，本优惠券将自动作废，只退回支付金额部分；\r\n<br />4、私拍优惠券每天只能使用一张，每天0:00为更新时间；\r\n<br />5、优惠券使用规则解释权归约约所有；\r\n<br />6、如有疑问，可拨打客服热线：4000-82-9003。",
							'coupon_type_id' => 1,
							'coupon_face_value' => 20,
							'coupon_start_time' => strtotime('2015-09-08 00:00:00'),
							'coupon_end_time' => strtotime('2015-10-12 23:59:59'),
							'scope_module_type_name' => '模特服务',
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
	 * OA需求_提交_后
	 * @param array $params
	 * @return boolean
	 * @tutorial
	 * $params = array(
	 *   'order_id' => 0, //OA订单ID
	 * );
	 */
	public function requirement_submit_after($params)
	{
		$order_id = intval($params['order_id']);
		if( $order_id<1 )
		{
			return false;
		}
		
		//获取OA订单信息
		$model_oa_order_obj = POCO::singleton('pai_model_oa_order_class');
		$oa_order_info = $model_oa_order_obj->get_order_info($order_id);
		if( empty($oa_order_info) )
		{
			return false;
		}
		$cameraman_phone = trim($oa_order_info['cameraman_phone']);
		
		$cur_time = time();
		
		//2015年4月 提需求赠券
		if( strtotime('2015-04-01 00:00:00')<=$cur_time && $cur_time<=strtotime('2015-04-15 23:59:59') )
		{
			//4月15日前提交
			$more_info = array('remark'=>$order_id);
			$coupon_give_obj = POCO::singleton('pai_coupon_give_class');
			$coupon_give_obj->submit_queue('Y2015M04D01_REQUIREMENT_FIRST', $cameraman_phone, 0, 0, $more_info);
		}
		elseif( strtotime('2015-04-16 00:00:00')<=$cur_time && $cur_time<=strtotime('2015-04-30 23:59:59') )
		{
			//4月15日后提交
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
	 * APP_分享_后
	 * @param array $params
	 * @return boolean
	 * @tutorial
	 * $params = array(
	 *   'share_id' => 0, //分享ID，目前没有
	 *   'user_id' => 0, //分享ID
	 *   'cellphone' => '', //手机号码
	 *   'event_id'=> 0, //活动ID，目前没有
	 *   'url' => '', //分享链接
	 * );
	 */
	public function app_share_after($params)
	{
        $user_id = intval($params['user_id']);
        $cellphone = trim($params['cellphone']);
        $url = trim($params['url']);

        $cur_time = time();

        //2015年11月16日至2015年11月21日 分享活动赠券 http://yp.yueus.com/mall/user/act/detail.php?event_id=60559
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
	 * 微信_分享_后
	 * @param array $params
	 * @return boolean
	 * @tutorial
	 * $params = array(
	 *   'share_id' => 0, //分享ID，目前没有
	 *   'cellphone' => '', //手机号码
	 *   'event_id'=> 0, //活动ID
	 *   'url' => '', //分享链接
	 * );
	 */
	public function weixin_share_after($params)
	{
        $user_id = intval($params['user_id']);
        $cellphone = trim($params['cellphone']);
        $url = trim($params['url']);

        $cur_time = time();

        //2015年11月16日至2015年11月21日 分享活动赠券 http://yp.yueus.com/mall/user/act/detail.php?event_id=60559
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
	 * App Store评价_前
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
	 * 优惠券_兑换_后
	 * @param array $params
	 * @return boolean
	 * @tutorial
	 * $params = array(
	 *   'user_id' => 0, //用户ID
	 *   'package_sn' => '', //兑换码
	 *   'coupon_sn_arr'=> 0, //优惠券
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
		
		//获取兑换码信息
		$coupon_package_obj = POCO::singleton('pai_coupon_package_class');
		$package_info = $coupon_package_obj->get_package_info($package_sn);
		
		//推送通知
		$msg_content = trim($package_info['msg_content']);
		$msg_url = trim($package_info['msg_url']);
		if( !empty($package_info) && strlen($msg_content)>0 )
		{
			//约约小助手
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
			
			//微信公众号模板消息
			$template_data = array(
				'title' => '优惠消息提醒',
				'content' => $msg_content,
			);
			$template_to_url = '';
			if( strlen($msg_url)>0 ) $template_to_url = 'http://yp.yueus.com' . $msg_url;
			POCO::singleton('pai_weixin_pub_class')->message_template_send_by_user_id($user_id, 'G_PAI_WEIXIN_SYSTEM_NOTICE', $template_data, $template_to_url);
		}
		
		return true;
	}
}
