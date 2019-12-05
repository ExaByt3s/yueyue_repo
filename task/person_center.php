<?php
/** 
 * 
 * tt
 * ��Բ
 * 2015-4-11
 * 
 */
define("DONT_CHECK_AUTH",1);

/**
 * ������Դ�ļ���λ��ע�⣡ȷ������·����ȡ
 */
$file_dir = dirname(__FILE__);

include_once($file_dir.'/./task_common.inc.php');

global $yue_login_id;

// Ȩ���ļ�
include_once($file_dir.'/./task_auth_common.inc.php');

include_once($file_dir. '/./webcontrol/head.php');
include_once($file_dir. '/./webcontrol/top_nav.php');
include_once($file_dir. '/./webcontrol/footer.php');

$tpl = $my_app_pai->getView('person_center.tpl.htm');

$tpl->assign('time', time());  //�����

// ������ʽ��js����
$pc_global_top = _get_wbc_head();
$tpl->assign('pc_task_top', $pc_global_top);

$pc_global_nav = _get_wbc_top_nav(array('cur_page'=>'order_list'));
$tpl->assign('pc_global_nav', $pc_global_nav);

// �ײ�
$footer_html = _get_wbc_footer();
$tpl->assign('footer_html', $footer_html);

$task_lead_obj = POCO::singleton('pai_task_lead_class');



// �����û�id
$yue_login_id = (int)$_INPUT['user_id'] ? (int)$_INPUT['user_id'] : $yue_login_id ;

// print_r($yue_login_id);

// pai_task_profile_class
/*
 * ��ȡ�����̼��û�ID
 * @return array
 */

$task_seller_obj = POCO::singleton('pai_task_seller_class');
$seller_info = $task_seller_obj->get_seller_info($yue_login_id);
if( empty($seller_info) )
{
	js_pop_msg("�������̼��˺�Ŷ��", false, "http://task.yueus.com/");
}
$service_id = intval($seller_info['service_id']);

/*
$get_all_profile_obj = POCO::singleton('pai_task_profile_class');
$user_arr = $get_all_profile_obj->get_all_profile_user_id();

if (!in_array($yue_login_id,$user_arr)) {
    js_pop_msg("�������̼��˺�Ŷ��", false,"http://www.yueus.com/");
}
// print_r($user_arr);
*/


// �û���Ϣ
$user_icon = get_user_icon($yue_login_id);
$user_nickname = get_user_nickname_by_user_id($yue_login_id);



$tpl->assign('user_icon', $user_icon);
$tpl->assign('user_nickname', $user_nickname);


/*
 * ��ȡ��ǰ�̼���Ϣ
 * @param int $user_id
 * @param int $service_id
 * @return array
 */
$task_profile_obj = POCO::singleton('pai_task_profile_class');
$profile_info = $task_profile_obj->get_profile_info($yue_login_id, $service_id);
$profile_info['city_name'] = get_poco_location_name_by_location_id ( $profile_info ['location_id'] );
$tpl->assign('profile_info', $profile_info);

// print_r($profile_info);



/*
 * ��ȡ�̼�ͼƬ
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
 * ��ȡ��ǰ�̼�������
 * @param bool $b_select_count
 * @param int $user_id
 * 
 * return array|int
 */
$task_review_obj = POCO::singleton('pai_task_review_class');
$comment_num = $task_review_obj->get_user_review_list(true,$yue_login_id);
$tpl->assign('comment_num', $comment_num);



/*
 * ��ȡ��ǰ�̼���������
 * @param int $user_id
 * @param int $service_id
 * @return array
 */
$task_profile_obj = POCO::singleton('pai_task_profile_class');
$profile_info = $task_profile_obj->get_profile_info($yue_login_id, $service_id);
$rank = floor($profile_info['rank']); 

for ($i=0; $i < 5; $i++) { 

    if ( $i < $rank ) 
    {
        $starts[$i]['start'] = 1 ;
    }
    else
    {
        $starts[$i]['start'] = 0 ;
    }
    
}

$tpl->assign('starts', $starts);


/*
 * ��ȡ�̼�FAQ
 * @param int $profile_id �̼�ID
 * @param string $limit 
 * return array
 */
$task_profile_obj = POCO::singleton('pai_task_profile_class');
$faq = $task_profile_obj->get_profile_faq_list($profile_info['profile_id'],$limit='0,1000');
$tpl->assign('faq',$faq);
// print_r($faq);



/*
 * ��ȡ�û������б�
 * @param bool $b_select_count
 * @param int $user_id
 * @param string $limit 
 * 
 * return array
 */
$task_review_obj = POCO::singleton('pai_task_review_class');
$msg_list = $task_review_obj->get_user_review_list($b_select_count = false,$user_id=$yue_login_id ,$limit='0,10');
foreach ($msg_list as $k => $val) {
    for ($i=0; $i < $val['rank'] ; $i++) { 
        $msg_list[$k]['starts_list'][$i]['new_start'] = 1 ;
    }
}
$tpl->assign('msg_list',$msg_list);
// print_r($msg_list);



// ��������ӿ�(��ȡ�����4����¼)
$task_quotes_obj = POCO::singleton('pai_task_quotes_class');
$recently_works = $task_quotes_obj -> get_quotes_history_by_userid($user_id);

$tpl->assign('recently_works',$recently_works);

// print_r($recently_works);

$tpl->output();
 ?>