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
 * Define 提交评论
 */
define('G_SIMPLE_INPUT_CLEAN_VALUE',1);
include_once('../common.inc.php');	

$order_sn = trim($_INPUT[order_sn]);
$table_id = trim($_INPUT[table_id]);

// 消费者评价商家
if(!empty($order_sn))
{
	// 订单类：pai_mall_order_class
	$mall_order_obj = POCO::singleton('pai_mall_order_class');

	// 评价类
	$mall_comment_obj = POCO::singleton('pai_mall_comment_class');

	// 查询订单详情

	/**
	* 获取完整信息
	* @param string $order_sn
	* @return array
	*/
	$order_full_info = $mall_order_obj->get_order_full_info($order_sn);
    

	$insert_data['from_user_id'] = $yue_login_id;  //评价人用户ID
	$insert_data['to_user_id'] =  $order_full_info['seller_user_id']; //被评价人用户ID
	$insert_data['order_id'] = $order_full_info['order_id'];
	
	$insert_data['overall_score'] = intval($_INPUT['overall_score']); //总体评价分数
	$insert_data['match_score'] = intval($_INPUT['match_score']); //商品符合分数
	$insert_data['manner_score'] = intval($_INPUT['manner_score']); //态度评价
	$insert_data['quality_score'] = intval($_INPUT['quality_score']);  //质量分数
	$insert_data['comment'] = iconv("UTF-8", "gbk//TRANSLIT", trim($_INPUT['comment']));  //评价内容
	$insert_data['is_anonymous'] = intval($_INPUT['is_anonymous']);  //匿名 1为true

	// 用于活动评价
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
		 * 消费者评商家
		 */
		$ret = $mall_comment_obj->add_seller_comment($insert_data);
		
	}
	// 用于商城评价
	elseif($order_full_info['order_type'] == 'detail')
	{
		$insert_data['goods_id'] = $order_full_info['detail_list'][0]['goods_id'];

		/*
		 * 消费者评商家
		 */
		$ret = $mall_comment_obj->add_seller_comment($insert_data);
	}
	// 用于面付
	elseif($order_full_info['order_type'] == 'payment')
	{
		if($yue_login_id == $order_full_info['seller_user_id'])
		{
			$insert_data['from_user_id'] = $yue_login_id;  //评价人用户ID
			$insert_data['to_user_id'] =  $order_full_info['buyer_user_id']; //被评价人用户ID
			/*
			 * 商家评价消费者，用于面付
			 */
			$ret = $mall_comment_obj->add_buyer_comment($insert_data);
		} 
		else
		{
			/*
			 * 消费者评商家
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
// 评价活动
else if(!empty($table_id))
{
	
	$overall_score = $_INPUT['overall_score'];
	$organize_score = $_INPUT['match_score'];
	$model_score = $_INPUT['quality_score'];
	$comment = iconv("UTF-8", "gbk//TRANSLIT", $_INPUT['comment']);
	$is_anonymous = $_INPUT['is_anonymous'];

	$event_table_obj = POCO::singleton('event_table_class');
	$event_comment_obj = POCO::singleton('pai_event_comment_log_class');
	
	$event_info = $event_table_obj->get_table_info($table_id);
	$event_id = $event_info['event_id'];

	/*
	 * 添加评价
	 * 
	 * @param int    $event_id    活动ID
	 * @param int    $user_id 用户ID
	 * @param enum   $overall_score     分数  1-5
	 * @param enum   $organize_score    分数  1-5
	 * @param enum   $model_score       分数  1-5
	 * @param string $comment     评价
	 * @param int    $is_anonymous     是否匿名评价
	 * 
	 * return int 
	 */


	if(empty($event_id))
	{
		die("参数错误");
	}

	$check_comment = $event_comment_obj->is_event_comment_by_user($event_id, $table_id,$yue_login_id);

	if($check_comment){
		$output_arr['code'] = 0;
		$output_arr['msg'] = '你已评价该活动了';

		mobile_output($output_arr,false);
		exit;
	}


	$ret = $event_comment_obj->add_comment($event_id, $table_id,$yue_login_id,$overall_score,$organize_score,$model_score, $comment,$is_anonymous);

	$output_arr['code'] = $ret ? 1 :0;
	$output_arr['msg'] = $ret ? '评价成功' : '评价失败';
	
	if($output_arr['code'] == 1)
	{
		$output_arr['data'] = array(
			'url'=>'../act/sign.php?event_id='.$event_id
		);
	}
	else
	{
		$output_arr['data'] = array();
	}
}










mall_mobile_output($output_arr,false);

?>