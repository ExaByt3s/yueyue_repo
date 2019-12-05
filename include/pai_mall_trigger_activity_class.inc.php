<?php
/**
 * 事件触发
 * 方法名规范：模块名称_事件_前/后
 * @author Henry
 * @copyright 2015-04-16
 */

class pai_mall_trigger_activity_class
{
	/**
	 * 构造函数
	 */
	public function __construct()
	{
	}

	/**
	 * 活动订单_提交_后
	 * @param array $params
	 * @return boolean
	 * $params = array(
	 *   'order_sn' => '',
	 * );
	 */
	public function submit_order_after($params)
	{
		//获取订单信息
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
		//非OA单，才发通知
		if( !in_array($order_info['referer'], array('oa', 'import')) )
		{
			//推送消息
			//买家 => 商家
			$send_data = array();
			$send_data['media_type'] = 'card';
			$send_data['card_style'] = 1;
			$send_data['card_text1'] = "下单了活动: " . $activity_info['activity_name'];
			$send_data['card_text2'] = '￥' . $order_info['total_amount'];
			$send_data['card_title'] = '查看订单详情';

			//商家 => 买家
			$to_send_data = array();
			$to_send_data['media_type'] = 'card';
			$to_send_data['card_style'] = 1;
			$to_send_data['card_text1'] = "下单了活动: " . $activity_info['activity_name'];
			$to_send_data['card_text2'] = '￥' . $order_info['total_amount'];
			$to_send_data['card_title'] = '等待支付';

			POCO::singleton('pai_information_push')->card_send_for_order($buyer_user_id, 'yuebuyer', $seller_user_id, 'yueseller', $send_data, $to_send_data, $order_sn);

			//微信公众号模板消息
			$template_data = array(
				'first' => '你已下单成功，待支付',
				'goods_name' => $order_info['activity_name'],
				'total_amount' => $order_info['total_amount'],
				'status_str' => $order_info['status_str'],
				'remark' => '点击完成支付',
			);
			$template_to_url = 'http://yp.yueus.com/mall/user/order/detail.php?order_sn=' . $order_sn;
			POCO::singleton('pai_weixin_pub_class')->message_template_send_by_user_id($buyer_user_id, 'G_PAI_WEIXIN_MALL_ORDER_STATUS', $template_data, $template_to_url);
		}

		/*********更新商品价格策略***********/
		$activity_id = intval($activity_info['activity_id']);
		if( $activity_id>0 )
		{
			$data = array(
				'bill_buy_num' => 1,
			);
			POCO::singleton('pai_mall_goods_class')->update_goods_statistical($activity_id, $data);
		}

		/*********更新订单统计报表***********/
		$comment_info = '';
		POCO::singleton('pai_mall_order_log_class')->add_order_log('submit_order',$order_id,$buyer_user_id,$seller_user_id,$order_info,$comment_info);

		return true;
	}

	/**
	 * 订单_改价_后
	 * @param $params
	 * @return bool
	 */
	public function change_order_price($params)
	{
		//获取订单信息
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
		//非OA单，才发通知
		if( !in_array($order_info['referer'], array('oa', 'import')) )
		{
			//买家 => 商家
			$to_send_data = array();
			$to_send_data['media_type'] = 'card';
			$to_send_data['card_style'] = 1;
			$to_send_data['card_text1'] = "修改了订单价格: " . $activity_info['activity_name'];
			$to_send_data['card_text2'] = '￥' . $order_info['total_amount'];
			$to_send_data['card_title'] = '查看订单详情';

			//商家 => 买家
			$send_data = array();
			$send_data['media_type'] = 'card';
			$send_data['card_style'] = 1;
			$send_data['card_text1'] = "修改了订单价格: " . $activity_info['activity_name'];
			$send_data['card_text2'] = '￥' . $order_info['total_amount'];
			$send_data['card_title'] = '等待支付';

			POCO::singleton('pai_information_push')->card_send_for_order($seller_user_id, 'yueseller', $buyer_user_id, 'yuebuyer', $send_data, $to_send_data, $order_sn);

			//微信公众号模板消息
			$template_data = array(
				'first' => '商家修改了订单价格',
				'goods_name' => $order_info['activity_name'],
				'total_amount' => $order_info['total_amount'],
				'status_str' => $order_info['status_str'],
				'remark' => '点击查看详情',
			);
			$template_to_url = 'http://yp.yueus.com/mall/user/order/detail.php?order_sn=' . $order_sn;
			POCO::singleton('pai_weixin_pub_class')->message_template_send_by_user_id($buyer_user_id, 'G_PAI_WEIXIN_MALL_ORDER_STATUS', $template_data, $template_to_url);
		}

		/*********更新订单统计报表***********/
		$comment_info = '';
		POCO::singleton('pai_mall_order_log_class')->add_order_log('change_order_price',$order_id,$buyer_user_id,$seller_user_id,$order_info,$comment_info);

		return true;
	}

