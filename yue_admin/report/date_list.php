<?php
include_once 'common.inc.php';
//获取地址
include_once ("/disk/data/htdocs232/poco/php_common/poco_location_fun.inc.php");
$tpl = new SmartTemplate ( "date_list.tpl.htm" );

$page_obj = new show_page ();
$activity_code_obj = POCO::singleton ( 'pai_activity_code_class' );
$event_details_obj = POCO::singleton ( 'event_details_class' );
//用户表
$user_obj = POCO::singleton ( 'pai_user_class' );
//机构库
$pai_organization_obj = POCO::singleton("pai_organization_class");
//模特库
$user_add_obj = POCO::singleton ( 'pai_model_add_class' );
$model_audit_obj = POCO::singleton('pai_model_audit_class');//模特审核库

$date_status = $_INPUT ['date_status'] ? $_INPUT ['date_status'] : 'all';

$from_date_id = $_INPUT ['from_date_id'];
$begin_time = $_INPUT ['begin_time'];
$end_time = $_INPUT ['end_time'];

//测试人员帐号
$test_user_id = TEST_PAI_USER_ID;



$where_str = "from_date_id not in ($test_user_id) and to_date_id not in ($test_user_id) and pay_status=1";

if($date_status!='all')
{
	$where_str .= " and date_status='{$date_status}'";
}

if($from_date_id)
{
	$where_str .= " and from_date_id={$from_date_id}";
}

if($begin_time && $end_time)
{
	$bt = strtotime($begin_time);
	$et = strtotime($end_time)+86400;
	$where_str .= " and add_time between {$bt} and {$et}";
}

$show_count = 20;
$page_obj->setvar ( array ("date_status" => $date_status,"from_date_id"=>$from_date_id,"begin_time"=>$begin_time,"end_time"=>$end_time ) );

$total_count = get_all_event_date ( true, $where_str );

$page_obj->set ( $show_count, $total_count );

$date_list = get_all_event_date ( false, $where_str, "date_id DESC", $page_obj->limit () );

foreach ( $date_list as $k => $val )
{
	//print_r($date_list);
	$date_list [$k] ['cameraman_nickname'] = get_user_nickname_by_user_id ( $val ['from_date_id'] );
	$date_list [$k] ['model_nickname'] = get_user_nickname_by_user_id ( $val ['to_date_id'] );
	$date_list [$k] ['pay_time'] = date ( "Y-m-d H:i", $val ['pay_time'] );
	$date_list[$k]['is_set']  = $user_add_obj->get_user_inputer_name_by_user_id($val ['to_date_id']);
	$date_list[$k]['org_name']  = $pai_organization_obj->get_org_name_by_user_id($val ['org_user_id']);
	$user_info  = $user_obj->get_user_info($val ['to_date_id']);
	//print_r($user_info);
	if(is_array($user_info))
	{
	   $date_list[$k]['location_name'] = get_poco_location_name_by_location_id ($user_info['location_id']);
	   //echo $location_name;
	}
	if($val ['enroll_id'])
	{
		$is_checked = $activity_code_obj->check_code_scan ( $val ['enroll_id'] );
	}
	if ($is_checked)
	{
		$date_list [$k] ['is_checked'] = "已签到";
	} else
	{
		$date_list [$k] ['is_checked'] = "未签到";
	}
	unset($is_checked);
	
	if($val ['event_id'])
	{
		$event_info = $event_details_obj->get_event_by_event_id($val ['event_id']);
		if($event_info['event_status']==0)
		{
			$date_list [$k] ['event_status'] = "进行中";
		}elseif($event_info['event_status']==2)
		{
			$date_list [$k] ['event_status'] = "已结束";
		}elseif($event_info['event_status']==3)
		{
			$date_list [$k] ['event_status'] = "已取消";
		}
	}
	else
	{
		$date_list [$k] ['event_status'] = "";
	}
    $date_list[$k]['is_approval'] = $model_audit_obj->get_status_by_user_id( $val ['to_date_id']);
	
}
//print_r($date_list);exit;
$tpl->assign ( 'total_count', $total_count );
$tpl->assign ( 'begin_time', $begin_time );
$tpl->assign ( 'end_time', $end_time );
$tpl->assign ( 'from_date_id', $from_date_id );
$tpl->assign ( 'date_status', $date_status );
$tpl->assign ( "page", $page_obj->output ( 1 ) );
$tpl->assign ( 'date_list', $date_list );
$tpl->assign ( 'MOBILE_ADMIN_TOP', $_POCO_STAT_MOBILE_ADMIN_REPORT_HEADER );
$tpl->output ();
?>