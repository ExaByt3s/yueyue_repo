<?php
include_once 'config.php';
$pc_wap = 'wap/';
$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'person_order/index.tpl.htm');

// Ȩ�޼��
mall_check_user_permissions($yue_login_id);

// ͷ��css���
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/head.php');

// ���ܲ���
$type_id = intval($_INPUT['type_id']);
$location_id = '';
$location_name = get_poco_location_name_by_location_id ( $location_id );
// ��������
$type_arr[0] = array('id'=>'31','name'=>'ģ��');
$type_arr[1] = array('id'=>'3','name'=>'��ױ');
$type_arr[2] = array('id'=>'12','name'=>'Ӱ��');
$type_arr[3] = array('id'=>'40','name'=>'��Ӱʦ');
$type_arr[4] = array('id'=>'5','name'=>'��ѵ');

foreach($type_arr as $key => $val)
{
	if(intval($val['id']) == $type_id)
	{
		$type_arr[$key]['class'] = 'ui-button ui-button-size-x ui-button-bg-fff order-type-btn on-btn';
	}
	else
	{
		$type_arr[$key]['class'] = 'ui-button ui-button-size-x ui-button-bg-fff order-type-btn';
	}
}

// ͷ��������ʽ��js����
$wap_global_top = _get_wbc_head();
$tpl->assign('wap_global_top', $wap_global_top);

// ���ڲ��ʹ�÷�Χ
$now_year = date("Y");
$now_month = date("m");
$next_year = $now_year+1;
$next_month = $now_month-6;

$tpl->assign('now_range', $now_year.'-'.$now_month);
$tpl->assign('next_range', $next_year.'-'.$next_month);
$tpl->assign('location_id', $location_id);
$tpl->assign('location_name', $location_name);
$tpl->assign('type_arr', $type_arr);
$tpl->output();
?>