	/**
	 * 买家支付_订单_后
	 * @param array $params
	 * @return boolean
	 * @tutorial
	 * $params = array(
	 *   'order_sn' => '',
	 * );
	 */
	public function pay_order_after($params)
	{
		//获取订单信息
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

		//非OA单，才发通知
		if( !in_array($order_info['referer'], array('oa', 'import')) && $order_info['is_auto_accept']==0 )
		{
			//商家 => 买家
			$send_data = array();
			$send_data['media_type'] = 'card';
			$send_data['card_style'] = 2;
			$send_data['card_text1'] = '已支付订单,请准时到现场扫码签到';
			$send_data['card_title'] = '出示签到码';

			//买家 => 商家
			$to_send_data = array();
			$to_send_data['media_type'] = 'card';
			$to_send_data['card_style'] = 2;
			$to_send_data['card_text1'] = '已支付订单,别忘了扫码签到确认收款';
			$to_send_data['card_title'] = '扫码签到';

			POCO::singleton('pai_information_push')->card_send_for_order($seller_user_id, 'yueseller', $buyer_user_id, 'yuebuyer', $send_data, $to_send_data, $order_sn);

			//微信公众号模板消息
			$template_data = array(
				'first' => '订单已支付，请准时到现场扫码签到，以完成交易',
				'goods_name' => $order_info['activity_name'],
				'total_amount' => $order_info['total_amount'],
				'status_str' => $order_info['status_str'],
				'remark' => '点击出示签到码',
			);
			$template_to_url = 'http://yp.yueus.com/mall/user/order/detail.php?order_sn=' . $order_sn;
			POCO::singleton('pai_weixin_pub_class')->message_template_send_by_user_id($buyer_user_id, 'G_PAI_WEIXIN_MALL_ORDER_STATUS', $template_data, $template_to_url);
		}

		/*********更新商品价格策略***********/
		$activity_id = intval($activity_info['activity_id']);
		if( $activity_id>0 )
		{
			$data = array(
				'bill_pay_num' => 1,
			);
			POCO::singleton('pai_mall_goods_class')->update_goods_statistical($activity_id, $data);
		}

		/*********更新订单统计报表***********/
		$comment_info = '';
		POCO::singleton('pai_mall_order_log_class')->add_order_log('pay_order',$order_id,$buyer_user_id,$seller_user_id,$order_info,$comment_info);

		return true;
	}

