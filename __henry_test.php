<?php
/**
 * 测试
 */

ignore_user_abort(true);
set_time_limit(3600);
ini_set('memory_limit', '512M');

require_once('poco_app_common.inc.php');
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$op = trim($_INPUT['op']);
if( $op=='sms' )
{
	$pai_sms_obj = POCO::singleton('pai_sms_class');
	
	/*
	$sms_data = array(
		'datetime' => date('H:i:s', time()),
	);
	$ret = $pai_sms_obj->send_sms('18588759753', 'G_PAI_MALL_SELLER_CHAT', $sms_data);
	var_dump($ret);
	*/
	
	/*
	//发送验证码
	$phone = '18588759753';
	$group_key = 'G_PAI_USER_PASSWORD_VERIFY';
	$ret = $pai_sms_obj->send_verify_code($phone, $group_key, array());
	var_dump($ret);
	
	//检查验证码
	$phone = '18588759753';
	$group_key = 'G_PAI_USER_PASSWORD_VERIFY';
	$verify_code = '193182';
	$ret = $pai_sms_obj->check_verify_code($phone, $group_key, $verify_code);
	var_dump($ret);
	*/
	
}
elseif( $op=='wx' )
{
 	$weixin_pub_obj = POCO::singleton('pai_weixin_pub_class');
 	$weixin_helper_obj = POCO::singleton('pai_weixin_helper_class');
 	 	
 	/*
 	//$sql = "SELECT id,bind_id,open_id FROM `pai_weixin_db`.`weixin_user_tbl` WHERE bind_id=1 AND is_subscribe=1 ORDER BY bind_id,open_id,id";
	//$list = db_simple_getdata($sql, false, 101);
 	
	//日志 http://yp.yueus.com/logs/201502/03_task.txt
	pai_log_class::add_log(array(), 'begin', 'wx_tmp');
	
	$i = 0;
	foreach($list as $info)
	{
		$i++;
		
	 	$bind_id = intval($info['bind_id']);
	 	$to_user = trim($info['open_id']);//oNHJqs6uUrgQQayhUT13bSZDW6Lo oNHJqs99qcw0n158HUKzsc89R5u0;
	 	$template_id = 'Em5smvXurTpRkZw4BIHEhku5A0hNGhtkv_1jJvyeiQ4';
	 	$template_data = array(
			'first' => array('value'=>'约约新版已发布！快来先睹为快吧~', 'color'=>''),
			'keyword1' => array('value'=>'小约', 'color'=>''),
			'keyword2' => array('value'=>'亲爱的约约er，约约服务号已经升级至最新版。之前暂停的交易功能已经全面开通了。新增了优惠券的使用功能，你可以在菜单栏体验新版服务，立刻约起来吧。', 'color'=>''),
			'remark' => array('value'=>'有任何问题，可以点击“调戏客服”与小约互动~', 'color'=>''),
		);
	 	$to_url = '';
	 	$ret = $weixin_helper_obj->wx_message_template_send($bind_id, $to_user, $template_id, $template_data, $to_url, '');
	 	var_dump($ret);
	 	
	 	pai_log_class::add_log(array('to_user'=>$to_user, 'ret'=>$ret), 'middle', 'wx_tmp');
	 	
	 	if( $i%100==0 )
	 	{
	 		sleep(1);
	 	}
	}
	pai_log_class::add_log(array(), 'finish', 'wx_tmp');
 	*/
 	
 	//分享后
//  	$template_code = 'G_PAI_WEIXIN_WAIPAI_SHARE_SUCCESS';
//  	$data = array('amount'=>'xx元优惠礼包', 'coupon_sn'=>'---', 'end_date'=>'4月30日');
//  	$to_url = '';
//  	$ret = $weixin_pub_obj->message_template_send_by_user_id($user_id, $template_code, $data, $to_url);
//  	var_dump($ret);
 	
}
elseif( $op=='pay' )
{
	define('YUE_OA_IMPORT_ORDER', 1);
	define('G_PAI_ECPAY_DEV', 1);
	
	//$pai_payment_obj = POCO::singleton('pai_payment_class');
	
}
elseif($op=='coupon')
{
	$coupon_obj = POCO::singleton('pai_coupon_class');
	$coupon_give_obj = POCO::singleton('pai_coupon_give_class');
	$coupon_package_obj = POCO::singleton('pai_coupon_package_class');
	
	/*
	$str = '';
	$arr = explode(',', $str);
	foreach($arr as $user_id)
	{
		$cellphone = '';
		//$user_id = '';
		$ref_id = 0;
		$ret = $coupon_give_obj->submit_queue('Y2015M10D01_CONSUMPTION_BACK_200', $cellphone, $user_id, $ref_id);
		var_dump($ret);
	}
	*/
	
	/*
	$user_id = 100003;
	$ret = $coupon_obj->give_coupon_by_create($user_id, 272);
	var_dump($ret);
	*/
	
	/*
	$package_sn_str = 'yueusgp21,yueusgp22';
	$package_sn_arr = explode(',', $package_sn_str);
	foreach ($package_sn_arr as $package_sn)
	{
		//$package_sn = 'GXRMB1800';
		$cate_id = 5;
		$start_time = strtotime('2015-09-29 00:00:00');
		$end_time = strtotime('2015-10-31 23:59:59');
		$plan_number = 999999999;
		$batch_list = array(
			array('batch_id'=>265, 'quantity'=>1, 'coupon_days'=>0),
		);
		$more_info = array('scope_user_divide'=>'new', 'package_title'=>'兑换码', 'package_remark'=>'2015年9月 摄影商家地推（9.29-10.31）');
		$ret = $coupon_package_obj->submit_package($package_sn, $cate_id, $start_time, $end_time, $plan_number, $batch_list, $more_info);
		var_dump($ret);
	}
	*/
	
	/*
	$coupon_give_obj = POCO::singleton('pai_coupon_give_class');
	$rst = $coupon_give_obj->submit_queue('Y2015M09D30_RECHARGE_300', '', 100003, 0);
	var_dump($rst);
	*/
	
}
elseif( $op=='mall' )
{
	//$mall_order_obj = POCO::singleton('pai_mall_order_class');
	
	/*
	//运营部，1元抽奖活动，限制每人只能购买一个
	if( $goods_id==2131785 && time()<=strtotime("2015-12-31 23:59:59") )
	{
		$disposable_sql = "SELECT SUM(d.quantity) AS quantity FROM `mall_db`.`mall_order_detail_tbl` AS d LEFT JOIN `mall_db`.`mall_order_tbl` AS o ON d.order_id=o.order_id WHERE d.goods_id=2131785 AND o.buyer_user_id={$buyer_user_id} AND o.is_pay=1 AND o.status<>7";
		$disposable_info = db_simple_getdata($disposable_sql, true, 101);
		$disposable_quantity = intval($disposable_info['quantity']);
		if( $disposable_quantity>0 )
		{
			echo "ERR";
		}
	}
	*/
	
	//获取商品信息
	//$goods_id = 2125398;
	//$mall_goods_obj = POCO::singleton('pai_mall_goods_class');
	//$goods_info = $mall_goods_obj->get_goods_info($goods_id);
	//var_dump($goods_info);
	
}
elseif( $op=='event_enroll')
{
	define('G_YUEUS_WAIPAI_IMPORT_ORDER', 1);
	
	/**
	 * 取消活动
	 * @param int $event_id
	 * @return boolean
	 */
	function henry_cancel_event($event_id)
	{
		$event_id = intval($event_id);
		if( $event_id<1 )
		{
			return false;
		}
		
		//关闭活动
		$event_details_obj = POCO::singleton('event_details_class');
		$event_details_obj->set_event_status_by_cancel($event_id);
		
		//关闭所有交易
		$payment_obj = POCO::singleton('pai_payment_class');
		$cancel_rst = $payment_obj->cancel_event_v2('waipai', $event_id);
		if( $cancel_rst['error']!=0 )
		{
			return false;
		}
		
		return true;
	}
	
	/**
	 * 取消报名
	 * @param int $enroll_id
	 * @return boolean
	 */
	function henry_cancel_enroll($enroll_id)
	{
		$enroll_id = intval($enroll_id);
		if( $enroll_id<1 )
		{
			return false;
		}
		
		//关闭报名
		$enroll_obj = POCO::singleton('event_enroll_class');
		$enroll_obj->update_enroll(array('event_remark'=>'活动取消'), $enroll_id);
		
		$coupon_obj = POCO::singleton('pai_coupon_class');
		$coupon_obj->refund_coupon_by_oid("waipai", $enroll_id);
		
		//关闭交易
		$payment_obj = POCO::singleton('pai_payment_class');
		return $payment_obj->closed_trade_v2('waipai', $enroll_id);
	}
	
	$coupon_obj = POCO::singleton('pai_coupon_class');
	$relate_org_obj = POCO::singleton('pai_model_relate_org_class');
	$order_obj = POCO::singleton('pai_mall_order_class');
	$payment_obj = POCO::singleton('pai_payment_class');
	$repay_obj = POCO::singleton('ecpay_pai_repay_class');
	
	//进行中的活动列表 
	$sql = "SELECT * FROM `event_db`.`event_details_tbl` WHERE type_icon IN ('photo', 'yuepai') AND event_status='0' AND new_version=2 AND event_id=60832 ORDER BY event_id ASC";
	$event_list = db_simple_getdata($sql, false, 23);
	echo "========== start " . count($event_list) . "============= <br />\r\n";
	foreach( $event_list as $event_info )
	{
		$event_id = intval($event_info['event_id']);
		$seller_poco_id = intval($event_info['user_id']);
		$seller_user_id = get_relate_yue_id($seller_poco_id);
		$org_user_id = intval($event_info['org_user_id']);
		$address_tmp = trim($event_info['address']);
		
		//冻结中的交易列表
		$sql = "SELECT * FROM `ecpay_db`.`pai_trade_tbl` WHERE channel_module='waipai' AND trade_type='out' AND status=1 AND event_id={$event_id} ORDER BY `trade_id`";
		$trade_list = db_simple_getdata($sql, false, 101);
		
		//已支付的报名列表
		$sql = "SELECT * FROM `event_db`.`event_enroll_tbl` WHERE event_id={$event_id} AND pay_status=1 AND event_remark<>'活动取消' ORDER BY `table_id` ASC,`status` ASC";
		$enroll_list = db_simple_getdata($sql, false, 23);
		echo "<br />\r\n------------ {$event_id} enroll " . count($enroll_list) . "------------ <br />\r\n";
		if( count($enroll_list)!=count($trade_list) )
		{
			echo "count {$event_id} <br />\r\n";
			exit();
		}
		
		foreach( $enroll_list as $enroll_info )
		{
			$enroll_id_tmp = intval($enroll_info['enroll_id']);
			$buyer_poco_id_tmp = intval($enroll_info['user_id']);
			$buyer_user_id_tmp = get_relate_yue_id($buyer_poco_id_tmp);
			$enroll_num_tmp = intval($enroll_info['enroll_num']);
			$table_id_tmp = intval($enroll_info['table_id']);
			$phone_tmp = trim($enroll_info['phone']);
			$source_tmp = trim($enroll_info['source']);
			$event_remark_tmp = trim($enroll_info['event_remark']);
			
			//签到码列表，一个报名多个签到码，取第一个码
			$sql = "SELECT * FROM `pai_db`.`pai_activity_code_tbl` WHERE event_id={$event_id} AND enroll_id={$enroll_id_tmp} ORDER BY `id` DESC";
			$code_list = db_simple_getdata($sql, false, 101);
			if( empty($code_list) )
			{
				echo "code_list {$event_id}_{$enroll_id_tmp}_{$table_id_tmp} <br />\r\n";
				exit();
			}
			$code_sn_tmp = trim($code_list[0]['code']);
			$is_checked_tmp = trim($code_list[0]['is_checked']);
			foreach( $code_list as $code_info )
			{
				if( $is_checked_tmp!=$code_info['is_checked'] )
				{
					echo "is_checked? {$event_id}_{$enroll_id_tmp}_{$table_id_tmp} <br />\r\n";
					exit();
				}
			}
			
			//机构用户ID
			$org_info_tmp = $relate_org_obj->get_org_info_by_user_id($seller_user_id);
			$org_user_id_tmp = intval($org_info_tmp['org_id']);
			if( $org_user_id_tmp!=$org_user_id )
			{
				echo "org_user_id {$event_id}_{$enroll_id_tmp}_{$table_id_tmp} <br />\r\n";
				exit();
			}
			
			//检查48小时自动签到时间
			$sql = "SELECT * FROM `event_db`.`event_table_tbl` WHERE id={$table_id_tmp} ORDER BY `id`";
			$table_info_tmp = db_simple_getdata($sql, true, 23);
			$begin_time_tmp = $table_info_tmp['begin_time']*1;
			$end_time_tmp = $table_info_tmp['end_time']*1;
			if( $end_time_tmp+24*3600<time() )
			{
				echo "end_time {$event_id}_{$enroll_id_tmp}_{$table_id_tmp} <br />\r\n";
				exit();
			}
			
			$sql = "SELECT * FROM `test`.`test_event_info_stage_tbl` WHERE event_id={$event_id} AND table_id={$table_id_tmp} ORDER BY `id`";
			$test_event_info = db_simple_getdata($sql, true, 101);
			if( empty($test_event_info) )
			{
				echo "goods_id {$event_id}_{$enroll_id_tmp}_{$table_id_tmp} <br />\r\n";
				exit();
			}
			$goods_id_tmp = intval($test_event_info['goods_id']);
			$stage_id_tmp = trim($test_event_info['stage_id']);
			$price_id_tmp = trim($test_event_info['price_id']);
			
			//查询优惠券
			$coupon_sn_tmp = '';
			if( $enroll_info['is_use_coupon']==1 )
			{
				$ref_order_list = $coupon_obj->get_ref_order_list(false, "channel_module='waipai' AND channel_oid={$enroll_id_tmp} AND is_refund=0", 'id DESC', '0,1');
				$coupon_sn_tmp = trim($ref_order_list[0]['coupon_sn']);
				$coupon_info = $coupon_obj->get_coupon_info($coupon_sn_tmp);
				if( $coupon_info['end_time']<=(time()+10) )
				{
					echo "coupon_end_time {$event_id}_{$enroll_id_tmp}_{$table_id_tmp} {$coupon_sn_tmp} <br />\r\n";
					exit();
				}
			}
			
			//下订单
			$extend_list = array( array(
			   'activity_id' => $goods_id_tmp,
			   'stage_id' => $stage_id_tmp,
			   'prices_type_id' => $price_id_tmp,
			   'service_cellphone' => $phone_tmp,
			   'service_address' => $address_tmp,
			   'quantity' => $enroll_num_tmp,
			   'activity_promotion_id' => 0,
			), );
			$more_info = array(
				'referer' => 'import',
				'is_auto_accept' => 0,
				'is_auto_sign' => ($is_checked_tmp==1)?1:0,
			 	'description' => "外拍ID {$enroll_id_tmp}",
			);
			$submit_rst = $order_obj->submit_order_activity($buyer_user_id_tmp, $extend_list, $more_info);
			if( $submit_rst['result']!=1 )
			{
				echo "submit_order_activity {$event_id}_{$enroll_id_tmp}_{$table_id_tmp} {$submit_rst['message']} {$goods_id_tmp} <br />\r\n";
				exit();
			}
			$order_id_tmp = intval($submit_rst['order_id']);
			$order_sn_tmp = trim($submit_rst['order_sn']);
			
			//取消报名
			$rst = henry_cancel_enroll($enroll_id_tmp);
			if( !$rst )
			{
				echo "henry_cancel_enroll {$event_id}_{$enroll_id_tmp}_{$table_id_tmp} {$order_id_tmp} <br />\r\n";
				exit();
			}
			
			//支付方式
			$sql = "SELECT r.recharge_id,r.third_code FROM `ecpay_db`.`pai_recharge_tbl` AS r LEFT JOIN `ecpay_db`.`pai_trade_tbl` AS t ON r.recharge_id=t.recharge_id WHERE t.apply_id={$enroll_id_tmp} AND t.event_id={$event_id} AND t.trade_type='out' AND r.status=1";
			$recharge_info_tmp = db_simple_getdata($sql, true, 101);
			$recharge_id_tmp = intval($recharge_info_tmp['recharge_id']);
			$recharge_third_code_tmp = trim($recharge_info_tmp['third_code']);
			if( !empty($recharge_info_tmp) && $recharge_third_code_tmp=='tenpay_wxpub' )
			{
				$sql = "SELECT * FROM `ecpay_db`.`pai_repay_tbl` WHERE repay_type='trade' AND status=0 AND user_id={$buyer_user_id_tmp} AND recharge_id={$recharge_id_tmp} ORDER BY `repay_id` DESC";
				$repay_info_tmp = db_simple_getdata($sql, true, 101);
				if( !empty($repay_info_tmp) )
				{
					echo "tenpay_wxpub {$event_id}_{$enroll_id_tmp}_{$table_id_tmp} <br />\r\n";
					exit();
				}
				
				//取消退款
				$repay_rst = $repay_obj->cancel_repay($repay_info_tmp['repay_id']);
				if( !$repay_rst )
				{
					echo "cancel_repay {$event_id}_{$enroll_id_tmp}_{$table_id_tmp} <br />\r\n";
					exit();
				}
			}
			
			//支付新订单
			$pay_rst = $order_obj->submit_pay_order($order_sn_tmp, $buyer_user_id_tmp, 0, 1, '', '', '', $coupon_sn_tmp);
			if( $pay_rst['result']!=1 && $pay_rst['result']!=2 )
			{
				echo "submit_pay_order {$event_id}_{$enroll_id_tmp}_{$table_id_tmp} {$pay_rst['message']} <br />\r\n";
				exit();
			}
			
			//更新二维码
			$sql = "UPDATE `mall_db`.`mall_order_code_tbl` SET code_sn='{$code_sn_tmp}' WHERE order_id={$order_id_tmp}";
			db_simple_getdata($sql, true, 101);
			
			//echo "{$seller_user_id}_{$org_user_id_tmp} {$event_id}_{$enroll_id_tmp}_{$table_id_tmp} {$buyer_user_id_tmp} {$enroll_num_tmp} {$code_sn_tmp}|{$is_checked_tmp} {$source_tmp} {$recharge_third_code_tmp} {$coupon_sn_tmp}\r\n";
			echo "success {$event_id}_{$enroll_id_tmp}_{$table_id_tmp} {$order_id_tmp} <br />\r\n";
		}
		
		//取消活动
		$rst = henry_cancel_event($event_id);
		if( !$rst )
		{
			echo "henry_cancel_event {$event_id}_{$enroll_id_tmp}_{$table_id_tmp} <br />\r\n";
			exit();
		}
	}
}
elseif( $op=='input' )
{
	echo "<pre>\r\n";
	print_r($_REQUEST);
	print_r($_INPUT);
	echo "\r\n</pre>";
}
else
{
	die('op error');
}

