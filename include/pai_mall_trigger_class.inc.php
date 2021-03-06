<?php
/**
 * 事件触发
 * 方法名规范：模块名称_事件_前/后
 * @author Henry
 * @copyright 2015-04-16
 */

class pai_mall_trigger_class
{
	/**
	 * 构造函数
	 */
	public function __construct()
	{
	}

	/**
	 * 订单_提交_后
	 * @param array $params
	 * @return boolean
	 * $params = array(
	 *   'order_sn' => '',
	 * );
	 */
	public function submit_order_after($order_info)
	{
		//获取订单信息
		if( empty($order_info) )
		{
			return false;
		}
		$order_id = intval($order_info['order_id']);
		$goods_info = $order_info['detail_list'][0];
		$buyer_user_id = $order_info['buyer_user_id'];
		$seller_user_id = $order_info['seller_user_id'];
		$order_sn = intval($order_info['order_sn']);

		//非OA单，才发通知
		if( !in_array($order_info['referer'], array('oa')) )
		{
			//推送消息
			//买家 => 商家
			$send_data = array();
			$send_data['media_type'] = 'card';
			$send_data['card_style'] = 1;
			$send_data['card_text1'] = "下单了服务: " . $goods_info['goods_name'];
			$send_data['card_text2'] = '￥' . $order_info['total_amount'];
			$send_data['card_title'] = '查看订单详情';

			//商家 => 买家
			$to_send_data = array();
			$to_send_data['media_type'] = 'card';
			$to_send_data['card_style'] = 1;
			$to_send_data['card_text1'] = "下单了服务: " . $goods_info['goods_name'];
			$to_send_data['card_text2'] = '￥' . $order_info['total_amount'];
			$to_send_data['card_title'] = '等待支付';

			//日志
			pai_log_class::add_log($send_data, 'submit_order_after', 'mall_trigger');

			POCO::singleton('pai_information_push')->card_send_for_order($buyer_user_id, 'yuebuyer', $seller_user_id, 'yueseller', $send_data, $to_send_data, $order_sn);

			//微信公众号模板消息
			$template_data = array(
				'first' => '你已下单成功，待支付',
				'goods_name' => $order_info['goods_name'],
				'total_amount' => $order_info['total_amount'],
				'status_str' => $order_info['status_str'],
				'remark' => '点击完成支付',
			);
			$template_to_url = 'http://yp.yueus.com/mall/user/order/detail.php?order_sn=' . $order_sn;
			POCO::singleton('pai_weixin_pub_class')->message_template_send_by_user_id($buyer_user_id, 'G_PAI_WEIXIN_MALL_ORDER_STATUS', $template_data, $template_to_url);
		}

		/*********更新商品价格策略***********/
		$goods_id = intval($goods_info['goods_id']);
		if( $goods_id>0 )
		{
			$data = array(
				'bill_buy_num' => 1,
			);
			POCO::singleton('pai_mall_goods_class')->update_goods_statistical($goods_id, $data);
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
	public function change_order_price_after($order_info)
	{
		//获取订单信息
		if( empty($order_info) )
		{
			return false;
		}
		$order_id = intval($order_info['order_id']);
		$goods_info = $order_info['detail_list'][0];
		$buyer_user_id = $order_info['buyer_user_id'];
		$seller_user_id = $order_info['seller_user_id'];
		$order_sn = intval($order_info['order_sn']);

		//非OA单，才发通知
		if( !in_array($order_info['referer'], array('oa')) )
		{
			//买家 => 商家
			$to_send_data = array();
			$to_send_data['media_type'] = 'card';
			$to_send_data['card_style'] = 1;
			$to_send_data['card_text1'] = "修改了订单价格: " . $goods_info['goods_name'];
			$to_send_data['card_text2'] = '￥' . $order_info['total_amount'];
			$to_send_data['card_title'] = '查看订单详情';

			//商家 => 买家
			$send_data = array();
			$send_data['media_type'] = 'card';
			$send_data['card_style'] = 1;
			$send_data['card_text1'] = "修改了订单价格: " . $goods_info['goods_name'];
			$send_data['card_text2'] = '￥' . $order_info['total_amount'];
			$send_data['card_title'] = '等待支付';

			POCO::singleton('pai_information_push')->card_send_for_order($seller_user_id, 'yueseller', $buyer_user_id, 'yuebuyer', $send_data, $to_send_data, $order_sn);

			//微信公众号模板消息
			$template_data = array(
				'first' => '商家修改了订单价格',
				'goods_name' => $order_info['goods_name'],
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
		$goods_info = $order_info['detail_list'][0];
		$buyer_user_id = $order_info['buyer_user_id'];
		$seller_user_id = $order_info['seller_user_id'];

		//非OA单，才发通知
		if( !in_array($order_info['referer'], array('oa')) && $order_info['is_auto_accept']==0 )
		{
			//买家 => 商家
			$send_data = array();
			$send_data['media_type'] = 'card';
			$send_data['card_style'] = 2;
			$send_data['card_text1'] = '已支付订单';
			$send_data['card_title'] = '接受或拒绝';

			//商家 => 买家
			$to_send_data = array();
			$to_send_data['media_type'] = 'card';
			$to_send_data['card_style'] = 2;
			$to_send_data['card_text1'] = '已支付订单';
			$to_send_data['card_title'] = '等待商家接受';

			POCO::singleton('pai_information_push')->card_send_for_order($buyer_user_id, 'yuebuyer', $seller_user_id, 'yueseller', $send_data, $to_send_data, $order_sn);

			//微信公众号模板消息
			$template_data = array(
				'first' => '你已支付订单，等待商家接受',
				'goods_name' => $order_info['goods_name'],
				'total_amount' => $order_info['total_amount'],
				'status_str' => $order_info['status_str'],
				'remark' => '点击查看详情',
			);
			$template_to_url = 'http://yp.yueus.com/mall/user/order/detail.php?order_sn=' . $order_sn;
			POCO::singleton('pai_weixin_pub_class')->message_template_send_by_user_id($buyer_user_id, 'G_PAI_WEIXIN_MALL_ORDER_STATUS', $template_data, $template_to_url);
		}

		/*********更新商品价格策略***********/
		$goods_id = intval($goods_info['goods_id']);
		if( $goods_id>0 )
		{
			$data = array(
				'bill_pay_num' => 1,
			);
			POCO::singleton('pai_mall_goods_class')->update_goods_statistical($goods_id, $data);
		}

		/*********更新订单统计报表***********/
		$comment_info = '';
		POCO::singleton('pai_mall_order_log_class')->add_order_log('pay_order',$order_id,$buyer_user_id,$seller_user_id,$order_info,$comment_info);

		return true;
	}

	/**
	 * 卖家接受_订单_后
	 * @param array $params array('order_sn'=>'')
	 * @return boolean
	 */
	public function accept_order_after($params)
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
		$goods_info = $order_info['detail_list'][0];
		$buyer_user_id = $order_info['buyer_user_id'];
		$seller_user_id = $order_info['seller_user_id'];

		//非OA单，才发通知
		if( !in_array($order_info['referer'], array('oa')) && $order_info['is_auto_sign']==0 )
		{
			//商家 => 买家
			$send_data = array();
			$send_data['media_type'] = 'card';
			$send_data['card_style'] = 2;
			$send_data['card_text1'] = '已接受订单,请准时到现场扫码签到';
			$send_data['card_title'] = '出示签到码';

			//买家 => 商家
			$to_send_data = array();
			$to_send_data['media_type'] = 'card';
			$to_send_data['card_style'] = 2;
			$to_send_data['card_text1'] = '已接受订单,别忘了扫码签到确认收款';
			$to_send_data['card_title'] = '扫码签到';

			POCO::singleton('pai_information_push')->card_send_for_order($seller_user_id, 'yueseller', $buyer_user_id, 'yuebuyer', $send_data, $to_send_data, $order_sn);

			//微信公众号模板消息
			$template_data = array(
				'first' => '订单已被接受，请准时到现场扫码签到，以完成交易',
				'goods_name' => $order_info['goods_name'],
				'total_amount' => $order_info['total_amount'],
				'status_str' => $order_info['status_str'],
				'remark' => '点击出示签到码',
			);
			$template_to_url = 'http://yp.yueus.com/mall/user/order/detail.php?order_sn=' . $order_sn;
			POCO::singleton('pai_weixin_pub_class')->message_template_send_by_user_id($buyer_user_id, 'G_PAI_WEIXIN_MALL_ORDER_STATUS', $template_data, $template_to_url);
		}

		/*********更新订单统计报表***********/
		$comment_info = '';
		POCO::singleton('pai_mall_order_log_class')->add_order_log('accept_order',$order_id,$buyer_user_id,$seller_user_id,$order_info,$comment_info);

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
		$goods_info = $order_info['detail_list'][0];
		$buyer_user_id = $order_info['buyer_user_id'];
		$seller_user_id = $order_info['seller_user_id'];
		$order_pending_amount = $order_info['pending_amount'];

		//非OA单，才发通知
		if( !in_array($order_info['referer'], array('oa')) )
		{
			//商家 => 买家
			$send_data = array();
			$send_data['media_type'] = 'card';
			$send_data['card_style'] = 2;
			$send_data['card_text1'] = '已成功签到，服务完成后请评价商家哦';
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
				'first' => "已成功签到，交易完成\r\n服务完成后请评价商家哦",
				'goods_name' => $order_info['goods_name'],
				'total_amount' => $order_info['total_amount'],
				'status_str' => $order_info['status_str'],
				'remark' => '点击评价商家',
			);
			$template_to_url = 'http://yp.yueus.com/mall/user/order/detail.php?order_sn=' . $order_sn;
			POCO::singleton('pai_weixin_pub_class')->message_template_send_by_user_id($buyer_user_id, 'G_PAI_WEIXIN_MALL_ORDER_STATUS', $template_data, $template_to_url);
		}

		/*********更新商品价格策略***********/
		$goods_id = intval($goods_info['goods_id']);
		$price = $goods_info['prices'];
		if( $goods_id>0 )
		{
			$data = array(
				'bill_finish_num' => 1,
				'prices' => $price,
			);
			POCO::singleton('pai_mall_goods_class')->update_goods_statistical($goods_id, $data);
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
		$goods_info = $order_info['detail_list'][0];
		$buyer_user_id = $order_info['buyer_user_id'];
		$seller_user_id = $order_info['seller_user_id'];
		$order_total_amount = $order_info['total_amount'];
		$order_pending_amount = $order_info['pending_amount'];
		
		$cur_time = time();

		//非匿名、非OA单，才发通知
		if( $params['is_anonymous']==0 && !in_array($order_info['referer'], array('oa')) )
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
				'goods_name' => $order_info['goods_name'],
				'total_amount' => $order_info['total_amount'],
				'status_str' => $order_info['status_str'],
				'remark' => '点击查看详情',
			);
			$template_to_url = 'http://yp.yueus.com/mall/user/order/detail.php?order_sn=' . $order_sn;
			POCO::singleton('pai_weixin_pub_class')->message_template_send_by_user_id($buyer_user_id, 'G_PAI_WEIXIN_MALL_ORDER_STATUS', $template_data, $template_to_url);
		}

		/*********更新订单统计报表***********/
		//获取消费者对商家订单的商品评价（这里需注意下，和订单评论逻辑不同）
		$comment_info = POCO::singleton('pai_mall_comment_class')->get_seller_comment_info($order_id,$goods_info['goods_id']);
		POCO::singleton('pai_mall_order_log_class')->add_order_log('comment_order_for_buyer',$order_id,$buyer_user_id,$seller_user_id,$order_info,$comment_info);

        //消费满额赠券，每个用户最多获得8次，10月
        if( $order_total_amount>=300 && in_array($type_id, array(3, 12, 31)) && $cur_time>=strtotime('2015-10-13 00:00:00') && $cur_time<=strtotime('2015-11-09 23:59:59') )
        {
            $give_code = 'Y2015M10D16_CONSUMPTION_BACK_300';
            $where_str = "give_code='{$give_code}' AND user_id={$buyer_user_id}";
            $coupon_give_obj = POCO::singleton('pai_coupon_give_class');
            $count_queue = $coupon_give_obj->get_queue_list(-1, true, $where_str);
            if( $count_queue<8 )
            {
                $coupon_give_obj->submit_queue($give_code, '', $buyer_user_id, $order_id);
            }
        }

        //约培训消费满额赠券，每个用户最多获得1次，11月
        //合作的商家配置
        $seller_arr_temp = array(
            154408, 
            129342,
            195022, 
            188044, 
            209222, 
            205020,
            350029, 
            150116, 
        );

        if( $order_total_amount >= 3000 && in_array($seller_user_id, $seller_arr_temp) && $type_id==5 && $cur_time>=strtotime('2015-11-07 00:00:00') && $cur_time<=strtotime('2015-11-30 23:59:59') )
        {
            $give_code = 'Y2015M11D09_CONSUMPTION_BACK_100_CATE_5';
            $coupon_give_obj = POCO::singleton('pai_coupon_give_class');
            $coupon_give_obj->submit_queue($give_code, '', $buyer_user_id, $order_id);
        }
        elseif( $order_total_amount >= 2000 && in_array($seller_user_id, $seller_arr_temp) && $type_id==5 && $cur_time>=strtotime('2015-11-07 00:00:00') && $cur_time<=strtotime('2015-11-30 23:59:59') )
        {
            $give_code = 'Y2015M11D09_CONSUMPTION_BACK_50_CATE_5';
            $coupon_give_obj = POCO::singleton('pai_coupon_give_class');
            $coupon_give_obj->submit_queue($give_code, '', $buyer_user_id, $order_id);
        }
        elseif( $order_pending_amount >= 1000 && $cur_time>=strtotime('2015-11-10 00:00:00') && $cur_time<=strtotime('2015-12-10 23:59:59') )
        {
            $give_code = 'Y2015M11D09_CONSUMPTION_BACK_68';
            $where_str = "give_code='{$give_code}' AND user_id={$buyer_user_id}";
            $coupon_give_obj = POCO::singleton('pai_coupon_give_class');
            $count_queue = $coupon_give_obj->get_queue_list(-1, true, $where_str);
            if( $count_queue<5 ) //防刷单限制为5次
            {
                $coupon_give_obj->submit_queue($give_code, '', $buyer_user_id, $order_id);
            }
        }
        elseif( $order_pending_amount >= 600 && $cur_time>=strtotime('2015-11-10 00:00:00') && $cur_time<=strtotime('2015-12-10 23:59:59') )
        {
            $give_code = 'Y2015M11D09_CONSUMPTION_BACK_48';
            $where_str = "give_code='{$give_code}' AND user_id={$buyer_user_id}";
            $coupon_give_obj = POCO::singleton('pai_coupon_give_class');
            $count_queue = $coupon_give_obj->get_queue_list(-1, true, $where_str);
            if( $count_queue<5 ) //防刷单限制为5次
            {
                $coupon_give_obj->submit_queue($give_code, '', $buyer_user_id, $order_id);
            }
        }
        elseif( $order_pending_amount >= 500 && $cur_time>=strtotime('2015-11-10 00:00:00') && $cur_time<=strtotime('2015-12-10 23:59:59') )
        {
            $give_code = 'Y2015M11D09_CONSUMPTION_BACK_38';
            $coupon_give_obj = POCO::singleton('pai_coupon_give_class');
            $coupon_give_obj->submit_queue($give_code, '', $buyer_user_id, $order_id);
        }
        elseif( $order_pending_amount >= 300 && $cur_time>=strtotime('2015-11-10 00:00:00') && $cur_time<=strtotime('2015-12-10 23:59:59') )
        {
            $give_code = 'Y2015M11D09_CONSUMPTION_BACK_28';
            $coupon_give_obj = POCO::singleton('pai_coupon_give_class');
            $coupon_give_obj->submit_queue($give_code, '', $buyer_user_id, $order_id);
        }
        elseif( $order_pending_amount >= 200 && $cur_time>=strtotime('2015-11-10 00:00:00') && $cur_time<=strtotime('2015-12-10 23:59:59') )
        {
            $give_code = 'Y2015M11D09_CONSUMPTION_BACK_18';
            $coupon_give_obj = POCO::singleton('pai_coupon_give_class');
            $coupon_give_obj->submit_queue($give_code, '', $buyer_user_id, $order_id);
        }
        elseif( $order_pending_amount >= 100 && $cur_time>=strtotime('2015-11-10 00:00:00') && $cur_time<=strtotime('2015-12-10 23:59:59') )
        {
            $give_code = 'Y2015M11D09_CONSUMPTION_BACK_8';
            $coupon_give_obj = POCO::singleton('pai_coupon_give_class');
            $coupon_give_obj->submit_queue($give_code, '', $buyer_user_id, $order_id);
        }

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
		$goods_info = $order_info['detail_list'][0];
		$buyer_user_id = $order_info['buyer_user_id'];
		$seller_user_id = $order_info['seller_user_id'];

		//非匿名、非OA单，才发通知
		if( $params['is_anonymous']==0 && !in_array($order_info['referer'], array('oa')) )
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
				'goods_name' => $order_info['goods_name'],
				'total_amount' => $order_info['total_amount'],
				'status_str' => $order_info['status_str'],
				'remark' => '点击查看详情',
			);
			$template_to_url = 'http://yp.yueus.com/mall/user/order/detail.php?order_sn=' . $order_sn;
			POCO::singleton('pai_weixin_pub_class')->message_template_send_by_user_id($buyer_user_id, 'G_PAI_WEIXIN_MALL_ORDER_STATUS', $template_data, $template_to_url);
		}