	/**
	 * 签到_订单_后
	 * @param array $params array('order_sn'=>'')
	 * @return boolean
	 */
	public function sign_order_after($params)
	{
		//获取订单信息
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

		//非OA单，才发通知
		if( !in_array($order_info['referer'], array('oa', 'import')) )
		{
			//商家 => 买家
			$send_data = array();
			$send_data['media_type'] = 'card';
			$send_data['card_style'] = 2;
			$send_data['card_text1'] = '已成功签到，活动完成后请评价商家哦';
			$send_data['card_title'] = '评价商家';
            if($order_pending_amount >= 100 && $cur_time>=strtotime('2015-11-10 00:00:00') && $cur_time<=strtotime('2015-12-10 23:59:59'))
            {
                $send_data['card_text1'] = '已成功签到，完成评价后最高可得100元优惠券';
                $send_data['card_title'] = '马上评价，100%得优惠券';
            }

			//买家 => 商家
			$to_send_data = array();
			$to_send_data['media_type'] = 'card';
			$to_send_data['card_style'] = 2;
			$to_send_data['card_text1'] = "已和{$order_info['buyer_name']}签到，账户即将收到款项";
			$to_send_data['card_title'] = '去评价买家';

			POCO::singleton('pai_information_push')->card_send_for_order($seller_user_id, 'yueseller', $buyer_user_id, 'yuebuyer', $send_data, $to_send_data, $order_sn);

			//微信公众号模板消息
			$template_data = array(
				'first' => "已成功签到，交易完成\r\n活动完成后请评价商家哦",
				'goods_name' => $order_info['activity_name'],
				'total_amount' => $order_info['total_amount'],
				'status_str' => $order_info['status_str'],
				'remark' => '点击评价商家',
			);
			$template_to_url = 'http://yp.yueus.com/mall/user/order/detail.php?order_sn=' . $order_sn;
			POCO::singleton('pai_weixin_pub_class')->message_template_send_by_user_id($buyer_user_id, 'G_PAI_WEIXIN_MALL_ORDER_STATUS', $template_data, $template_to_url);
		}

		/*********更新商品价格策略***********/
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

		/*************更新用户交易数据**************/
		$user_obj = POCO::singleton('pai_user_data_class');
		$user_obj->add_deal_times($buyer_user_id);
		$user_obj->add_consume_ammount($buyer_user_id, $order_info['pending_amount']);

		/*********更新订单统计报表***********/
		$comment_info = '';
		POCO::singleton('pai_mall_order_log_class')->add_order_log('sign_order',$order_id,$buyer_user_id,$seller_user_id,$order_info,$comment_info);

		/*********更新订单统计报表***********/
		$add_user_deal = POCO::singleton('pai_mall_follow_user_class')->add_user_deal($buyer_user_id, $seller_user_id);
		pai_log_class::add_log($add_user_deal, '$cal_quantity_sku_info', 'order_service');

		return true;
	}

	/**
	 * 买家评价_订单_后
	 * @param array $params array('order_sn'=>'', 'is_anonymous'=>0)
	 * @return boolean
	 */
	public function comment_order_for_buyer_after($params)
	{
		//获取订单信息
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

		//非匿名、非OA单，才发通知
		if( $params['is_anonymous']==0 && !in_array($order_info['referer'], array('oa', 'import')) )
		{
			//买家 => 商家
			$send_data = array();
			$send_data['media_type'] = 'card';
			$send_data['card_style'] = 2;
			$send_data['card_text1'] = '评价了你';
			$send_data['card_title'] = '查看订单详情';

			//商家 => 买家
			$to_send_data = array();
			$to_send_data['media_type'] = 'card';
			$to_send_data['card_style'] = 2;
			$to_send_data['card_text1'] = "评价了{$order_info['seller_name']}";
			$to_send_data['card_title'] = '查看订单详情';

			POCO::singleton('pai_information_push')->card_send_for_order($buyer_user_id, 'yuebuyer', $seller_user_id, 'yueseller', $send_data, $to_send_data, $order_sn);

			//微信公众号模板消息
			$template_data = array(
				'first' => "你已评价了{$order_info['seller_name']}",
				'goods_name' => $order_info['activity_name'],
				'total_amount' => $order_info['total_amount'],
				'status_str' => $order_info['status_str'],
				'remark' => '点击查看详情',
			);
			$template_to_url = 'http://yp.yueus.com/mall/user/order/detail.php?order_sn=' . $order_sn;
			POCO::singleton('pai_weixin_pub_class')->message_template_send_by_user_id($buyer_user_id, 'G_PAI_WEIXIN_MALL_ORDER_STATUS', $template_data, $template_to_url);
		}

		/*********更新订单统计报表***********/
		//获取消费者对商家订单的商品评价（这里需注意下，和订单评论逻辑不同）
		$comment_info = POCO::singleton('pai_mall_comment_class')->get_seller_comment_info($order_id,$activity_info['activity_id']);
		POCO::singleton('pai_mall_order_log_class')->add_order_log('comment_order_for_buyer',$order_id,$buyer_user_id,$seller_user_id,$order_info,$comment_info);
		return true;
	}

