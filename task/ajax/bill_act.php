<?php
/**
 * 账单
 * @author Henry
 * @copyright 2014-09-13
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

if(empty($yue_login_id))
{
	die('no login');
}

$type = trim($_INPUT['type']);

$page = intval($_INPUT['page']);
if( $page<1 ) $page = 1;

$page_size = 1000; 

$output_arr = array();
$list = array();

if( $type=='trade' )	//交易记录
{
	$search_arr = array(
		'user_id' => $yue_login_id,
	);
	
	$offset = ($page-1)*$page_size;
	$limit = $offset . ',' . ($page_size + 1);
	
	$payment_obj = POCO::singleton('pai_payment_class');
	$trade_list = $payment_obj->get_trade_list_by_search($search_arr, $b_select_count = false, $limit);
	foreach ($trade_list as $info)
	{
		$subject = trim($info['subject']);
		$trade_type = trim($info['trade_type']);
		if( $trade_type=='in' )
		{
			if( strlen($subject)>0 ) $subject = '-' . $subject;
			$subject = '收入' . $subject;
		}
		elseif( $trade_type=='out' )
		{
			if( strlen($subject)>0 ) $subject = '-' . $subject;
			$subject = '支出' . $subject;
		}
		elseif( $trade_type=='refund' )
		{
			if( strlen($subject)>0 ) $subject = '-' . $subject;
			$subject = '退款' . $subject;
		}
		elseif( $trade_type=='transfer' )
		{
			if( strlen($subject)>0 ) $subject = '-' . $subject;
			$subject = '转账' . $subject;
		}
		
		$flow_type_tmp = trim($info['flow_type']);
		$flow_type_tmp = ($flow_type_tmp=='in') ? 0 : 1;
		
		$status_color = 0; //1绿色 2灰色
		$status_tmp = intval($info['status']);
		if( $status_tmp==0 )
		{
			$status_str = '等待支付';
			$status_color = 2;
		}
		elseif( $status_tmp==7 )
		{
			$status_str = '交易关闭';
			$status_color = 2;
		}
		elseif( $status_tmp==8 )
		{
			$status_str = '交易成功';
			$status_color = 2;
		}
		else 
		{
			$status_str = '处理中';
			$status_color = 1;
		}
		
		$list[] = array(
			'subject' => $subject,
			'flow_type' => $flow_type_tmp,
			'amount' => trim($info['total_amount']),
			'status' => $status_str,
			'status_color' => $status_color,
			'add_date' => date('Y-m-d', $info['add_time']),
		);
	}
	
	//是否有下一页
	$has_next = 0;
	if( count($list)>$page_size )
	{
		array_pop($list);
		$has_next = 1;
	}
	
	$output_arr['code'] = 1;
	$output_arr['msg'] = '';
	$output_arr['data']  = $list;
	$output_arr['has_next']  = $has_next;
	mobile_output($output_arr, false);
	exit();
}
elseif( $type=='recharge' )	//充值记录
{
	$search_arr = array(
		'user_id' => $yue_login_id,
		'recharge_type' => array('recharge','bail','consume'),
		'status' => 1,
	);
	
	$offset = ($page-1)*$page_size;
	$limit = $offset . ',' . ($page_size + 1);
	
	$payment_obj = POCO::singleton('pai_payment_class');
	$recharge_list = $payment_obj->get_recharge_list_by_search($search_arr, $b_select_count = false, $limit);
	foreach ($recharge_list as $info)
	{
		$subject = trim($info['subject']);
		if( strlen($subject)>0 ) $subject = '-' . $subject;
		$subject = '充值' . $subject;
		
		$status_color = 0; //1绿色 2灰色
		$status_tmp = intval($info['status']);
		if( $status_tmp==0 )
		{
			$status_str = '等待充值';
			$status_color = 2;
		}
		elseif( $status_tmp==1 )
		{
			$status_str = '充值成功';
			$status_color = 2;
		}
		elseif( $status_tmp==2 )
		{
			$status_str = '充值失败';
			$status_color = 2;
		}
		else 
		{
			$status_str = '处理中';
			$status_color = 1;
		}
		
		$list[] = array(
			'subject' => $subject,
			'flow_type' => 0,
			'amount' => trim($info['amount']),
			'status' => $status_str,
			'status_color' => $status_color,
			'add_date' => date('Y-m-d', $info['add_time']),
		);
	}
	
	//是否有下一页
	$has_next = 0;
	if( count($list)>$page_size )
	{
		array_pop($list);
		$has_next = 1;
	}
	
	$output_arr['code'] = 1;
	$output_arr['msg'] = '';
	$output_arr['data']  = $list;
	$output_arr['has_next']  = $has_next;
	mobile_output($output_arr, false);
	exit();
}
elseif( $type=='withdraw' )	//提现记录
{
	$search_arr = array(
		'user_id' 		=> $yue_login_id,
		'withdraw_type' => array('withdraw', 'bail')
	);
	
	$offset = ($page-1)*$page_size;
	$limit = $offset . ',' . ($page_size + 1);
	
	$payment_obj = POCO::singleton('pai_payment_class');
	$withdraw_list = $payment_obj->get_withdraw_list_by_search($search_arr, $b_select_count = false, $limit);
	foreach ($withdraw_list as $info)
	{
		$subject = trim($info['subject']);
		if( strlen($subject)>0 ) $subject = '-' . $subject;
		$subject = '提现' . $subject;
		
		$status_color = 0; //1绿色 2灰色
		$status_tmp = intval($info['status']);
		if( $status_tmp==0 )
		{
			$status_str = '处理中';
			$status_color = 1;
		}
		elseif( $status_tmp==1 )
		{
			$status_str = '提现成功';
			$status_color = 2;
		}
		elseif( $status_tmp==2 )
		{
			$status_str = '提现失败';
			$status_color = 2;
		}
		else 
		{
			$status_str = '处理中';
			$status_color = 1;
		}

		$list[] = array(
			'subject' => $subject,
			'flow_type' => 1,
			'amount' => trim($info['amount']),
			'status' => $status_str,
			'status_color' => $status_color,
			'add_date' => date('Y-m-d', $info['add_time']),
		);
	}
	
	//是否有下一页
	$has_next = 0;
	if( count($list)>$page_size )
	{
		array_pop($list);
		$has_next = 1;
	}
	
	$output_arr['code'] = 1;
	$output_arr['msg'] = '';
	$output_arr['data']  = $list;
	$output_arr['has_next']  = $has_next;
	mobile_output($output_arr, false);
	exit();
}
elseif( $type=='transfer' )	//转账记录
{
	$search_arr = array(
		'user_id' => $yue_login_id,
	);
	
	$offset = ($page-1)*$page_size;
	$limit = $offset . ',' . ($page_size + 1);
	
	$payment_obj = POCO::singleton('pai_payment_class');
	$transfer_list = $payment_obj->get_transfer_list_by_search($search_arr, $b_select_count = false, $limit);
	foreach ($transfer_list as $info)
	{
		$subject = trim($info['subject']);
		if( strlen($subject)>0 ) $subject = '-' . $subject;
		$subject = '转账' . $subject;
		
		$flow_type_tmp = trim($info['flow_type']);
		$flow_type_tmp = ($flow_type_tmp=='in') ? 0 : 1;
		
		$status_color = 0; //1绿色 2灰色
		$status_tmp = intval($info['status']);
		if( $status_tmp==0 )
		{
			$status_str = '等待支付';
			$status_color = 2;
		}
		elseif( $status_tmp==7 )
		{
			$status_str = '转账失败';
			$status_color = 2;
		}
		elseif( $status_tmp==8 )
		{
			$status_str = '转账成功';
			$status_color = 2;
		}
		else
		{
			$status_str = '处理中';
			$status_color = 1;
		}

		$list[] = array(
			'subject' => $subject,
			'flow_type' => $flow_type_tmp,
			'amount' => trim($info['amount']),
			'status' => $status_str,
			'status_color' => $status_color,
			'add_date' => date('Y-m-d', $info['add_time']),
		);
	}
	
	//是否有下一页
	$has_next = 0;
	if( count($list)>$page_size )
	{
		array_pop($list);
		$has_next = 1;
	}
	
	$output_arr['code'] = 1;
	$output_arr['msg'] = '';
	$output_arr['data']  = $list;
	$output_arr['has_next']  = $has_next;
	mobile_output($output_arr, false);
	exit();
}
elseif( $type=='repay' )	//退回记录
{
	$search_arr = array(
		'user_id' => $yue_login_id,
	);
	
	$offset = ($page-1)*$page_size;
	$limit = $offset . ',' . ($page_size + 1);
	
	$payment_obj = POCO::singleton('pai_payment_class');
	$repay_list = $payment_obj->get_repay_list_by_search($search_arr, $b_select_count = false, $limit);
	foreach ($repay_list as $info)
	{
		$subject = trim($info['subject']);
		if( strlen($subject)>0 ) $subject = '-' . $subject;
		$subject = '退款' . $subject;
		
		$status_color = 0; //1绿色 2灰色
		$status_tmp = intval($info['status']);
		if( $status_tmp==0 )
		{
			$status_str = '处理中';
			$status_color = 1;
		}
		elseif( $status_tmp==1 )
		{
			$status_str = '退回成功';
			$status_color = 2;
		}
		elseif( $status_tmp==2 )
		{
			$status_str = '退回失败';
			$status_color = 2;
		}
		else
		{
			$status_str = '处理中';
			$status_color = 1;
		}

		$list[] = array(
			'subject' => $subject,
			'flow_type' => 1,
			'amount' => trim($info['amount']),
			'status' => $status_str,
			'status_color' => $status_color,
			'add_date' => date('Y-m-d', $info['add_time']),
		);
	}
	
	//是否有下一页
	$has_next = 0;
	if( count($list)>$page_size )
	{
		array_pop($list);
		$has_next = 1;
	}
	
	$output_arr['code'] = 1;
	$output_arr['msg'] = '';
	$output_arr['data']  = $list;
	$output_arr['has_next']  = $has_next;
	mobile_output($output_arr, false);
	exit();
}
else
{
	$output_arr['code'] = 0;
	$output_arr['msg'] = 'type error';
	$output_arr['data']  = $list;
	mobile_output($output_arr, false);
	exit();
}
