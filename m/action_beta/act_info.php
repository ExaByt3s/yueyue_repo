<?php
/**
 * hudw 2014.9.1
 * 详细页 
 */
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

if(!$yue_login_id)
{
	//$output_arr['data'] = "no login";
	//mobile_output($output_arr,false);
	//exit();
}

/**
 * 页面接收参数
 */
$event_id = intval($_INPUT['event_id']);
$is_show_table_num = $_INPUT['is_show_table_num'];
$is_from_my_act_list = $_INPUT['is_from_my_act_list'];

$ret = get_event_by_event_id($event_id);

if($is_from_my_act_list)
{
	$output_arr['data'] = $ret;

	mobile_output($output_arr,false);

	exit();
}

// 活动介绍
$act_intro = array();
// 活动信息
$act_info = array();
// 活动安排
$act_arrange = array();

$act_intro['title'] = $ret['title'];
$act_intro['content'] = $ret['content'];
$act_intro['other_info_detail'] = $ret['other_info_detail']; // 场地模特

$act_info = array();
$act_info['title'] = $ret['title'];
$act_info['event_time'] = $ret['event_time'];
$act_info['address'] = $ret['address'];
$act_info['club_name'] = $ret['club_name'];
$act_info['budget'] = $ret['budget'];
$act_info['join_count'] = $ret['join_count'];
$act_info['hit_count'] = $ret['hit_count'];
$act_info['event_status'] = (int)$ret['event_status'];
$act_info['event_organizers'] = $ret['event_organizers'];
$act_info['event_join'] = $ret['event_join'];
$act_info['user_icon'] = $ret['user_icon'];
$act_info['nickname'] = $ret['nickname'];

$act_arrange['title'] = $ret['title'];
$act_arrange['table_info'] = $ret['table_info'];
$act_arrange['remark'] = $ret['remark'];
$act_arrange['leader_info_detail'] = $ret['leader_info_detail'];// 活动领队

/*
* 是否已关注该活动
* 
* @param int    $event_id    活动ID
* @param int    $user_id     用户ID
* 
* return bool
*/
if($yue_login_id)
{
	$pai_follow_obj = POCO::singleton('pai_event_follow_class');//活动关注

	$is_follow = $pai_follow_obj->check_event_follow($event_id, $yue_login_id);
}

foreach ($act_arrange['table_info'] as $key => $value) 
{

	if($is_show_table_num)
	{
		$table_num = '第'.($key+1).' 场 ';
	}
	else
	{
		$table_num = '';
	}
	
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
	}
	elseif($pay_status==='0')
	{
		$act_arrange['table_info'][$key]['table_status'] = 2;
		$act_arrange['table_info'][$key]['table_text'] = '(未支付)';
	}
	elseif($pay_status==='1')
	{
		$act_arrange['table_info'][$key]['table_status'] = 1;
		$act_arrange['table_info'][$key]['table_text'] = '(已支付)';
	}
	else
	{
		$act_arrange['table_info'][$key]['table_status'] = 0;
		$act_arrange['table_info'][$key]['table_text'] = '';
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




$output_arr['data'] = array
(
	'act_intro' => $act_intro,
	'act_info' => $act_info,
	'act_arrange' => $act_arrange,
	'pub_user_id' => $ret['user_id'],
	'is_follow' => $is_follow,
	'user_info' =>$user_info,
	'share_text' => $ret['share_text']
);

mobile_output($output_arr,false);
?>