	/**
	 * 卖家评价_订单_后
	 * @param array $params array('order_sn'=>'','is_anonymous'=>0)
	 * @return boolean
	 */
	public function comment_order_for_seller_after($params)
	{
		//获取订单信息
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

		//非匿名、非OA单，才发通知
		if( $params['is_anonymous']==0 && !in_array($order_info['referer'], array('oa', 'import')) )
		{
			//商家 => 买家
			$send_data = array();
			$send_data['media_type'] = 'card';
			$send_data['card_style'] = 2;
			$send_data['card_text1'] = '评价了你';
			$send_data['card_title'] = '查看订单详情';

			//买家 => 商家
			$to_send_data = array();
			$to_send_data['media_type'] = 'card';
			$to_send_data['card_style'] = 2;
			$to_send_data['card_text1'] = "评价了{$order_info['buyer_name']}";
			$to_send_data['card_title'] = '查看订单详情';

			POCO::singleton('pai_information_push')->card_send_for_order($seller_user_id, 'yueseller', $buyer_user_id, 'yuebuyer', $send_data, $to_send_data, $order_sn);

			//微信公众号模板消息
			$order_full_info = $order_obj->get_order_full_info($order_sn);
			$seller_name = get_user_nickname_by_user_id($seller_user_id);
			$template_data = array(
				'first' => "{$seller_name}评价了你",
				'goods_name' => $order_info['activity_name'],
				'total_amount' => $order_info['total_amount'],
				'status_str' => $order_info['status_str'],
				'remark' => '点击查看详情',
			);
			$template_to_url = 'http://yp.yueus.com/mall/user/order/detail.php?order_sn=' . $order_sn;
			POCO::singleton('pai_weixin_pub_class')->message_template_send_by_user_id($buyer_user_id, 'G_PAI_WEIXIN_MALL_ORDER_STATUS', $template_data, $template_to_url);
		}

		/*********更新订单统计报表***********/
		//获取商家对消费者订单的商品评价
		$comment_info = POCO::singleton('pai_mall_comment_class')->get_buyer_comment_info($order_id,$activity_info['activity_id']);
		POCO::singleton('pai_mall_order_log_class')->add_order_log('comment_order_for_seller',$order_id,$buyer_user_id,$seller_user_id,$order_info,$comment_info);

		return true;
	}

	/**
	 * 买家关闭_待支付订单_后
	 * @param array $params
	 * @return boolean
	 * @tutorial
	 * $params = array(
	 *   'order_sn' => '',
	 * );
	 */
	public function close_wait_pay_order_for_buyer_after($params)
	{
		//获取订单信息
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

		//非OA单，才发通知
		if( !in_array($order_info['referer'], array('oa', 'import')) )
		{
			//买家 => 商家
			$send_data = array();
			$send_data['media_type'] = 'card';
			$send_data['card_style'] = 2;
			$send_data['card_text1'] = '已关闭订单';
			$send_data['card_title'] = '点击查看详情';

			//商家 => 买家
			$to_send_data = array();
			$to_send_data['media_type'] = 'card';
			$to_send_data['card_style'] = 2;
			$to_send_data['card_text1'] = '已关闭订单';
			$to_send_data['card_title'] = '点击查看详情';

			POCO::singleton('pai_information_push')->card_send_for_order($buyer_user_id, 'yuebuyer', $seller_user_id, 'yueseller', $send_data, $to_send_data, $order_sn);

			//微信公众号模板消息
			$template_data = array(
				'first' => '你已关闭订单',
				'goods_name' => $order_info['activity_name'],
				'total_amount' => $order_info['total_amount'],
				'status_str' => $order_info['status_str'],
				'remark' => '点击查看详情',
			);
			$template_to_url = 'http://yp.yueus.com/mall/user/order/detail.php?order_sn=' . $order_sn;
			POCO::singleton('pai_weixin_pub_class')->message_template_send_by_user_id($buyer_user_id, 'G_PAI_WEIXIN_MALL_ORDER_STATUS', $template_data, $template_to_url);
		}

		/*********更新订单统计报表***********/
		$comment_info = '';
		POCO::singleton('pai_mall_order_log_class')->add_order_log('close_wait_pay_order_for_buyer',$order_id,$buyer_user_id,$seller_user_id,$order_info,$comment_info);

		return true;
	}

