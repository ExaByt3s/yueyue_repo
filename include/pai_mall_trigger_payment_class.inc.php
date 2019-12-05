<?php
/**
 * 事件触发
 * 方法名规范：模块名称_事件_前/后
 * @author Henry
 * @copyright 2015-11-16
 */

class pai_mall_trigger_payment_class
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
	public function submit_order_after($params)
	{
		//获取订单信息
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
		
		/*********更新订单统计报表***********/
		$comment_info = '';
		POCO::singleton('pai_mall_order_log_class')->add_order_log('submit_order', $order_id, $buyer_user_id, $seller_user_id, $order_info, $comment_info);
		
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
		
		//非OA单，才发通知
		if( !in_array($order_info['referer'], array('oa')) )
		{
			//买家 => 商家
			$send_data = array();
			$send_data['media_type'] = 'card';
			$send_data['card_style'] = 1;
			$send_data['card_text1'] = '已向你直接付款';
			$send_data['card_text2'] = '￥' . $order_info['total_amount'];
			$send_data['card_title'] = '已到账';
			
			//商家 => 买家
			$to_send_data = array();
			$to_send_data['media_type'] = 'card';
			$to_send_data['card_style'] = 1;
			$to_send_data['card_text1'] = '已对该商家进行直接付款';
			$to_send_data['card_text2'] = '￥' . $order_info['total_amount'];
			$to_send_data['card_title'] = '支付成功';
			
			POCO::singleton('pai_information_push')->card_send_for_order($buyer_user_id, 'yuebuyer', $seller_user_id, 'yueseller', $send_data, $to_send_data, $order_sn, $order_type);
		}
		
		/*********更新订单统计报表***********/
		$comment_info = '';
		POCO::singleton('pai_mall_order_log_class')->add_order_log('pay_order', $order_id, $buyer_user_id, $seller_user_id, $order_info, $comment_info);
		
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
		
		/*********更新订单统计报表***********/
		//获取消费者对商家订单的商品评价（这里需注意下，和订单评论逻辑不同）
		$comment_info = POCO::singleton('pai_mall_comment_class')->get_seller_comment_info($order_id, 0);
		POCO::singleton('pai_mall_order_log_class')->add_order_log('comment_order_for_buyer', $order_id, $buyer_user_id, $seller_user_id, $order_info, $comment_info);
		
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
		
		/*********更新订单统计报表***********/
		//获取商家对消费者订单的商品评价
		$comment_info = POCO::singleton('pai_mall_comment_class')->get_buyer_comment_info($order_id, 0);
		POCO::singleton('pai_mall_order_log_class')->add_order_log('comment_order_for_seller', $order_id, $buyer_user_id, $seller_user_id, $order_info, $comment_info);
		
		return true;
	}
	
}
