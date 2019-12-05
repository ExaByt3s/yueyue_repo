<?php

 
include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');
include_once('./mine_head.php');
$tpl = $my_app_pai->getView('mine_deal.tpl.htm');

// 用户信息
$user_icon = get_user_icon($yue_login_id);
$user_nickname = get_user_nickname_by_user_id($yue_login_id);

$tpl->assign('user_icon', $user_icon);
$tpl->assign('user_nickname', $user_nickname);

/*
 * 获取当前商家信息
 * @param int $user_id
 * @param int $service_id
 * @return array
 */
$task_profile_obj = POCO::singleton('pai_task_profile_class');
$profile_info = $task_profile_obj->get_profile_info_by_user_id($yue_login_id);
$tpl->assign('profile_info', $profile_info);

/*
 * 获取用户评价列表
 * @param bool $b_select_count
 * @param int $user_id
 * @param string $limit 
 * 
 * return array
 */
$task_review_obj = POCO::singleton('pai_task_review_class');
$msg_list = $task_review_obj->get_user_review_list($b_select_count = false,$user_id=$yue_login_id ,$limit='0,15');
$rank_count;
foreach ($msg_list as $k => $val) {
	$rank_count = $val['rank'];
    for ($i=0; $i < $val['rank'] ; $i++) { 
        $msg_list[$k]['starts_list'][$i]['yes_start'] = 1 ;
		$rank_count = $i + 1;
    }
	if($rank_count == 0){
		for($j=0; $j < 5;$j++){
			$msg_list[$k]['starts_list'][$j]['no_start'] = 1;
		}
	}
		else{
			for ($j=$rank_count; $j < 5;$j++){
		$msg_list[$k]['starts_list'][$j]['no_start'] = 1;
		
	}
	}	
}
$tpl->assign('msg_list',$msg_list);

$tpl->assign('time', time());  //随机数


$tpl->output();
 ?>