<?php

 
include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');

include_once('./mine_head.php');

$tpl = $my_app_pai->getView('mine.tpl.htm');


$task_seller_obj = POCO::singleton('pai_task_seller_class');
$seller_info = $task_seller_obj->get_seller_info($yue_login_id);
$service_id = intval($seller_info['service_id']);

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
$profile_info = $task_profile_obj->get_profile_info($yue_login_id, $service_id);
/**
$rank_count = (int)$profile_info['rank'];
for($i = 0; $i < $rank_count ; $i++)
{
	$profile_info['starts_list'][$i]['yes_start'] = 1 ;
	$rank_count = $i + 1;
}
if($rank_count == 0){
		for($j=0; $j < 5;$j++){
			$profile_info['starts_list'][$j]['no_start'] = 1;
		}
	}
else{
			for ($j=$rank_count; $j < 5;$j++){
		$profile_info['starts_list'][$j]['no_start'] = 1;
		
	}
}
**/

// modify by hudw
/*
 * 获取当前商家评价星星
 * @param int $user_id
 * @param int $service_id
 * @return array
 */
$rank = floor($profile_info['rank']); 

for ($i=0; $i < 5; $i++) { 

    if ( $i < $rank ) 
    {
        $starts[$i]['yes_start'] = 1 ;
    }
    else
    {
       $starts[$i]['no_start'] = 1 ;
    }
    
}

$profile_info['starts_list'] = $starts;


$tpl->assign('profile_info', $profile_info);





/*
 * 获取商家图片
 * @param int $profile_id
 * @param string $limit
 * 
 * return array
 */
$task_profile_img_obj = POCO::singleton('pai_task_profile_img_class');
$pic_arr = $task_profile_img_obj->get_profile_pic($profile_info['profile_id']);
$tpl->assign('pic_arr', $pic_arr);
// print_r($pic_arr);



/*
 * 获取当前商家评论数
 * @param bool $b_select_count
 * @param int $user_id
 * 
 * return array|int
 */
$task_review_obj = POCO::singleton('pai_task_review_class');
$comment_num = $task_review_obj->get_user_review_list(true,$yue_login_id);
$tpl->assign('comment_num', $comment_num);



/*
 * 获取当前商家评价星星
 * @param int $user_id
 * @param int $service_id
 * @return array
 */
$task_profile_obj = POCO::singleton('pai_task_profile_class');
$profile_info = $task_profile_obj->get_profile_info($yue_login_id, $service_id);
$rank = floor($profile_info['rank']); 

for ($i=0; $i < $rank; $i++) { 
    $starts[$i]['start'] = 1 ;
}
$tpl->assign('starts', $starts);
// print_r($starts);


/*
 * 获取商家FAQ
 * @param int $profile_id 商家ID
 * @param string $limit 
 * return array
 */
$task_profile_obj = POCO::singleton('pai_task_profile_class');
$faq = $task_profile_obj->get_profile_faq_list(1,$limit='0,1');
$tpl->assign('faq',$faq);
// print_r($faq);



/*
 * 获取用户评价列表
 * @param bool $b_select_count
 * @param int $user_id
 * @param string $limit 
 * 
 * return array
 */
$task_review_obj = POCO::singleton('pai_task_review_class');
$msg_list = $task_review_obj->get_user_review_list($b_select_count = false,$user_id=$yue_login_id ,$limit='0,1');
$rank_count;
foreach ($msg_list as $k => $val) {
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
// print_r($msg_list);

/*
* 近期工作记录
*
*/
$task_quotes_obj = POCO::singleton('pai_task_quotes_class');
$resent = $task_quotes_obj -> get_quotes_history_by_userid($yue_login_id);
foreach ($resent as $k => $val)
{ 
	$val['pay_time'] = strtotime($val['pay_time']);
}
$tpl->assign('resent', $resent);
$tpl->assign('time', time());  //随机数
$tpl ->assign("rand",date("YmdHis"));
$tpl->assign('user_id', $user_id);
$tpl->output();
 ?>