	/**
	 * 卖家关闭_待支付订单_后
	 * @param array $params
	 * @return boolean
	 * @tutorial
	 * $params = array(
	 *   'order_sn' => '',
	 * );
	 */
	public function close_wait_pay_order_for_seller_after($params)
	{
		//获取订单信息
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

		//非OA单，才发通知
		if( !in_array($order_info['referer'], array('oa', 'import')) )
		{
			//商家 => 买家
			$send_data = array();
			$send_data['media_type'] = 'card';
			$send_data['card_style'] = 2;
			$send_data['card_text1'] = '已关闭订单';
			$send_data['card_title'] = '点击查看详情';

			//买家 => 商家
			$to_send_data = array();
			$to_send_data['media_type'] = 'card';
			$to_send_data['card_style'] = 2;
			$to_send_data['card_text1'] = '已关闭订单';
			$to_send_data['card_title'] = '点击查看详情';

			POCO::singleton('pai_information_push')->card_send_for_order($seller_user_id, 'yueseller', $buyer_user_id, 'yuebuyer', $send_data, $to_send_data, $order_sn);

			//微信公众号模板消息
			$template_data = array(
				'first' => '商家已关闭订单',
				'goods_name' => $order_info['activity_name'],
				'total_amount' => $order_info['total_amount'],
				'status_str' => $order_info['status_str'],
				'remark' => '点击查看详情',
			);
			$template_to_url = 'http://yp.yueus.com/mall/user/order/detail.php?order_sn=' . $order_sn;
			POCO::singleton('pai_weixin_pub_class')->message_template_send_by_user_id($buyer_user_id, 'G_PAI_WEIXIN_MALL_ORDER_STATUS', $template_data, $template_to_url);
		}

		/*********更新订单统计报表***********/
		$comment_info = '';
		POCO::singleton('pai_mall_order_log_class')->add_order_log('close_wait_pay_order_for_seller',$order_id,$buyer_user_id,$seller_user_id,$order_info,$comment_info);

		return true;
	}

	/**
	 * 买家关闭_待确认订单_后
	 * @param array $params
	 * @return boolean
	 * @tutorial
	 * $params = array(
	 *   'order_sn' => '',
	 * );
	 */
	public function close_wait_confirm_order_for_buyer_after($params)
	{
		//获取订单信息
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

		//非OA单，才发通知
		if( !in_array($order_info['referer'], array('oa', 'import')) )
		{
			//买家 => 商家
			$send_data = array();
			$send_data['media_type'] = 'card';
			$send_data['card_style'] = 2;
			$send_data['card_text1'] = '已取消订单';
			$send_data['card_title'] = '查看订单详情';

			//商家 => 买家
			$to_send_data = array();
			$to_send_data['media_type'] = 'card';
			$to_send_data['card_style'] = 2;
			$to_send_data['card_text1'] = '已取消订单';
			$to_send_data['card_title'] = '查看订单详情';

			POCO::singleton('pai_information_push')->card_send_for_order($buyer_user_id, 'yuebuyer', $seller_user_id, 'yueseller', $send_data, $to_send_data, $order_sn);

			//微信公众号模板消息
			$template_data = array(
				'first' => '你已取消订单',
				'goods_name' => $order_info['activity_name'],
				'total_amount' => $order_info['total_amount'],
				'status_str' => $order_info['status_str'],
				'remark' => '点击查看详情',
			);
			$template_to_url = 'http://yp.yueus.com/mall/user/order/detail.php?order_sn=' . $order_sn;
			POCO::singleton('pai_weixin_pub_class')->message_template_send_by_user_id($buyer_user_id, 'G_PAI_WEIXIN_MALL_ORDER_STATUS', $template_data, $template_to_url);
		}

		/*********更新订单统计报表***********/
		$comment_info = '';
		POCO::singleton('pai_mall_order_log_class')->add_order_log('close_wait_confirm_order_for_buyer',$order_id,$buyer_user_id,$seller_user_id,$order_info,$comment_info);

		/*********更新商品价格策略***********/
		$this->_close_order_after($activity_info);

		return true;
	}

