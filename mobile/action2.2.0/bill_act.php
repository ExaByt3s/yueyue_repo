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

$page_size = 10;

$output_arr = array();
$list = array();

if( $type=='trade' )	//交易记录
{
	$offset = ($page-1)*$page_size;
	$limit = $offset . ',' . ($page_size + 1);
	
	$payment_obj = POCO::singleton('pai_payment_class');
	$bill_list = $payment_obj->get_bill_trade_list($yue_login_id, false, $limit);
	foreach($bill_list as $info)
	{
		$subject = trim($info['subject']);
		$is_invalid = intval($info['is_invalid']);
		$flow_type = intval($info['flow_type']);
		$amount = trim($info['amount']);
		$status_str = trim($info['status_str']);
		$remark = trim($info['remark']);
		$repay_str_arr = $info['repay_str_arr'];
		if( !is_array($repay_str_arr) ) $repay_str_arr = array();
		$add_date = trim($info['add_date']);
		
		$is_invalid_color = $is_invalid ? ' coloraaa' : '';
		$is_invalid_line = $is_invalid ? ' line-through' : '';
		$status_color = ($status_str=='处理中') ? ' color0086C5' : ' coloraaa';
		$flow_type_symbol = $flow_type ? '-' : '+';
		$flow_type_color = $flow_type ? '' : ' color3fc585';
		if( $is_invalid ) $flow_type_color = $is_invalid_color;
		
		//内容1
		$content1 = '';
		$content1 .= '<div class="f18 '.$is_invalid_color . $is_invalid_line.'">'.$subject.'</div>';
		if( strlen($status_str)>0 && strlen($remark)<1 && empty($repay_str_arr) )
		{
			$content1 .= '<div class="f10 '.$status_color.'">'.$status_str.'</div>';
		}
		if( strlen($remark)>0 )
		{
			$content1 .= '<div class="f10 colore06666">'.$remark.'</div>';
		}
		foreach($repay_str_arr as $repay_str)
		{
			$content1 .= '<div class="f10 colore06666">'.$repay_str.'</div>';
		}
		$content1 .= '<div class="f10 coloraaa">'.$add_date.'</div>';
		
		//内容2
		$content2 = '';
		$content2 .= '<div class="f16 '.$flow_type_color.'">'.$flow_type_symbol . $amount.'</div>';
		
		$list[] = array(
			'content1' => $content1,
			'content2' => $content2,
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
	$offset = ($page-1)*$page_size;
	$limit = $offset . ',' . ($page_size + 1);
	
	$payment_obj = POCO::singleton('pai_payment_class');
	$bill_list = $payment_obj->get_bill_recharge_list($yue_login_id, false, $limit);
	foreach($bill_list as $info)
	{
		$subject = trim($info['subject']);
		$is_invalid = intval($info['is_invalid']);
		$flow_type = intval($info['flow_type']);
		$amount = trim($info['amount']);
		$status_str = trim($info['status_str']);
		$remark = trim($info['remark']);
		$repay_str_arr = $info['repay_str_arr'];
		if( !is_array($repay_str_arr) ) $repay_str_arr = array();
		$add_date = trim($info['add_date']);
		
		$status_color = ($status_str=='处理中') ? ' color0086C5' : ' coloraaa';
		$flow_type_symbol = $flow_type ? '-' : '+';
		$flow_type_color = $flow_type ? '' : ' color3fc585';
		
		//内容1
		$content1 = '';
		$content1 .= '<div class="f18 ">'.$subject.'</div>';
		$content1 .= '<div class="f10 '.$status_color.'">'.$status_str.'</div>';
		$content1 .= '<div class="f10 coloraaa">'.$add_date.'</div>';
		
		//内容2
		$content2 = '';
		$content2 .= '<div class="f16 '.$flow_type_color.'">'.$flow_type_symbol . $amount.'</div>';
		
		$list[] = array(
			'content1' => $content1,
			'content2' => $content2,
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
	$offset = ($page-1)*$page_size;
	$limit = $offset . ',' . ($page_size + 1);
	
	$payment_obj = POCO::singleton('pai_payment_class');
	$bill_list = $payment_obj->get_bill_withdraw_list($yue_login_id, false, $limit);
	foreach($bill_list as $info)
	{
		$subject = trim($info['subject']);
		$is_invalid = intval($info['is_invalid']);
		$flow_type = intval($info['flow_type']);
		$amount = trim($info['amount']);
		$status_str = trim($info['status_str']);
		$remark = trim($info['remark']);
		$repay_str_arr = $info['repay_str_arr'];
		if( !is_array($repay_str_arr) ) $repay_str_arr = array();
		$add_date = trim($info['add_date']);
		
		$is_invalid_color = $is_invalid ? ' coloraaa' : '';
		$is_invalid_line = $is_invalid ? ' line-through' : '';
		$status_color = ($status_str=='处理中') ? ' color0086C5' : ' coloraaa';
		$flow_type_symbol = $flow_type ? '-' : '+';
		$flow_type_color = $flow_type ? '' : ' color3fc585';
		if( $is_invalid ) $flow_type_color = $is_invalid_color;
		
		//内容1
		$content1 = '';
		$content1 .= '<div class="f18 '.$is_invalid_color . $is_invalid_line.'">'.$subject.'</div>';
		$content1 .= '<div class="f10 '.$status_color.'">'.$status_str.'</div>';
		$content1 .= '<div class="f10 coloraaa">'.$add_date.'</div>';
		
		//内容2
		$content2 = '';
		$content2 .= '<div class="f16 '.$flow_type_color.'">'.$flow_type_symbol . $amount.'</div>';
		
		$list[] = array(
			'content1' => $content1,
			'content2' => $content2,
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
