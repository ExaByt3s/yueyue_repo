<?php
include_once 'config.php';
$pc_wap = 'wap/';

// 权限检查
$check_arr = mall_check_user_permissions($yue_login_id);

// 账号切换时
if(intval($check_arr['switch']) == 1)
{
	$url='http://'.$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"]; 
	header("Location:{$url}");
	die();
}


$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'act/order.tpl.html');

// 头部css相关
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/head.php');
// 头部公共样式和js引入
$wap_global_top = _get_wbc_head();
$tpl->assign('wap_global_top', $wap_global_top);

// 底部公共文件引入
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/footer.php');
$wap_global_footer = _get_wbc_footer();
$tpl->assign('wap_global_footer', $wap_global_footer);

/**
 * 页面接收参数
 */
$event_id = intval($_INPUT['event_id']);

$ret = get_event_by_event_id($event_id);

// 活动介绍
$act_intro = array();
// 活动信息
$act_info = array();
// 活动安排
$act_arrange = array();


$act_info = array();
$act_info['title'] = $ret['title'];
$act_info['event_time'] = $ret['event_time'];

$act_info['budget'] = $ret['budget'];
$act_info['join_count'] = $ret['join_count'];
$act_info['event_status'] = (int)$ret['event_status'];
$act_info['event_organizers'] = $ret['event_organizers'];
$act_info['event_join'] = $ret['event_join'];

$act_arrange['title'] = $ret['title'];
$act_arrange['table_info'] = $ret['table_info'];

foreach ($act_arrange['table_info'] as $key => $value)
{


	$act_arrange['table_info'][$key]['session']  = $key+1;
	$act_arrange['table_info'][$key]['begin_time'] = date("m.d H:i",$value['begin_time']);
	$act_arrange['table_info'][$key]['end_time'] = date("m.d H:i",$value['end_time']);
	$act_arrange['table_info'][$key]['text']  = date("m.d H:i",$value['begin_time']).' - '.date("H:i",$value['end_time']);

	//检查是否重复报名
	$is_duplicate =0;

	if($yue_login_id)
	{
		$is_duplicate = check_duplicate($yue_login_id,$event_id,"all", $value['id']);

		$user_id = get_relate_poco_id($yue_login_id);
		$enroll_arr = get_enroll_list("user_id={$user_id} and event_id={$event_id} and table_id=".$value['id'], false);

		$act_arrange['table_info'][$key]['enroll_id'] = (int)$enroll_arr[0]['enroll_id'];
	}





	//检查场次时间
	if($value['end_time']<time())
	{
		$table_is_end = true;
	}
	else
	{
		$table_is_end = false;
	}

	$pay_status = $enroll_arr[0]['pay_status'];

	if($table_is_end)
	{
		$act_arrange['table_info'][$key]['table_status'] = 3;
		$act_arrange['table_info'][$key]['table_text'] = '(已过期)';
		
		$act_arrange['table_info'][$key]['table_btn_enable'] = 0;
	}
	elseif($pay_status==='0')
	{
		$act_arrange['table_info'][$key]['table_status'] = 2;
		$act_arrange['table_info'][$key]['table_text'] = '(未支付)';
		
		$act_arrange['table_info'][$key]['table_btn_enable'] = 1;
		$act_arrange['table_info'][$key]['table_btn_jump'] = 1;
	}
	elseif($pay_status==='1')
	{
		$act_arrange['table_info'][$key]['table_status'] = 1;
		$act_arrange['table_info'][$key]['table_text'] = '(已支付)';
		
		$act_arrange['table_info'][$key]['table_btn_enable'] = 0;
	}
	else
	{
		$act_arrange['table_info'][$key]['table_status'] = 0;
		$act_arrange['table_info'][$key]['table_text'] = '';
		
		$act_arrange['table_info'][$key]['table_btn_enable'] = 1;
	}


}

if($yue_login_id)
{
	$pai_obj   = POCO::singleton('pai_user_class');
	$user_info = $pai_obj->get_user_info_by_user_id($yue_login_id);
}
else
{
	$user_info = array();
}



$tpl->assign('act_intro',$act_intro);
$tpl->assign('act_info',$act_info);
$tpl->assign('act_arrange',$act_arrange);
$tpl->assign('pub_user_id',$ret['user_id']);

$tpl->assign('user_info',$user_info);
$tpl->assign('share_text',$ret['share_text']);
$tpl->assign('event_id',$event_id);


if($_INPUT['print'] == 1)
{
	print_r($tpl);
	die();
}


$tpl->output();
?>