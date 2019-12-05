<?php
/**
 * 事件触发
 * 方法名规范：模块名称_事件_前/后
 * @author Henry
 * @copyright 2015-04-16
 */

class pai_task_trigger_class
{
	/**
	 * 构造函数
	 */
	public function __construct()
	{
	}
	
	/**
	 * 需求_提交_后
	 * @param array $params
	 * @return boolean
	 * @tutorial
	 * $params = array(
	 *   'request_id' => 0, //需求ID
	 * );
	 */
	public function request_submit_after($params)
	{
		//检查参数
		$request_id = intval($params['request_id']);
		if( $request_id<1 )
		{
			return false;
		}
		
		//获取需求
		$task_request_obj = POCO::singleton('pai_task_request_class');
		$request_info = $task_request_obj->get_request_info($request_id);
		if( empty($request_info) )
		{
			return false;
		}
		$buyer_user_id = intval($request_info['user_id']);
		
		$cur_time = time();
		
		//2015年5月 首次提需求赠券
		if( strtotime('2015-05-01 00:00:00')<=$cur_time && $cur_time<=strtotime('2015-05-31 23:59:59') )
		{
			$request_count = $task_request_obj->get_request_detail_list($buyer_user_id, 0, true);
			if( $request_count==1 )
			{
				$coupon_give_obj = POCO::singleton('pai_coupon_give_class');
				$coupon_give_obj->submit_queue('Y2015M05D01_TASK_REQUEST_FIRST', '', $buyer_user_id, 0);
			}
		}
		
		return true;
	}
	
	/**
	 * 需求_通过_后
	 * @param array $params
	 * @return boolean
	 * @tutorial
	 * $params = array(
	 *   'request_id' => 0, //需求ID
	 * );
	 */
	public function request_pass_after($params,$auto=false)
	{
		if($auto)
		{
			return true;
		}
		//检查参数
		$request_id = intval($params['request_id']);
		if( $request_id<1 )
		{
			return false;
		}
		
		//获取需求信息
		$task_request_obj = POCO::singleton('pai_task_request_class');
		$request_info = $task_request_obj->get_request_info($request_id);
		if( empty($request_info) )
		{
			return false;
		}
		$buyer_user_id = intval($request_info['user_id']);
		
		//获取服务信息
		$service_id = intval($request_info['service_id']);
		$task_service_obj = POCO::singleton('pai_task_service_class');
		$service_info = $task_service_obj->get_service_info($service_id);
		if( empty($service_info) )
		{
			return false;
		}
		
		$content = "我们已经把你发布的{$service_info['service_name']}需求推送给每一个符合资格的{$service_info['profession_name']}。\r\n你可以在24小时内收到最多八条报价，报价数量达到八条，或发布报价时间达到24小时后，{$service_info['profession_name']}将不会再发送报价。";
		$url = '';
		send_message_for_10006($buyer_user_id, $content, $url);
		
		return true;
	}
	
