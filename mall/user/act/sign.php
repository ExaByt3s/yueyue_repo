<?php
/**
 * 
 *
 * @author hudingwen
 * @version $Id$
 * @copyright , 17 July, 2015
 * @package default
 */

/**
 * Define �����
 */
include_once 'config.php';

$pc_wap = 'wap/';
$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'act/sign.tpl.html');

// Ȩ�޼��
$check_arr = mall_check_user_permissions($yue_login_id);

// �˺��л�ʱ
if(intval($check_arr['switch']) == 1)
{
	$url='http://'.$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"]; 
	header("Location:{$url}");
	die();
}

// ͷ��css���
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/head.php');
// ͷ��������ʽ��js����
$wap_global_top = _get_wbc_head();
$tpl->assign('wap_global_top', $wap_global_top);

// �ײ������ļ�����
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/footer.php');
$wap_global_footer = _get_wbc_footer();
$tpl->assign('wap_global_footer', $wap_global_footer);

$event_id = intval($_INPUT['event_id']);

//$event_id = 40150;

$ret = get_mark_list_v2($event_id);

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


$output_arr['list'] = $ret;

$output_arr['is_follow'] = $is_follow;

$output_arr['event_title'] = $ret[0]['event_title'];
$output_arr['event_organizers'] = $ret[0]['event_organizers'];
$output_arr['event_status'] = $ret[0]['event_status'];

$show_join_btn = 1;
$show_scan_btn = 0;

$user_agent_arr = mall_get_user_agent_arr();

if($_INPUT['print'] == 1)
{
	print_r($user_agent_arr);
	die();
}

if($output_arr['event_organizers'] == $yue_login_id)
{
	$show_join_btn = 0;
	
	// ֻ��ԼԼApp����ɨ�蹦��
	if($user_agent_arr['is_yueyue_app'] == 1)
	{
		$show_scan_btn = 1;
	}

}
else if($output_arr['event_status'] != 0)
{
	$show_join_btn = 0;
}

//================= Appʵ�����칦�� =================
$chat_json = 'null';
if(MALL_UA_IS_YUEYUE == 1 && $output_arr['event_organizers'] == $yue_login_id)
{
	$sendername = get_user_nickname_by_user_id($yue_login_id);

	$sendericon = get_user_icon($yue_login_id,165);

	$ret_json = array(
		'senderid' => $yue_login_id,
		'sendername' => $sendername,
		'sendericon' => $sendericon
	);		
	
	$chat_json = mall_output_format_data($ret_json);
	
	$tpl->assign('chat',1);
}
$tpl->assign('is_yueyue_app',MALL_UA_IS_YUEYUE);
$tpl->assign('chat_json',$chat_json);

$show_join_btn = 0;

$list = mall_output_format_data($output_arr['list']);


$tpl->assign('sign_data',$list);
$tpl->assign('title',$output_arr['event_title']);
$tpl->assign('event_organizers',$output_arr['event_organizers']);
$tpl->assign('event_id',$event_id);
$tpl->assign('show_join_btn',$show_join_btn);
$tpl->assign('show_scan_btn',$show_scan_btn);
$tpl->output();	
?>