		/*********更新订单统计报表***********/
		//获取商家对消费者订单的商品评价
		$comment_info = POCO::singleton('pai_mall_comment_class')->get_buyer_comment_info($order_id,$goods_info['goods_id']);
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
		$goods_info = $order_info['detail_list'][0];
		$buyer_user_id = $order_info['buyer_user_id'];
		$seller_user_id = $order_info['seller_user_id'];

		//非OA单，才发通知
		if( !in_array($order_info['referer'], array('oa')) )
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
				'goods_name' => $order_info['goods_name'],
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
		$goods_info = $order_info['detail_list'][0];
		$buyer_user_id = $order_info['buyer_user_id'];
		$seller_user_id = $order_info['seller_user_id'];

		//非OA单，才发通知
		if( !in_array($order_info['referer'], array('oa')) )
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
				'first' => '对方已关闭订单',
				'goods_name' => $order_info['goods_name'],
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
		$goods_info = $order_info['detail_list'][0];
		$buyer_user_id = $order_info['buyer_user_id'];
		$seller_user_id = $order_info['seller_user_id'];

		//非OA单，才发通知
		if( !in_array($order_info['referer'], array('oa')) )
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
				'goods_name' => $order_info['goods_name'],
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
		$this->_close_order_after($goods_info);

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
		$goods_info = $order_info['detail_list'][0];
		$buyer_user_id = $order_info['buyer_user_id'];
		$seller_user_id = $order_info['seller_user_id'];

