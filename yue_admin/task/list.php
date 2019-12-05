<?php
include_once ('/disk/data/htdocs232/poco/pai/yue_admin/audit/include/Classes/PHPExcel.php');
include_once 'common.inc.php';
$tpl = new SmartTemplate ( TASK_TEMPLATES_ROOT."list.tpl.htm" );
$task_request_obj = POCO::singleton('pai_task_request_class');
$user_obj = POCO::singleton('pai_user_class');
$task_admin_log_obj = POCO::singleton('pai_task_admin_log_class');
$task_review_obj = POCO::singleton('pai_task_review_class');


if(isset($_INPUT ['lead_type']))
{
	$lead_type = (int)$_INPUT ['lead_type'];
	$re = $task_request_obj->set_request_lead_mode($lead_type);
	$re = array('result'=>$re);
	exit(json_encode($re));	
}

if($_INPUT ['submit_remark']==1)
{
	$request_id = $_INPUT ['request_id'];
	$remark = $_INPUT ['remark'];
	$remark = iconv("UTF-8","GBK",$remark);
	$task_log_obj = POCO::singleton('pai_task_admin_log_class');
	$ret = $task_log_obj->add_log($yue_login_id,1008,1,$_INPUT,$remark,$request_id);
	echo (int)$ret;
	exit;
}

$lead_status = $_INPUT ['lead_status'] ? $_INPUT ['lead_status'] : '0';
$begin_time = $_INPUT ['begin_time']?$_INPUT ['begin_time']:date('Y-m-d', strtotime('-7 day'));
$end_time = $_INPUT ['end_time']?$_INPUT ['end_time']:date('Y-m-d', strtotime('+7 day'));
$service_id = (int)$_INPUT ['service_id'];

$task_service_obj = POCO::singleton('pai_task_service_class');
$service_list = $task_service_obj->get_service_list(false,'status=1');
$service_name = array();
foreach($service_list as $key => $val)
{
	if($val['service_id'] == $service_id)
	{
		$service_list[$key]['selected'] = true;
	}
	$service_name[$val['service_id']] = $val['service_name'];
}



$where = "request_id<2563"; //TT单转到OA去处理，2015-08-05 18:39:00
$table_fn = 'get_request_detail_list';
if($lead_status=='0')
{
	$where.= " and lead_status=0";
	$headArr = array("用户ID","昵称","手机","服务类型","地区","过期时间","备注");
}
elseif($lead_status=='1')
{
	$where.= " and lead_status=1";
	$headArr = array("用户ID","昵称","手机","服务类型","地区","是否雇佣","已雇佣供应商","金额","评价","备注");
	
}
elseif($lead_status=='2')
{
	$headArr = array("用户ID","昵称","手机","服务类型","地区","过期时间","审核不通过原因","备注");
	$table_fn = 'get_request_del_detail_list';
}
if ($begin_time && $end_time) 
{
	$where .= " and FROM_UNIXTIME(expire_time,'%Y-%m-%d') between '{$begin_time}' and '{$end_time}'";
}
$total_count = $task_request_obj->$table_fn(0, $service_id, true, $where);

$page_obj = new show_page ();
$show_count = 20;
$page_obj->setvar ( array ("lead_status" => $lead_status,"begin_time" => $begin_time,"end_time" => $end_time,"service_id" => $service_id,) );
$page_obj->set ( $show_count, $total_count );

if($_INPUT['output']==1)
{
	$list = $task_request_obj->$table_fn(0, $service_id, false, $where, "request_id desc", "0,1000000");
}
else
{
	$list = $task_request_obj->$table_fn(0, $service_id, false, $where, "request_id desc", $page_obj->limit());
}

$lead_type = $task_request_obj->get_request_lead_mode();
foreach($list as $k=>$val)
{
	$list[$k]['expire_time'] = date("Y-m-d H:i:s",$val['expire_time']);
	$list[$k]['add_time'] = date("Y-m-d H:i:s",$val['add_time']);
	$list[$k]['service_name'] = $service_name[$val['service_id']];	
	$list[$k]['lead_status_name'] = $val['lead_status']?"已推荐":"未审核";
	$list[$k]['admin_name'] = $val['admin_id']?get_user_nickname_by_user_id($val['admin_id']):"";
	$list[$k]['del_time'] = date("Y-m-d H:i:s",$val['del_time']);
	if(!$val['where_str'])
	{
		$user_info = $user_obj->get_user_info($val['user_id']);
		$list[$k]['where_str'] = get_poco_location_name_by_location_id ( $user_info ['location_id'] );
	}
	$where_log = "type_id=1008 and action_id=".$val['request_id']." and action_type=1";
	$log_list = $task_admin_log_obj->get_log_list(false, $where_log, 'id DESC', '0,1');
	$list[$k]['remark'] =$log_list[0]['note'];
	
	$output_data[$k]['user_id'] = $list[$k]['user_id'];
	$output_data[$k]['nickname'] = $list[$k]['nickname'];
	$output_data[$k]['cellphone'] = $list[$k]['cellphone'];
	$output_data[$k]['service_name'] = $list[$k]['service_name'];
	$output_data[$k]['where_str'] = $list[$k]['where_str'];
	
	if($lead_status=='0')
	{
		$output_data[$k]['expire_time'] = $list[$k]['expire_time'];
		$output_data[$k]['remark'] = $list[$k]['remark'];
	}
	elseif($lead_status=='1')
	{
		if ($val['status']==1) {
			$hire_text="已雇佣";
		}
		else
		{
			$hire_text="未雇佣";
		}
		$output_data[$k]['hire_text'] = $hire_text;
		
		$seller_info = $task_request_obj->get_seller_info_by_request_id($val['request_id']);
		
		$output_data[$k]['seller_info'] = $seller_info['profile_title'];
		$output_data[$k]['pay_amount'] = $seller_info['pay_amount'];
		
		$review_list = $task_review_obj->get_review_list(false, "request_id=".$val['request_id'], 'add_time DESC', '0,1');
		
		$output_data[$k]['review'] = $review_list[0]['content'];
		
		$output_data[$k]['remark'] = $list[$k]['remark'];
	}
	elseif($lead_status=='2')
	{
		$output_data[$k]['expire_time'] = $list[$k]['expire_time'];
		$output_data[$k]['remark'] = $list[$k]['remark'];
	}
}

if($_INPUT['xxx']==1)
{
	print_r($list);
}

if($_INPUT['output']==1)
{
	$fileName = "tt";
	getExcel ( $fileName, $headArr, $output_data );
	exit;
}

$tpl->assign ( "page", $page_obj->output ( 1 ) );
$tpl->assign ( 'list', $list );
$tpl->assign ( 'lead_status', $lead_status );
$tpl->assign ( 'begin_time', $begin_time );
$tpl->assign ( 'end_time', $end_time );
$tpl->assign ( 'service_id', $service_id );
$tpl->assign ( 'service_list', $service_list );
$tpl->assign ( 'lead_type', $lead_type);
$tpl->output ();
?>