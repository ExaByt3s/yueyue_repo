<?php
/**
 * hudw 2014.9.1
 * ��ϸҳ 
 */
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

if(!$yue_login_id)
{
	//$output_arr['data'] = "no login";
	//mobile_output($output_arr,false);
	//exit();
}

/**
 * ҳ����ղ���
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

// �����
$act_intro = array();
// ���Ϣ
$act_info = array();
// �����
$act_arrange = array();

$act_intro['title'] = $ret['title'];
$act_intro['content'] = $ret['content'];
$act_intro['other_info_detail'] = $ret['other_info_detail']; // ����ģ��

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
$act_arrange['leader_info_detail'] = $ret['leader_info_detail'];// ����

/*
* �Ƿ��ѹ�ע�û
* 
* @param int    $event_id    �ID
* @param int    $user_id     �û�ID
* 
* return bool
*/
if($yue_login_id)
{
	$pai_follow_obj = POCO::singleton('pai_event_follow_class');//���ע

	$is_follow = $pai_follow_obj->check_event_follow($event_id, $yue_login_id);
}

foreach ($act_arrange['table_info'] as $key => $value) 
{

	if($is_show_table_num)
	{
		$table_num = '��'.($key+1).' �� ';
	}
	else
	{
		$table_num = '';
	}
	
	$act_arrange['table_info'][$key]['session']  = $key+1;
	$act_arrange['table_info'][$key]['begin_time'] = date("m.d H:i",$value['begin_time']);
	$act_arrange['table_info'][$key]['end_time'] = date("m.d H:i",$value['end_time']);
	$act_arrange['table_info'][$key]['text']  = date("m.d H:i",$value['begin_time']).' - '.date("H:i",$value['end_time']);
	
	//����Ƿ��ظ�����
	$is_duplicate =0;

	if($yue_login_id)
	{
		$is_duplicate = check_duplicate($yue_login_id,$event_id,"all", $value['id']);

		$user_id = get_relate_poco_id($yue_login_id);
		$enroll_arr = get_enroll_list("user_id={$user_id} and event_id={$event_id} and table_id=".$value['id'], false);

		$act_arrange['table_info'][$key]['enroll_id'] = (int)$enroll_arr[0]['enroll_id'];
	}

	
	
	

	//��鳡��ʱ��
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
		$act_arrange['table_info'][$key]['table_text'] = '(�ѹ���)';
	}
	elseif($pay_status==='0')
	{
		$act_arrange['table_info'][$key]['table_status'] = 2;
		$act_arrange['table_info'][$key]['table_text'] = '(δ֧��)';
	}
	elseif($pay_status==='1')
	{
		$act_arrange['table_info'][$key]['table_status'] = 1;
		$act_arrange['table_info'][$key]['table_text'] = '(��֧��)';
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