	/**
	 * 报价_支付生意卡_后
	 * @param array $params
	 * @return boolean
	 * @tutorial
	 * $params = array(
	 *   'quotes_id' => 0, //报价ID
	 * );
	 */
	public function quotes_pay_coins_after($params)
	{
		//检查参数
		$quotes_id = intval($params['quotes_id']);
		if( $quotes_id<1 )
		{
			return false;
		}
		
		//获取报价信息
		$task_quotes_obj = POCO::singleton('pai_task_quotes_class');
		$quotes_info = $task_quotes_obj->get_quotes_info($quotes_id);
		if( empty($quotes_info) )
		{
			return false;
		}
		$request_id = intval($quotes_info['request_id']);
		
		//获取需求信息
		$task_request_obj = POCO::singleton('pai_task_request_class');
		$request_info = $task_request_obj->get_request_info($request_id);
		if( empty($request_info))
		{
			return false;
		}
		$service_id = intval($request_info['service_id']);
		$buyer_user_id = intval($request_info['user_id']);
		
		//获取服务信息
		$task_service_obj = POCO::singleton('pai_task_service_class');
		$service_info = $task_service_obj->get_service_info($service_id);
		if( empty($service_info) )
		{
			return false;
		}
		
		$send_data = array();
		$send_data['media_type'] ='card';
		$send_data['card_style'] = 2;
		$send_data['card_text1'] = "你收到了一条{$service_info['service_name']}的新报价";
		$send_data['card_title'] = '查看报价详情';
		$send_data['link_url'] = 'yueyue://goto?type=inner_app&pid=1220079&request_id=' . $request_id;
		$send_data['wifi_url'] = 'yueyue://goto?type=inner_app&pid=1220079&request_id=' . $request_id;
		$push_obj = POCO::singleton('pai_information_push');
		$push_obj->send_msg_for_system_v2(10006, $buyer_user_id, $send_data);
		//报价后给app发送离线信息
		$content = "你收到了一条新的报价。（点击进入报价列表）";
		send_offline_message($buyer_user_id,$content);
		
		/*
		//获取买家手机号码
		$pai_user_obj = POCO::singleton('pai_user_class');
		$buyer_cellphone = $pai_user_obj->get_phone_by_user_id($buyer_user_id);
		$sms_data = array(
			'service_name' => $service_info['service_name'],
		);
		$pai_sms_obj = POCO::singleton('pai_sms_class');
		$pai_sms_obj->send_sms($buyer_cellphone, 'G_PAI_TASK_QUOTES_PAY_COINS_BUYER', $sms_data);
		*/
		
		//获取报价的数量，判断报价是否已满。这里只处理报满的情况，过期未报满的在定时执行那边处理。
		$quotes_count = $task_quotes_obj->get_quotes_list_for_valid($request_id, true);
		$max_quotes_num = $task_quotes_obj->get_max_quotes_num();
		if( $quotes_count>0 && $quotes_count==$max_quotes_num )
		{
			//获取买家手机号码
			$pai_user_obj = POCO::singleton('pai_user_class');
			$buyer_cellphone = $pai_user_obj->get_phone_by_user_id($buyer_user_id);
			$sms_data = array();
			$pai_sms_obj = POCO::singleton('pai_sms_class');
			$pai_sms_obj->send_sms($buyer_cellphone, 'G_PAI_TASK_QUOTES_FINISH_BUYER', $sms_data);
			
// 			$content = "你在约约上发布的{$service_info['service_name']}需求已经有{$quotes_count}人发来了报价。快去看看吧";
// 			$url = '';
// 			send_message_for_10006($buyer_user_id, $content, $url);
		}
		
		return true;
	}
	
	/**
	 * 报价_被查看_后
	 * @param array $params
	 * @return boolean
	 * @tutorial
	 * $params = array(
	 *   'quotes_id' => 0, //报价ID
	 * );
	 */
	public function quotes_read_after($params)
	{
		//检查参数
		$quotes_id = intval($params['quotes_id']);
		if( $quotes_id<1 )
		{
			return false;
		}
		
		return true;
	}
	
	/**
	 * 报价_雇佣_后
	 * @param array $params
	 * @return boolean
	 * @tutorial
	 * $params = array(
	 *   'quotes_id' => 0, //报价ID
	 * );
	 */
	public function quotes_hire_after($params)
	{
		//检查参数
		$quotes_id = intval($params['quotes_id']);
		if( $quotes_id<1 )
		{
			return false;
		}
		
		//获取报价信息
		$task_quotes_obj = POCO::singleton('pai_task_quotes_class');
		$quotes_info = $task_quotes_obj->get_quotes_info($quotes_id);
		if( empty($quotes_info) )
		{
			return false;
		}
		$request_id = intval($quotes_info['request_id']);
		$seller_user_id = intval($quotes_info['user_id']);
		$seller_nickname = get_user_nickname_by_user_id($seller_user_id);
		
		//获取需求信息
		$task_request_obj = POCO::singleton('pai_task_request_class');
		$request_info = $task_request_obj->get_request_info($request_id);
		if( empty($request_info))
		{
			return false;
		}
		$buyer_user_id = intval($request_info['user_id']);
		$buyer_nickname = get_user_nickname_by_user_id($buyer_user_id);
		
		$content = "你已经与{$seller_nickname}达成订单。请与Ta联系，确认选择后，再进行服务金的支付。";
		$url = '';
		send_message_for_10006($buyer_user_id, $content, $url);
		
		//获取买家手机号码
		$pai_user_obj = POCO::singleton('pai_user_class');
		$buyer_cellphone = $pai_user_obj->get_phone_by_user_id($buyer_user_id);
		$sms_data = array(
			'seller_nickname' => $seller_nickname,
		);
		$pai_sms_obj = POCO::singleton('pai_sms_class');
		$pai_sms_obj->send_sms($buyer_cellphone, 'G_PAI_TASK_QUOTES_HIRE_BUYER', $sms_data);
		
		//获取卖家手机号码
		$task_seller_obj = POCO::singleton('pai_task_seller_class');
		$seller_cellphone = $task_seller_obj->get_seller_cellphone($seller_user_id);
		$sms_data = array(
			'buyer_nickname' => $buyer_nickname,
			'url' => 'task.yueus.com/m/talk.php?quotes_id='.$quotes_id,
		);
		$pai_sms_obj = POCO::singleton('pai_sms_class');
		$pai_sms_obj->send_sms($seller_cellphone, 'G_PAI_TASK_QUOTES_HIRE_SELLER', $sms_data);
		
		return true;
	}
	