		//非OA单，才发通知
		if( !in_array($order_info['referer'], array('oa')) )
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
				'goods_name' => $order_info['goods_name'],
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
		$this->_close_order_after($goods_info);

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
		$goods_info = $order_info['detail_list'][0];
		$buyer_user_id = $order_info['buyer_user_id'];
		$seller_user_id = $order_info['seller_user_id'];

		//非OA单，才发通知
		if( !in_array($order_info['referer'], array('oa')) )
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
				'goods_name' => $order_info['goods_name'],
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
		$this->_close_order_after($goods_info);

		return true;
	}

	/**
	 * 系统关闭_待签到订单_后（管理员关闭距离服务时间不足十二小时）
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
		$goods_info = $order_info['detail_list'][0];
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
		if( !in_array($order_info['referer'], array('oa')) )
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
		$this->_close_order_after($goods_info);

		return true;
	}

	/**
	 * @param $goods_info
	 * @return bool
	 */
	public function _close_order_after($goods_info)
	{
		/*********更新商品价格策略***********/
		$goods_id = intval($goods_info['goods_id']);
		if( $goods_id>0 )
		{
			$data = array(
				'bill_pay_num' => -1,
			);
			POCO::singleton('pai_mall_goods_class')->update_goods_statistical($goods_id, $data);
		}
		return true;
	}

	/**
	 * 订单_改价_后
	 * @param $params
	 * @return bool
	 */
	public function change_order_price_after_v1($params)
	{
		//获取订单信息
		$order_obj = POCO::singleton('pai_mall_order_class');
		$order_info = $order_obj->get_order_info($params['order_sn']);
		if( empty($order_info) )
		{
			return false;
		}
		$detail_list = $order_obj->get_detail_list_all($order_info['order_id']);

		//非OA单，才发通知
		if( !in_array($order_info['referer'], array('oa')) )
		{
			//买家 => 商家
			$to_send_data = array();
			$to_send_data['media_type'] = 'card';
			$to_send_data['card_style'] = 1;
			$to_send_data['card_text1'] = "修改了订单价格: " . $detail_list[0]['goods_name'];
			$to_send_data['card_text2'] = '￥' . $order_info['total_amount'];
			$to_send_data['card_title'] = '查看订单详情';
			
			//商家 => 买家
			$send_data = array();
			$send_data['media_type'] = 'card';
			$send_data['card_style'] = 1;
			$send_data['card_text1'] = "修改了订单价格: " . $detail_list[0]['goods_name'];
			$send_data['card_text2'] = '￥' . $order_info['total_amount'];
			$send_data['card_title'] = '等待支付';

			POCO::singleton('pai_information_push')->card_send_for_order($order_info['seller_user_id'], 'yueseller', $order_info['buyer_user_id'], 'yuebuyer', $send_data, $to_send_data, $order_info['order_sn']);
			
			//微信公众号模板消息
			$order_full_info = $order_obj->get_order_full_info($order_info['order_sn']);
			$template_data = array(
				'first' => '商家修改了订单价格',
				'goods_name' => $order_full_info['goods_name'],
				'total_amount' => $order_full_info['total_amount'],
				'status_str' => $order_full_info['status_str'],
				'remark' => '点击查看详情',
			);
			$template_to_url = 'http://yp.yueus.com/mall/user/order/detail.php?order_sn=' . $order_full_info['order_sn'];
			POCO::singleton('pai_weixin_pub_class')->message_template_send_by_user_id($order_full_info['buyer_user_id'], 'G_PAI_WEIXIN_MALL_ORDER_STATUS', $template_data, $template_to_url);
		}

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
	public function pay_order_after_v1($params)
	{
		//获取订单信息
		$order_obj = POCO::singleton('pai_mall_order_class');
		$order_info = $order_obj->get_order_info($params['order_sn']);
		if( empty($order_info) )
		{
			return false;
		}
		$detail_list = $order_obj->get_detail_list_all($order_info['order_id']);
		
		//非OA单，才发通知
		if( !in_array($order_info['referer'], array('oa')) && $order_info['is_auto_accept']==0 )
		{
			//买家 => 商家
			$send_data = array();
			$send_data['media_type'] = 'card';
			$send_data['card_style'] = 2;
			$send_data['card_text1'] = '已支付订单';
			$send_data['card_title'] = '接受或拒绝';
			
			//商家 => 买家
			$to_send_data = array();
			$to_send_data['media_type'] = 'card';
			$to_send_data['card_style'] = 2;
			$to_send_data['card_text1'] = '已支付订单';
			$to_send_data['card_title'] = '等待商家接受';
			
			POCO::singleton('pai_information_push')->card_send_for_order($order_info['buyer_user_id'], 'yuebuyer', $order_info['seller_user_id'], 'yueseller', $send_data, $to_send_data, $order_info['order_sn']);
		
			//微信公众号模板消息
			$order_full_info = $order_obj->get_order_full_info($order_info['order_sn']);
			$template_data = array(
				'first' => '你已支付订单，等待商家接受',
				'goods_name' => $order_full_info['goods_name'],
				'total_amount' => $order_full_info['total_amount'],
				'status_str' => $order_full_info['status_str'],
				'remark' => '点击查看详情',
			);
			$template_to_url = 'http://yp.yueus.com/mall/user/order/detail.php?order_sn=' . $order_full_info['order_sn'];
			POCO::singleton('pai_weixin_pub_class')->message_template_send_by_user_id($order_full_info['buyer_user_id'], 'G_PAI_WEIXIN_MALL_ORDER_STATUS', $template_data, $template_to_url);
		}
		
		/*********更新商品价格策略***********/
		$goods_id = intval($detail_list[0]['goods_id']);
		if( $goods_id>0 )
		{
			$data = array(
				'bill_pay_num' => 1,
			);
			POCO::singleton('pai_mall_goods_class')->update_goods_statistical($goods_id, $data);
		}

		return true;
	}

	/**
	 * 卖家接受_订单_后
	 * @param array $params array('order_sn'=>'')
	 * @return boolean
	 */
	public function accept_order_after_v1($params)
	{
		//获取订单信息
		$order_obj = POCO::singleton('pai_mall_order_class');
		$order_info = $order_obj->get_order_info($params['order_sn']);
		if( empty($order_info) )
		{
			return false;
		}
		
		//非OA单，才发通知
		if( !in_array($order_info['referer'], array('oa')) && $order_info['is_auto_sign']==0 )
		{
			//商家 => 买家
			$send_data = array();
			$send_data['media_type'] = 'card';
			$send_data['card_style'] = 2;
			$send_data['card_text1'] = '已接受订单,请准时到现场扫码签到';
			$send_data['card_title'] = '出示签到码';
			
			//买家 => 商家
			$to_send_data = array();
			$to_send_data['media_type'] = 'card';
			$to_send_data['card_style'] = 2;
			$to_send_data['card_text1'] = '已接受订单,别忘了扫码签到确认收款';
			$to_send_data['card_title'] = '扫码签到';
			
			POCO::singleton('pai_information_push')->card_send_for_order($order_info['seller_user_id'], 'yueseller', $order_info['buyer_user_id'], 'yuebuyer', $send_data, $to_send_data, $order_info['order_sn']);
			
			//微信公众号模板消息
			$order_full_info = $order_obj->get_order_full_info($order_info['order_sn']);
			$template_data = array(
				'first' => '订单已被接受，请准时到现场扫码签到，以完成交易',
				'goods_name' => $order_full_info['goods_name'],
				'total_amount' => $order_full_info['total_amount'],
				'status_str' => $order_full_info['status_str'],
				'remark' => '点击出示签到码',
			);
			$template_to_url = 'http://yp.yueus.com/mall/user/order/detail.php?order_sn=' . $order_full_info['order_sn'];
			POCO::singleton('pai_weixin_pub_class')->message_template_send_by_user_id($order_full_info['buyer_user_id'], 'G_PAI_WEIXIN_MALL_ORDER_STATUS', $template_data, $template_to_url);
		}
		
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
	public function close_wait_pay_order_for_buyer_after_v1($params)
	{
		//获取订单信息
		$order_obj = POCO::singleton('pai_mall_order_class');
		$order_info = $order_obj->get_order_info($params['order_sn']);
		if( empty($order_info) )
		{
			return false;
		}
		
		//非OA单，才发通知
		if( !in_array($order_info['referer'], array('oa')) )
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
			
			POCO::singleton('pai_information_push')->card_send_for_order($order_info['buyer_user_id'], 'yuebuyer', $order_info['seller_user_id'], 'yueseller', $send_data, $to_send_data, $order_info['order_sn']);
			
			//微信公众号模板消息
			$order_full_info = $order_obj->get_order_full_info($order_info['order_sn']);
			$template_data = array(
				'first' => '你已关闭订单',
				'goods_name' => $order_full_info['goods_name'],
				'total_amount' => $order_full_info['total_amount'],
				'status_str' => $order_full_info['status_str'],
				'remark' => '点击查看详情',
			);
			$template_to_url = 'http://yp.yueus.com/mall/user/order/detail.php?order_sn=' . $order_full_info['order_sn'];
			POCO::singleton('pai_weixin_pub_class')->message_template_send_by_user_id($order_full_info['buyer_user_id'], 'G_PAI_WEIXIN_MALL_ORDER_STATUS', $template_data, $template_to_url);
		}
		
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
	public function close_wait_pay_order_for_seller_after_v1($params)
	{
		//获取订单信息
		$order_obj = POCO::singleton('pai_mall_order_class');
		$order_info = $order_obj->get_order_info($params['order_sn']);
		if( empty($order_info) )
		{
			return false;
		}
		
		//非OA单，才发通知
		if( !in_array($order_info['referer'], array('oa')) )
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
			
			POCO::singleton('pai_information_push')->card_send_for_order($order_info['seller_user_id'], 'yueseller', $order_info['buyer_user_id'], 'yuebuyer', $send_data, $to_send_data, $order_info['order_sn']);
		
			//微信公众号模板消息
			$order_full_info = $order_obj->get_order_full_info($order_info['order_sn']);
			$template_data = array(
				'first' => '对方已关闭订单',
				'goods_name' => $order_full_info['goods_name'],
				'total_amount' => $order_full_info['total_amount'],
				'status_str' => $order_full_info['status_str'],
				'remark' => '点击查看详情',
			);
			$template_to_url = 'http://yp.yueus.com/mall/user/order/detail.php?order_sn=' . $order_full_info['order_sn'];
			POCO::singleton('pai_weixin_pub_class')->message_template_send_by_user_id($order_full_info['buyer_user_id'], 'G_PAI_WEIXIN_MALL_ORDER_STATUS', $template_data, $template_to_url);
		}
		
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
	public function close_wait_confirm_order_for_buyer_after_v1($params)
	{
		//获取订单信息
		$order_obj = POCO::singleton('pai_mall_order_class');
		$order_info = $order_obj->get_order_info($params['order_sn']);
		if( empty($order_info) )
		{
			return false;
		}
		
		//非OA单，才发通知
		if( !in_array($order_info['referer'], array('oa')) )
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
			
			POCO::singleton('pai_information_push')->card_send_for_order($order_info['buyer_user_id'], 'yuebuyer', $order_info['seller_user_id'], 'yueseller', $send_data, $to_send_data, $order_info['order_sn']);
		
			//微信公众号模板消息
			$order_full_info = $order_obj->get_order_full_info($order_info['order_sn']);
			$template_data = array(
				'first' => '你已取消订单',
				'goods_name' => $order_full_info['goods_name'],
				'total_amount' => $order_full_info['total_amount'],
				'status_str' => $order_full_info['status_str'],
				'remark' => '点击查看详情',
			);
			$template_to_url = 'http://yp.yueus.com/mall/user/order/detail.php?order_sn=' . $order_full_info['order_sn'];
			POCO::singleton('pai_weixin_pub_class')->message_template_send_by_user_id($order_full_info['buyer_user_id'], 'G_PAI_WEIXIN_MALL_ORDER_STATUS', $template_data, $template_to_url);
		}
		
		return true;
	}
	
	/**
	 * 卖家关闭_待确认订单_后
	 * @param array $params array('order_sn'=>'')
	 * @return boolean
	 */
	public function close_wait_confirm_order_for_seller_after_v1($params)
	{
		//获取订单信息
		$order_obj = POCO::singleton('pai_mall_order_class');
		$order_info = $order_obj->get_order_info($params['order_sn']);
		if( empty($order_info) )
		{
			return false;
		}
		
		//非OA单，才发通知
		if( !in_array($order_info['referer'], array('oa')) )
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
			
			POCO::singleton('pai_information_push')->card_send_for_order($order_info['seller_user_id'], 'yueseller', $order_info['buyer_user_id'], 'yuebuyer', $send_data, $to_send_data, $params['order_sn']);
			
			//微信公众号模板消息
			$order_full_info = $order_obj->get_order_full_info($order_info['order_sn']);
			$template_data = array(
				'first' => '订单被拒绝',
				'goods_name' => $order_full_info['goods_name'],
				'total_amount' => $order_full_info['total_amount'],
				'status_str' => $order_full_info['status_str'],
				'remark' => '点击查看详情',
			);
			$template_to_url = 'http://yp.yueus.com/mall/user/order/detail.php?order_sn=' . $order_full_info['order_sn'];
			POCO::singleton('pai_weixin_pub_class')->message_template_send_by_user_id($order_full_info['buyer_user_id'], 'G_PAI_WEIXIN_MALL_ORDER_STATUS', $template_data, $template_to_url);
		}
		
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
	public function close_wait_sign_order_for_buyer_after_v1($params)
	{
		//获取订单信息
		$order_obj = POCO::singleton('pai_mall_order_class');
		$order_info = $order_obj->get_order_info($params['order_sn']);
		if( empty($order_info) )
		{
			return false;
		}
		
		//非OA单，才发通知
		if( !in_array($order_info['referer'], array('oa')) )
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
			
			POCO::singleton('pai_information_push')->card_send_for_order($order_info['buyer_user_id'], 'yuebuyer', $order_info['seller_user_id'], 'yueseller', $send_data, $to_send_data, $order_info['order_sn']);
		
			//微信公众号模板消息
			$order_full_info = $order_obj->get_order_full_info($order_info['order_sn']);
			$template_data = array(
				'first' => '已在有效时间内申请退款成功',
				'goods_name' => $order_full_info['goods_name'],
				'total_amount' => $order_full_info['total_amount'],
				'status_str' => $order_full_info['status_str'],
				'remark' => '点击查看详情',
			);
			$template_to_url = 'http://yp.yueus.com/mall/user/order/detail.php?order_sn=' . $order_full_info['order_sn'];
			POCO::singleton('pai_weixin_pub_class')->message_template_send_by_user_id($order_full_info['buyer_user_id'], 'G_PAI_WEIXIN_MALL_ORDER_STATUS', $template_data, $template_to_url);
		}
		
		return true;
	}
	
	/**
	 * 签到_订单_后
	 * @param array $params array('order_sn'=>'')
	 * @return boolean
	 */
	public function sign_order_after_v1($params)
	{
		//获取订单信息
		$order_obj = POCO::singleton('pai_mall_order_class');
		$order_info = $order_obj->get_order_info($params['order_sn']);
		if( empty($order_info) )
		{
			return false;
		}
		$detail_list = $order_obj->get_detail_list_all($order_info['order_id']);
		$buyer_user_id = intval($order_info['buyer_user_id']);
		
		//非OA单，才发通知
		if( !in_array($order_info['referer'], array('oa')) )
		{
			$buyer_name = get_user_nickname_by_user_id($buyer_user_id);
			
			//商家 => 买家
			$send_data = array();
			$send_data['media_type'] = 'card';
			$send_data['card_style'] = 2;
			$send_data['card_text1'] = '已成功签到，服务完成后请评价商家哦';
			$send_data['card_title'] = '评价商家';
			
			//买家 => 商家
			$to_send_data = array();
			$to_send_data['media_type'] = 'card';
			$to_send_data['card_style'] = 2;
			$to_send_data['card_text1'] = "已和{$buyer_name}签到，账户即将收到款项";
			$to_send_data['card_title'] = '去评价买家';
			
			POCO::singleton('pai_information_push')->card_send_for_order($order_info['seller_user_id'], 'yueseller', $order_info['buyer_user_id'], 'yuebuyer', $send_data, $to_send_data, $order_info['order_sn']);
		
			//微信公众号模板消息
			$order_full_info = $order_obj->get_order_full_info($order_info['order_sn']);
			$template_data = array(
				'first' => "已成功签到，交易完成\r\n服务完成后请评价商家哦",
				'goods_name' => $order_full_info['goods_name'],
				'total_amount' => $order_full_info['total_amount'],
				'status_str' => $order_full_info['status_str'],
				'remark' => '点击评价商家',
			);
			$template_to_url = 'http://yp.yueus.com/mall/user/order/detail.php?order_sn=' . $order_full_info['order_sn'];
			POCO::singleton('pai_weixin_pub_class')->message_template_send_by_user_id($order_full_info['buyer_user_id'], 'G_PAI_WEIXIN_MALL_ORDER_STATUS', $template_data, $template_to_url);
		}
		
		/*********更新商品价格策略***********/
		$goods_id = intval($detail_list[0]['goods_id']);
		$price = $detail_list[0]['prices'];
		if( $goods_id>0 )
		{
			$data = array(
				'bill_finish_num' => 1,
				'prices' => $price,
			);
			POCO::singleton('pai_mall_goods_class')->update_goods_statistical($goods_id, $data);
		}
		
		/*************更新交易数据**************/
		$user_obj = POCO::singleton('pai_user_data_class');
		$user_obj->add_deal_times($buyer_user_id);
		$user_obj->add_consume_ammount($buyer_user_id, $order_info['pending_amount']);
		
		return true;
	}

	/**
	 * 买家评价_订单_后
	 * @param array $params array('order_sn'=>'', 'is_anonymous'=>0)
	 * @return boolean
	 */
	public function comment_order_for_buyer_after_v1($params)
	{
		//获取订单信息
		$order_obj = POCO::singleton('pai_mall_order_class');
		$order_info = $order_obj->get_order_info($params['order_sn']);
		if( empty($order_info) )
		{
			return false;
		}
		
		//非匿名、非OA单，才发通知
		if( $params['is_anonymous']==0 && !in_array($order_info['referer'], array('oa')) )
		{
			$seller_name = get_user_nickname_by_user_id($order_info['seller_user_id']);
			
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
			$to_send_data['card_text1'] = "评价了{$seller_name}";
			$to_send_data['card_title'] = '查看订单详情';
			
			POCO::singleton('pai_information_push')->card_send_for_order($order_info['buyer_user_id'], 'yuebuyer', $order_info['seller_user_id'], 'yueseller', $send_data, $to_send_data, $order_info['order_sn']);
		
			//微信公众号模板消息
			$order_full_info = $order_obj->get_order_full_info($order_info['order_sn']);
			$template_data = array(
				'first' => "你已评价了{$seller_name}",
				'goods_name' => $order_full_info['goods_name'],
				'total_amount' => $order_full_info['total_amount'],
				'status_str' => $order_full_info['status_str'],
				'remark' => '点击查看详情',
			);
			$template_to_url = 'http://yp.yueus.com/mall/user/order/detail.php?order_sn=' . $order_full_info['order_sn'];
			POCO::singleton('pai_weixin_pub_class')->message_template_send_by_user_id($order_full_info['buyer_user_id'], 'G_PAI_WEIXIN_MALL_ORDER_STATUS', $template_data, $template_to_url);
		}
		
		return true;
	}

	/**
	 * 卖家评价_订单_后
	 * @param array $params array('order_sn'=>'','is_anonymous'=>0)
	 * @return boolean
	 */
	public function comment_order_for_seller_after_v1($params)
	{
		//获取订单信息
		$order_obj = POCO::singleton('pai_mall_order_class');
		$order_info = $order_obj->get_order_info($params['order_sn']);
		if( empty($order_info) )
		{
			return false;
		}
		
		//非匿名、非OA单，才发通知
		if( $params['is_anonymous']==0 && !in_array($order_info['referer'], array('oa')) )
		{
			$buyer_name = get_user_nickname_by_user_id($order_info['buyer_user_id']);
			
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
			$to_send_data['card_text1'] = "评价了{$buyer_name}";
			$to_send_data['card_title'] = '查看订单详情';
			
			POCO::singleton('pai_information_push')->card_send_for_order($order_info['seller_user_id'], 'yueseller', $order_info['buyer_user_id'], 'yuebuyer', $send_data, $to_send_data, $order_info['order_sn']);
		
			//微信公众号模板消息
			$order_full_info = $order_obj->get_order_full_info($order_info['order_sn']);
			$seller_name = get_user_nickname_by_user_id($order_full_info['seller_user_id']);
			$template_data = array(
				'first' => "{$seller_name}评价了你",
				'goods_name' => $order_full_info['goods_name'],
				'total_amount' => $order_full_info['total_amount'],
				'status_str' => $order_full_info['status_str'],
				'remark' => '点击查看详情',
			);
			$template_to_url = 'http://yp.yueus.com/mall/user/order/detail.php?order_sn=' . $order_full_info['order_sn'];
			POCO::singleton('pai_weixin_pub_class')->message_template_send_by_user_id($order_full_info['buyer_user_id'], 'G_PAI_WEIXIN_MALL_ORDER_STATUS', $template_data, $template_to_url);
		}
		
		return true;
	}

}
