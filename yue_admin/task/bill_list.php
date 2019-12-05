<?php
include_once ('/disk/data/htdocs232/poco/pai/yue_admin/audit/include/Classes/PHPExcel.php');
include_once 'common.inc.php';
if($_INPUT ['action'] == 'pay')
{
	$request_id = (int)$_INPUT ['request_id'];
	$task_request_obj = POCO::singleton('pai_task_request_class');
	$re = $task_request_obj->update_request_paytouser($request_id);
	$task_log_obj = POCO::singleton('pai_task_admin_log_class');
	$task_log_obj->add_log($yue_login_id,1007,1,$_INPUT,'',$request_id);
	if($re)
	{
		echo json_encode(array('result'=>true));
	}
	else
	{
		echo json_encode(array('result'=>false));
	}
	exit;
}
$tpl = new SmartTemplate ( TASK_TEMPLATES_ROOT."bill_list.tpl.htm" );
$task_request_obj = POCO::singleton('pai_task_request_class');
$task_user_obj = POCO::singleton('pai_user_class');

$status = $_INPUT ['status']?$_INPUT ['status']:0;
$begin_time = $_INPUT ['begin_time']?$_INPUT ['begin_time']:date('Y-m-d', strtotime('-7 day'));
$end_time = $_INPUT ['end_time']?$_INPUT ['end_time']:date('Y-m-d', strtotime('+7 day'));
$service_id = (int)$_INPUT ['service_id'];
$_INPUT ['action'];

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

$where = 1;
if($status == '0')
{
	$where.= " and status=0 and lead_status = 1 and FROM_UNIXTIME(expire_time,'%Y-%m-%d') between '{$begin_time}' and '{$end_time}'";
	$select_name = '未雇佣';
	$action_name = '过期';
}
elseif($status == '1')
{
	$where.= " and status=1 and is_pay = 0 and FROM_UNIXTIME(hired_time,'%Y-%m-%d') between '{$begin_time}' and '{$end_time}'";//hired_time
	$select_name = '已雇佣';
	$action_name = '雇佣';
}
elseif($status == '2')
{
	//$where.= " and is_pay=1 and FROM_UNIXTIME(pay_time,'%Y-%m-%d') between '{$begin_time}' and '{$end_time}'";//pay_time
	$where.= " and is_pay=1 and is_review=0 and FROM_UNIXTIME(pay_time,'%Y-%m-%d') between '{$begin_time}' and '{$end_time}'";//pay_time
	$select_name = '已支付';
	$action_name = '支付';
}
elseif($status == '3')
{
	$where.= " and is_review=1 and FROM_UNIXTIME(review_time,'%Y-%m-%d') between '{$begin_time}' and '{$end_time}'";//pay_time
	$select_name = '已评价';
	$action_name = '评价';
}
else
{
	$where.= " and status=1 and is_pay = 0 and FROM_UNIXTIME(hired_time,'%Y-%m-%d') between '{$begin_time}' and '{$end_time}'";
	$where.= " or is_pay=1 and is_review=0 and FROM_UNIXTIME(pay_time,'%Y-%m-%d') between '{$begin_time}' and '{$end_time}'";
	$where.= " and is_review=1 and FROM_UNIXTIME(review_time,'%Y-%m-%d') between '{$begin_time}' and '{$end_time}'";//pay_time
	$where.= " or status=0 and lead_status = 1 and FROM_UNIXTIME(expire_time,'%Y-%m-%d') between '{$begin_time}' and '{$end_time}'";
}
//echo $where;
if($_INPUT ['action'] == 'export')
{
	$list = $task_request_obj->get_request_detail_list(0, $service_id, false, $where, "request_id desc",'0,10000');//最多导出10000记录
	$headArr =array(
					'订单ID',
							 '需求者ID',
							 '需求者昵称',
							 '服务类型',
							 '联系电话',
							 '联系EMAIL',
							 '服务时间',
							 '服务地区',
							 '过期时间',
							 '付款金额',
							 '付款订单号',
							 '付款时间',
							 '评论时间',
							 '支付ID',
							 '收款方ID',
							 '收款方昵称',
							 '打款时间',
					);
	$output_data = array();
	foreach($list as $k => $val)
	{
		$val['pay_time'] = $val['pay_time']?date("Y-m-d H:i:s",$val['pay_time']):'';
		$val['hired_time'] = $val['hired_time']?date("Y-m-d H:i:s",$val['hired_time']):'';
		$val['expire_time'] = $val['expire_time']?date("Y-m-d H:i:s",$val['expire_time']):'';
		$val['review_time'] = $val['review_time']?date("Y-m-d H:i:s",$val['review_time']):'';
		$val['service_name'] = $service_name[$val['service_id']];	
		$val['select_name'] = ($select_name?$select_name:$val['status_name']);
		$val['paytouser_time'] = $val['paytouser_time']?date("Y-m-d H:i:s",$val['paytouser_time']):'';
		$user_info = $task_user_obj->get_user_info($val['user_id']);
		$val['where_str'] = $val['where_str']?$val['where_str']:get_poco_location_name_by_location_id($user_info['location_id']);
		foreach($val['quotes_list'] as $val_de)
		{
			if($val_de['is_pay'])
			{
				$val['pay_amount'] .= $val_de['pay_amount'];
				$val['payment_no'] .= $val_de['payment_no'];
				$val['pay_user_id'] .= $val_de['user_id'];
				$val['pay_user_name'] .= get_user_nickname_by_user_id($val_de['user_id']);
				$val['user_pay_time'] .= $val_de['pay_time']?date("Y-m-d H:i:s",$val_de['pay_time']):'';
				$val['quotes_id'] .= $val_de['quotes_id'];
			}
		}
		$output_data[] = array(
							   $val['request_id'],
								$val['user_id'],
								$val['nickname'],
								$val['title'],
								$val['cellphone'],
								$val['email'],
								$val['when_str'],
								$val['where_str'],
								$val['expire_time'],
								$val['pay_amount'],
								$val['payment_no'],
								$val['pay_time'],
								$val['review_time'],
								$val['quotes_id'],
								$val['pay_user_id'],
								$val['pay_user_name'],
								$val['paytouser_time'],
							   );
	}
	getExcel ( $select_name, $headArr, $output_data,$select_name.'列表' );
	exit;
}

