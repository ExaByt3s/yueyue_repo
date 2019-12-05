<?php

include_once 'common.inc.php';
include_once 'top.php';
//地区引用
include_once ("/disk/data/htdocs232/poco/php_common/poco_location_fun.inc.php");

$tpl = new SmartTemplate ( "list.tpl.htm" );

$model_oa_order_obj = POCO::singleton ( 'pai_model_oa_order_class' );
$model_oa_enroll_obj = POCO::singleton ( 'pai_model_oa_enroll_class' );
$page_obj = new show_page ();

$show_count = 20;

$list_status = $_INPUT ['list_status'] ? $_INPUT ['list_status'] : 'wait';
$order_id = ( int ) $_INPUT ['order_id'];
$order_status = $_INPUT ['order_status'];
$begin_time = $_INPUT ['begin_time'];
$end_time = $_INPUT ['end_time'];
$cameraman_phone = ( int ) $_INPUT ['cameraman_phone'];
$source = ( int ) $_INPUT ['source'];
$requirement = ( int ) $_INPUT ['requirement'];
$question_category =  $_INPUT ['question_category'];
$type_id =  $_INPUT ['type_id'];

$where = yue_oa_list_status ( $oa_role, $list_status );

if($requirement)
{
	$where .= " AND source=4";
}
else
{
	$where .= " AND source IN (1,2,3,5)";
}

if ($order_id)
{
	$where .= " AND order_id={$order_id}";
}

if ($order_status)
{
	$where .= " AND order_status='{$order_status}'";
}

if ($begin_time && $end_time)
{
	$bt = strtotime ( $begin_time );
	$et = strtotime ( $end_time )+86400;
	$where .= " AND add_time BETWEEN {$bt} AND {$et}";
}

if ($cameraman_phone)
{
	$where .= " AND cameraman_phone={$cameraman_phone}";
}

if ($source)
{
	$where .= " AND source={$source}";
}

if($type_id)
{
    $where .= " AND ".type_where($type_id);
}


$page_obj->setvar ( array ("list_status" => $list_status, "order_id" => $order_id, "order_status" => $order_status, "begin_time" => $begin_time, "end_time" => $end_time, "cameraman_phone" => $cameraman_phone, "source" => $source,"requirement"=>$requirement,"question_category"=>$question_category,"type_id"=>$type_id ) );

$total_count = $model_oa_order_obj->get_order_list ( true, $where );

$page_obj->set ( $show_count, $total_count );

$list = $model_oa_order_obj->get_order_list ( false, $where, 'order_id DESC', $page_obj->limit () );