	/**
	 * 报价_支付定金_后
	 * @param array $params
	 * @return boolean
	 * @tutorial
	 * $params = array(
	 *   'quotes_id' => 0, //报价ID
	 * );
	 */
	public function quotes_pay_after($params)
	{
		//检查参数
		$quotes_id = intval($params['quotes_id']);
		if( $quotes_id<1 )
		{
			return false;
		}
		
		return true;
	}
	
	/**
	 * 报价_评价_后
	 * @param array $params
	 * @return boolean
	 * @tutorial
	 * $params = array(
	 *   'quotes_id' => 0, //报价ID
	 * );
	 */
	public function quotes_review_after($params)
	{
		//检查参数
		$quotes_id = intval($params['quotes_id']);
		if( $quotes_id<1 )
		{
			return false;
		}
		
		//获取报价信息
		$task_quotes_obj = POCO::singleton('pai_task_quotes_class');
		$quotes_info = $task_quotes_obj->get_quotes_info($quotes_id);
		if( empty($quotes_info) )
		{
			return false;
		}
		$request_id = intval($quotes_info['request_id']);
		$seller_user_id = intval($quotes_info['user_id']);
		
		//获取需求信息
		$task_request_obj = POCO::singleton('pai_task_request_class');
		$request_info = $task_request_obj->get_request_info($request_id);
		if( empty($request_info))
		{
			return false;
		}
		$buyer_user_id = intval($request_info['user_id']);
		
		$cur_time = time();
		
		//2015年5月 首次评价商家赠券
		if( strtotime('2015-05-01 00:00:00')<=$cur_time && $cur_time<=strtotime('2015-05-31 23:59:59') )
		{
			$request_count = $task_request_obj->get_request_detail_list($buyer_user_id, 0, true, "is_review=1");
			if( $request_count==1 )
			{
				$coupon_give_obj = POCO::singleton('pai_coupon_give_class');
				$coupon_give_obj->submit_queue('Y2015M05D01_TASK_REVIEW_FIRST', '', $buyer_user_id, 0);
			}
		}
		
		return true;
	}
	
	/**
	 * 消息_提交_后
	 * @param array $params
	 * @return boolean
	 * @tutorial
	 * $params = array(
	 *   'message_id' => 0, //消息ID
	 * );
	 */
	public function message_submit_after($params)
	{
		//检查参数
		$message_id = intval($params['message_id']);
		if( $message_id<1 )
		{
			return false;
		}
		
		//获取消息信息
		$task_message_obj = POCO::singleton('pai_task_message_class');
		$message_info = $task_message_obj->get_message_info($message_id);
		if( empty($message_info) )
		{
			return false;
		}
		$quotes_id = intval($message_info['quotes_id']);
		$from_user_id = intval($message_info['from_user_id']);
		$to_user_id = intval($message_info['to_user_id']);
		
		//获取报价信息
		$task_quotes_obj = POCO::singleton('pai_task_quotes_class');
		$quotes_info = $task_quotes_obj->get_quotes_info($quotes_id);
		if( empty($quotes_info) )
		{
			return false;
		}
		$request_id = intval($quotes_info['request_id']);
		$seller_user_id = intval($quotes_info['user_id']);
		
		//获取需求信息
		$task_request_obj = POCO::singleton('pai_task_request_class');
		$request_info = $task_request_obj->get_request_info($request_id);
		if( empty($request_info))
		{
			return false;
		}
		$buyer_user_id = intval($request_info['user_id']);
		
		//卖家（商家）=> 给买家（用户）
		if( $to_user_id==$buyer_user_id && in_array($message_info['message_type'], array('message')) )
		{
			$from_nickname = get_user_nickname_by_user_id($from_user_id);
			$send_data = array();
			$send_data['media_type'] ='card';
			$send_data['card_style'] = 2;
			$send_data['card_text1'] = "{$from_nickname}给你发了一条新的消息";
			$send_data['card_title'] = '查看消息详情';
			$send_data['link_url'] = 'yueyue://goto?type=inner_app&pid=1220079&request_id=' . $request_id;
			$send_data['wifi_url'] = 'yueyue://goto?type=inner_app&pid=1220079&request_id=' . $request_id;
			$push_obj = POCO::singleton('pai_information_push');
			$push_obj->send_msg_for_system_v2(10006, $to_user_id, $send_data);
			//消息回复后给app发送离线信息
			$content = "你收到了一条新的回复。（点击进入报价列表，新的消息处）";
			send_offline_message($to_user_id,$content);
		}
		
		return true;
	}
	