	/**
	 * 卖家关闭_待确认订单_后
	 * @param array $params array('order_sn'=>'')
	 * @return boolean
	 */
	public function close_wait_confirm_order_for_seller_after($params)
	{
		//获取订单信息
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

		//非OA单，才发通知
		if( !in_array($order_info['referer'], array('oa', 'import')) )
		{
			//商家 => 买家
			$send_data = array();
			$send_data['media_type'] = 'card';
			$send_data['card_style'] = 2;
			$send_data['card_text1'] = '已拒绝订单';
			$send_data['card_title'] = '查看订单详情';

			//买家 => 商家
			$to_send_data = array();
			$to_send_data['media_type'] = 'card';
			$to_send_data['card_style'] = 2;
			$to_send_data['card_text1'] = '已拒绝订单';
			$to_send_data['card_title'] = '查看订单详情';

			POCO::singleton('pai_information_push')->card_send_for_order($seller_user_id, 'yueseller', $buyer_user_id, 'yuebuyer', $send_data, $to_send_data, $order_sn);

			//微信公众号模板消息
			$template_data = array(
				'first' => '订单被拒绝',
				'goods_name' => $order_info['activity_name'],
				'total_amount' => $order_info['total_amount'],
				'status_str' => $order_info['status_str'],
				'remark' => '点击查看详情',
			);
			$template_to_url = 'http://yp.yueus.com/mall/user/order/detail.php?order_sn=' . $order_sn;
			POCO::singleton('pai_weixin_pub_class')->message_template_send_by_user_id($buyer_user_id, 'G_PAI_WEIXIN_MALL_ORDER_STATUS', $template_data, $template_to_url);
		}

		/*********更新订单统计报表***********/
		$comment_info = '';
		POCO::singleton('pai_mall_order_log_class')->add_order_log('close_wait_confirm_order_for_seller',$order_id,$buyer_user_id,$seller_user_id,$order_info,$comment_info);

		/*********更新商品价格策略***********/
		$this->_close_order_after($activity_info);

		return true;
	}

	/**
	 * 买家关闭_待签到订单_后
	 * @param array $params
	 * @return boolean
	 * @tutorial
	 * $params = array(
	 *   'order_sn' => '',
	 * );
	 */
	public function close_wait_sign_order_for_buyer_after($params)
	{
		//获取订单信息
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

		//非OA单，才发通知
		if( !in_array($order_info['referer'], array('oa', 'import')) )
		{
			//买家 => 商家
			$send_data = array();
			$send_data['media_type'] = 'card';
			$send_data['card_style'] = 2;
			$send_data['card_text1'] = '已在有效时间内申请退款';
			$send_data['card_title'] = '查看订单详情';

			//商家 => 买家
			$to_send_data = array();
			$to_send_data['media_type'] = 'card';
			$to_send_data['card_style'] = 2;
			$to_send_data['card_text1'] = '已在有效时间内申请退款';
			$to_send_data['card_title'] = '查看订单详情';

			POCO::singleton('pai_information_push')->card_send_for_order($buyer_user_id, 'yuebuyer', $seller_user_id, 'yueseller', $send_data, $to_send_data, $order_sn);

			//微信公众号模板消息
			$template_data = array(
				'first' => '已在有效时间内申请退款成功',
				'goods_name' => $order_info['activity_name'],
				'total_amount' => $order_info['total_amount'],
				'status_str' => $order_info['status_str'],
				'remark' => '点击查看详情',
			);
			$template_to_url = 'http://yp.yueus.com/mall/user/order/detail.php?order_sn=' . $order_sn;
			POCO::singleton('pai_weixin_pub_class')->message_template_send_by_user_id($buyer_user_id, 'G_PAI_WEIXIN_MALL_ORDER_STATUS', $template_data, $template_to_url);
		}

		/*********更新订单统计报表***********/
		$comment_info = '';
		POCO::singleton('pai_mall_order_log_class')->add_order_log('close_wait_sign_order_for_buyer',$order_id,$buyer_user_id,$seller_user_id,$order_info,$comment_info);

		/*********更新商品价格策略***********/
		$this->_close_order_after($activity_info);

		return true;
	}

