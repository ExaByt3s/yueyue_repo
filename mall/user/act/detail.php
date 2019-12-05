<?php
/**
 *
 *
 * @author hudingwen
 * @version $Id$
 * @copyright , 16 July, 2015
 * @package default
 */

/**
 * Define 外拍详情
 */

include_once 'config.php';


/**
 * 页面接收参数
 */
$event_id = intval($_INPUT['event_id']);
$is_show_table_num = $_INPUT['is_show_table_num'];


// ========================= 区分pc，wap模板与数据格式整理 start  =======================
if(MALL_UA_IS_PC == 1)
{
    //****************** pc版 ******************
	header("location: http://event.poco.cn/event_browse.php?event_id=".$event_id) ;
}
else
{

    //****************** wap版 ******************
 	$pc_wap = 'wap/';
 	$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'act/detail.tpl.html');

 	if(MALL_UA_IS_YUEYUE == 1)
 	{
 		define('MALL_NOT_REDIRECT_LOGIN',1);

 		// 权限检查
 		$check_arr = mall_check_user_permissions($yue_login_id);

 		// 账号切换时
 		if(intval($check_arr['switch']) == 1)
 		{
 			$url='http://'.$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"]; 
 			header("Location:{$url}");
 			die();
 		}
 	}

 	// 头部css相关
 	include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/head.php');
 	// 头部公共样式和js引入
 	$wap_global_top = _get_wbc_head();
 	$tpl->assign('wap_global_top', $wap_global_top);

 	// 底部公共文件引入
 	include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/footer.php');
 	$wap_global_footer = _get_wbc_footer();
 	$tpl->assign('wap_global_footer', $wap_global_footer);

 	

 	$ret = get_event_by_event_id($event_id);

 	//IOS临时把二维码图片删除
 	if(stripos($_SERVER['HTTP_USER_AGENT'], 'iphone'))
 	{
 		foreach($ret['other_info_detail'] as $k=>$val){
 			if(preg_match('/二维码/',$val['text']))
 			{
 				unset($ret['other_info_detail'][$k]);
 			}
 		}
 	}

 	foreach($ret['other_info_detail'] as $k=>$val)
 	{
 		$ret['other_info_detail'][$k]['text'] = str_replace("\n","<br />",$val
 	["text"]);
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
 	$act_arrange['remark'] = str_replace("\n",'<br />',$ret['remark']);
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
 		$act_arrange['table_info'][$key]['begin_time'] = date("m.d H:i",$value
 	['begin_time']);
 		$act_arrange['table_info'][$key]['end_time'] = date("m.d H:i",$value
 	['end_time']);
 		$act_arrange['table_info'][$key]['text']  = date("m.d H:i",$value
 	['begin_time']).' - '.date("H:i",$value['end_time']);

 		//检查是否重复报名
 		$is_duplicate =0;

 		if($yue_login_id)
 		{
 			$is_duplicate = check_duplicate($yue_login_id,$event_id,"all", $value
 	['id']);

 			$user_id = get_relate_poco_id($yue_login_id);
 			$enroll_arr = get_enroll_list("user_id={$user_id} and event_id=
 	{$event_id} and table_id=".$value['id'], false);

 			$act_arrange['table_info'][$key]['enroll_id'] = (int)$enroll_arr[0]
 	['enroll_id'];
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

 	if($_INPUT['print'] == 1)
 	{
 		print_r($output_arr);
 		die();
 	}

 	$show_join_btn = 1;
 	if($act_info['event_organizers'] == $yue_login_id)
 	{
 		$show_join_btn = 0;
 	}
 	else if($act_info['event_status'] != 0)
 	{
 		$show_join_btn = 0;
 	}

 	$ret['share_text']['url'] = $ret['share_text']['url_v3'];
 	$share_text = mall_output_format_data($ret['share_text']);

 	//================= App实现聊天功能 =================
 	$chat_json = 'null';
 	if(MALL_UA_IS_YUEYUE == 1)
 	{
 		$sendername = get_user_nickname_by_user_id($yue_login_id);
 		$receivername = get_user_nickname_by_user_id($act_info['event_organizers']);

 		$sendericon = get_user_icon($yue_login_id,165);
 		$receivericon = get_user_icon($act_info['event_organizers'],165);

 		$ret_json = array(
 			'senderid' => $yue_login_id,
 			'receiverid' => $act_info['event_organizers'],
 			'sendername' => $sendername,
 			'receivername' => $receivername,
 			'sendericon' => $sendericon,
 			'receivericon' => $receivericon
 		);		
 		
 		$chat_json = mall_output_format_data($ret_json);
 		
 		
 	}
 	$tpl->assign('is_yueyue_app',MALL_UA_IS_YUEYUE);
 	$tpl->assign('chat_json',$chat_json);
 	$tpl->assign('act_intro',$act_intro);
 	$tpl->assign('act_info',$act_info);
 	$tpl->assign('act_arrange',$act_arrange);
 	$tpl->assign('pub_user_id',$ret['user_id']);
 	$tpl->assign('is_follow',$is_follow);
 	$tpl->assign('user_info',$user_info);
 	$tpl->assign('share_text',$share_text);
 	$tpl->assign('event_id',$event_id);
 	$tpl->assign('show_join_btn',$show_join_btn);
 	$tpl->assign('is_weixin',MALL_UA_IS_WEIXIN);

 	if($_INPUT['print'] == 1)
 	{
 		print_r($ret['share_text']);
 		die();
 	}
 	$tpl->output();


} 
// ========================= 区分pc，wap模板与数据格式整理 end  =======================



?>