foreach ( $list as $k => $val )
{
	$list [$k] ['add_time'] = date ( "Y-m-d H:i", $val ['add_time'] );
	
	$list [$k] ['city_name'] = get_poco_location_name_by_location_id ( $val ['location_id'], false, false );
	
	$list [$k] ['address'] = $city_name . $val ['date_address'];

    if($val ['date_time']=='0000-00-00 00:00:00')
    {
        $list [$k] ['date_time'] = "";
    }
    else
    {
        $list [$k] ['date_time'] = date ( "Y-m-d H:i", strtotime ( $val ['date_time'] ) );
    }

	$list [$k] ['order_status'] = yue_oa_order_status ( $val ['order_status'] );
	
	$list [$k] ['count_enroll'] = $model_oa_enroll_obj->count_enroll($val ['order_id']);

    if($val['type_id_str'])
    {
        //商城分类名称
        $type_name_arr = array('12'=>'影棚租赁','5'=>'摄影培训','3'=>'化妆服务','31'=>'模特约拍','40'=>'摄影服务');
        $type_id_arr = explode(',',$val['type_id_str']);
        foreach($type_id_arr as $type_val)
        {
            $__type_name_arr[] = $type_name_arr[$type_val];
        }

        $type_name = implode('<br />',$__type_name_arr);
        unset($__type_name_arr);
    }
    else
    {
        //TT分类名称
        $type_name_arr = array('1'=>'影棚租赁','2'=>'摄影培训','3'=>'化妆服务','7'=>'模特约拍','0'=>'模特约拍');
        $type_name = $type_name_arr[$val['service_id']];
    }

    $list [$k] ['type_name'] = $type_name;
	
	if ($oa_role == 'operate')
	{
		if ($list_status == 'wait' && $val ['order_status'] == 'wait')
		{
			$button = '<a href="order_edit.php?order_id=' . $val ['order_id'] . '">下单</a><br /><a href="order_modify.php?list_status='.$list_status.'&order_id=' . $val ['order_id'] . '">订单修改</a>';
		}
		elseif ($list_status == 'doing' && $val ['order_status'] == 'confirm_order')
		{
			
			$button = '<a href="order_view.php?order_id=' . $val ['order_id'] . '">订单详情</a><br /><a href="order_modify.php?list_status='.$list_status.'&order_id=' . $val ['order_id'] . '">订单修改</a><br /><a href="model_match.php?order_id=' . $val ['order_id'] . '">商家匹配</a>';
		}
		elseif ($list_status == 'doing' && $val ['order_status'] == 'complete_recommend')
		{
			if ($val ['source']==4)
			{
				$button = '<a href="order_view.php?order_id=' . $val ['order_id'] . '">订单详情</a><br /><a href="order_modify.php?list_status='.$list_status.'&order_id=' . $val ['order_id'] . '">订单修改</a>';
			}
			else
			{
				$button = '<a href="model_match.php?order_id=' . $val ['order_id'] . '">通知拍摄</a><br /><a href="order_modify.php?list_status='.$list_status.'&order_id=' . $val ['order_id'] . '">订单修改</a>';
			}
		}
		elseif ($list_status == 'doing' && $val ['order_status'] == 'pay_confirm')
		{
			$button = '<a href="order_edit.php?order_id=' . $val ['order_id'] . '">通知摄影师</a><br /><a href="order_modify.php?list_status='.$list_status.'&order_id=' . $val ['order_id'] . '">订单修改</a>';
		}
		elseif ($list_status == 'doing' && $val ['order_status'] == 'wait_shoot')
		{
			$button = '<a href="order_edit.php?order_id=' . $val ['order_id'] . '">确认拍摄</a><br /><a href="order_modify.php?list_status='.$list_status.'&order_id=' . $val ['order_id'] . '">订单修改</a>';
		}
		elseif ($list_status == 'doing' && $val ['order_status'] == 'wait_close')
		{
			$button = '<a href="order_view.php?order_id=' . $val ['order_id'] . '">订单详情</a><br /><a href="close_order.php?order_id=' . $val ['order_id'] . '">结单</a><br /><a href="order_modify.php?list_status='.$list_status.'&order_id=' . $val ['order_id'] . '">订单修改</a>';
		}
		elseif ($val ['order_status'] == 'close' || $val ['order_status'] == 'cancel')
		{
			$button = '<a href="order_view.php?order_id=' . $val ['order_id'] . '">订单详情</a>';
		}
		else
		{
			$button = '<a href="order_view.php?order_id=' . $val ['order_id'] . '">订单详情</a><br /><a href="order_modify.php?list_status='.$list_status.'&order_id=' . $val ['order_id'] . '">订单修改</a>';
		}
	}
	elseif ($oa_role == 'expand')
	{
		if ($list_status == 'wait' && $val ['order_status'] == 'confirm_order')
		{
			$button = '<a href="model_match.php?order_id=' . $val ['order_id'] . '">商家匹配</a>';
		}
		else
		{
			$button = '<a href="order_view.php?order_id=' . $val ['order_id'] . '">订单详情</a>';
		}
	}
	elseif ($oa_role == 'financial')
	{
		if ($list_status == 'wait' && $val ['order_status'] == 'shoot_confirm')
		{
			$button = '<a href="payment.php?order_id=' . $val ['order_id'] . '">付款确认</a>';
		}
		elseif ($list_status == 'doing')
		{
			$button = '<a href="refund.php?order_id=' . $val ['order_id'] . '">退款</a>';
		}
		else
		{
			$button = '<a href="order_view.php?order_id=' . $val ['order_id'] . '">订单详情</a>';
			
		}
	}
	elseif ($oa_role == 'admin')
	{
		$button = '<a href="order_edit.php?order_id=' . $val ['order_id'] . '">下单</a><br />';
		$button .= '<a href="close_order.php?order_id=' . $val ['order_id'] . '">结单</a><br />';
		$button .= '<a href="model_match.php?order_id=' . $val ['order_id'] . '">确认拍摄</a><br />';
		$button .= '<a href="model_match.php?order_id=' . $val ['order_id'] . '">商家匹配</a><br />';
		$button .= '<a href="payment.php?order_id=' . $val ['order_id'] . '">付款确认</a><br />';
		$button .= '<a href="refund.php?order_id=' . $val ['order_id'] . '">退款</a>';
	}
	
	$list [$k] ['button'] = $button;
}

$tpl->assign ( "page", $page_obj->output ( 1 ) );

$tpl->assign ( 'list', $list );

$tpl->assign ( 'oa_role', $oa_role );



$tpl->assign ( 'question_category', $question_category );
$tpl->assign ( 'requirement', $requirement );
$tpl->assign ( 'list_status', $list_status );
$tpl->assign ( 'order_status', $order_status );
$tpl->assign ( 'cameraman_phone', $cameraman_phone );
$tpl->assign ( 'source', $source );
$tpl->assign ( 'begin_time', $begin_time );
$tpl->assign ( 'end_time', $end_time );
$tpl->assign ( 'type_id', $type_id );

$tpl->output ();


?>