	/**
	 * 系统关闭_待签到订单_后（管理员关闭距离活动时间不足十二小时）
	 * @param array $params
	 * @return boolean
	 * @tutorial
	 * $params = array(
	 *   'order_sn' => '',
	 * );
	 */
	public function close_wait_sign_order_for_system_after($params)
	{
		//获取订单信息
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

		//非OA单，才发通知
		if( !in_array($order_info['referer'], array('oa', 'import')) )
		{
			//约约小助手=>买家
			$send_data = array();
			$send_data['media_type'] = 'notify';
			$send_data['content']    = '你的订单已被关闭（订单号：'.$order_sn.'），已付款项将自动转入你的账户，请查收。如有疑问请联系约约客服。';
			$link_url = 'http://yp.yueus.com/mall/user/order/detail.php?order_sn=' . $order_sn;
			$wifi_link_url = 'http://yp-wifi.yueus.com/mall/user/order/detail.php?order_sn=' . $order_sn;
			$send_data['link_url']   = 'yueyue://goto?type=inner_web&showtitle=2&url=' . urlencode($link_url) . '$wifi_url=' . urlencode($wifi_link_url);
			POCO::singleton('pai_information_push')->message_sending_for_system($buyer_user_id, $send_data, 10002);

			//约约小助手=>商家
			$send_data = array();
			$send_data['media_type'] = 'notify';
			$send_data['content']    = '你有一笔订单已被关闭（订单号：'.$order_sn.'），如有疑问请联系约约客服。';
			$send_data['link_url']   = 'yueseller://goto?order_sn=' . $order_sn . '&pid=1250022&type=inner_app';
			POCO::singleton('pai_information_push')->message_sending_for_system($seller_user_id, $send_data, 10002, 'yueseller');
		}

		//发送短信
		$pai_sms_obj = POCO::singleton('pai_sms_class');
		//系统关闭订单，提醒商家
		$sms_data = array(
			'order_sn' => $order_sn,
		);
		$pai_sms_obj->send_sms($seller_user_phone, 'G_PAI_MALL_ORDER_CLOSE_WAIT_SIGN_FOR_SYSTEM_SELLER', $sms_data);
		//系统关闭订单，提醒消费者
		$sms_data = array(
			'order_sn' => $order_sn,
		);
		$pai_sms_obj->send_sms($buy_user_phone, 'G_PAI_MALL_ORDER_CLOSE_WAIT_SIGN_FOR_SYSTEM_BUYER', $sms_data);

		/*********更新订单统计报表***********/
		$comment_info = '';
		POCO::singleton('pai_mall_order_log_class')->add_order_log('close_wait_sign_order_for_system',$order_id,$buyer_user_id,$seller_user_id,$order_info,$comment_info);

		/*********更新商品价格策略***********/
		$this->_close_order_after($activity_info);

		return true;
	}

	/**
	 * @param $activity_info
	 * @return bool
	 */
	public function _close_order_after($activity_info)
	{
		/*********更新商品价格策略***********/
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