	/**
	 * 推送引导_提交_后
	 * @param array $params
	 * @return boolean
	 * @tutorial
	 * $params = array(
	 *   'lead_id' => 0, //消息ID
	 * );
	 */
	public function lead_submit_after($params)
	{
		//检查参数
		$lead_id = intval($params['lead_id']);
		if( $lead_id<1 )
		{
			return false;
		}
		
		//获取引导信息
		$task_lead_obj = POCO::singleton('pai_task_lead_class');
		$lead_info = $task_lead_obj->get_lead_info($lead_id);
		if( empty($lead_info) )
		{
			return false;
		}
		$seller_user_id = intval($lead_info['user_id']);
		$request_id = intval($lead_info['request_id']);
		
		//获取需求信息
		$task_request_obj = POCO::singleton('pai_task_request_class');
		$request_info = $task_request_obj->get_request_info($request_id);
		if( empty($request_info))
		{
			return false;
		}
		$service_id = intval($request_info['service_id']);
		$buyer_user_id = intval($request_info['user_id']);
		$buyer_nickname = get_user_nickname_by_user_id($buyer_user_id);
		
		//获取服务信息
		$task_service_obj = POCO::singleton('pai_task_service_class');
		$service_info = $task_service_obj->get_service_info($service_id);
		if( empty($service_info) )
		{
			return false;
		}
		
		//获取卖家邮箱
		$task_seller_obj = POCO::singleton('pai_task_seller_class');
		$email = $task_seller_obj->get_seller_email($seller_user_id);
		if( strlen($email)<1 ) 
		{
			return false;
		}
		
		//获取问卷答案
		$task_questionnaire_obj = POCO::singleton('pai_task_questionnaire_class');
		$questionnaire_data = $task_questionnaire_obj->show_questionnaire_data($request_id);
		if( empty($questionnaire_data) || !is_array($questionnaire_data['data']) || empty($questionnaire_data['data']) )
		{
			return false;
		}
		$question_answer_content = '';
		$question_list = $questionnaire_data['data'];
		foreach ($question_list as $question_info)
		{
			$question_answer_content .= trim($question_info['titles']) . '<br />';
			$answer_list = $question_info['data'];
			if( !is_array($answer_list) ) $answer_list = array();
			foreach ($answer_list as $answer_info)
			{
				$question_answer_content .= trim($answer_info['titles']) . '<br />';
			}
			$question_answer_content .= '<br />';
		}
		
		$email_title = "约约提醒你有一个新的生意机会";
		$email_html = "约约提醒你有一个新的生意机会。<br /><br />
{$buyer_nickname}需要一个{$service_info['profession_name']}<br />
需求如下：<br />
{$question_answer_content}
点击 <a href=\"http://task.yueus.com/lead_detail.php?lead_id={$lead_id}\" target=\"_blank\">进入查看详情</a>
<br /><br />===============================================<br />
约约，最高效的摄影服务平台<br />
一站式满足个性化需求<br />
找模特、找摄影培训、找摄影场地、找摄影配套，上约约<br />
合作邮箱：iwantu@yueus.com<br />
约约官方电话：4000-82-9003<br />";

		$pai_email_obj = POCO::singleton('pai_email_class');
		$pai_email_obj->send_email($email, $email_title, $email_html);
		
		return true;
	}
	
}