$total_count = $task_request_obj->get_request_detail_list(0, $service_id, true, $where);

$page_obj = new show_page ();
$show_count = 20;
$page_obj->setvar ( array ("status" => $status,"begin_time" => $begin_time,"end_time" => $end_time,"service_id" => $service_id,) );
$page_obj->set ( $show_count, $total_count );

$list = $task_request_obj->get_request_detail_list(0, $service_id, false, $where, "request_id desc", $page_obj->limit());
foreach($list as $k=>$val)
{
	$list[$k]['action_time'] = date("Y-m-d H:i:s",$val['review_time']?$val['review_time']:($val['pay_time']?$val['pay_time']:($val['hired_time']?$val['hired_time']:$val['expire_time'])));
	$list[$k]['service_name'] = $service_name[$val['service_id']];	
	$list[$k]['select_name'] = ($select_name?$select_name:$val['status_name']);
	$list[$k]['paytouser_time'] = date("Y-m-d H:i:s",$val['paytouser_time']);
	$user_info = $task_user_obj->get_user_info($val['user_id']);
	$list[$k]['where_str'] = $list[$k]['where_str']?$list[$k]['where_str']:get_poco_location_name_by_location_id($user_info['location_id']);
	foreach($val['quotes_list'] as $val_de)
	{
		if($val_de['is_pay'])
		{
			$list[$k]['pay_amount'] .= $val_de['pay_amount'];
			$list[$k]['pay_user_id'] .= $val_de['user_id'];
			$list[$k]['user_pay_time'] .= date("Y-m-d H:i:s",$val_de['pay_time']);
			$list[$k]['quotes_id'] .= $val_de['quotes_id'];
		}
	}
}

$tpl->assign ( "page", $page_obj->output ( 1 ) );
$tpl->assign ( 'list', $list );
$tpl->assign ( 'status', $status );
$tpl->assign ( 'begin_time', $begin_time );
$tpl->assign ( 'end_time', $end_time );
$tpl->assign ( 'service_id', $service_id );
$tpl->assign ( 'service_list', $service_list );
$tpl->assign ( 'action_name', $action_name );
$tpl->output ();
?>