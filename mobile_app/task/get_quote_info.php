<?php
/**
 * ���Ͽ�ҳ
 * @author rong
 * @copyright 2015-04-09
 */
define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

$user_obj = POCO::singleton ( 'pai_user_class' );
$task_profile_obj = POCO::singleton ( 'pai_task_profile_class' );
$task_quotes_obj = POCO::singleton('pai_task_quotes_class');
$task_review_obj = POCO::singleton('pai_task_review_class');
$task_profile_img_obj = POCO::singleton('pai_task_profile_img_class');
$task_request_obj = POCO::singleton('pai_task_request_class');
$task_message_obj = POCO::singleton('pai_task_message_class');

//��ȡ����
$user_id = $client_data['data']['param']['user_id'];
$quote_id = $client_data['data']['param']['quote_id'];



$quote_info = $task_quotes_obj->get_quotes_detail_info_by_id($quote_id);

$request_info = $task_request_obj->get_request_detail_info_by_id($quote_info['request_id']);

$profile_info = $task_profile_obj->get_profile_info_by_id($quote_info['profile_id']);

if($quote_info['status']==1)
{
	$is_hire = 1;
}
else
{
	$is_hire = 0;
}

$data = array();
$data['user_icon'] = get_user_icon($quote_info['user_id']);
$data['nickname'] = get_user_nickname_by_user_id($quote_info['user_id']);
$data['review_star'] = floor($profile_info['rank']);
$data['is_hire'] = $is_hire;
$data['price'] = $quote_info['price'];
$data['is_vip'] = $quote_info['is_vip'];
$data['status_code'] = $request_info['status_code']; //�ڶ��ֲ���ʱ��ȥ����ֵ
$data['service_intro'] = "����۸����";
$data['service_intro_url'] = "";

//���Ͻǰ�ť
$btn_type_tmp = '';
$status_code_tmp = trim($request_info['status_code']);
if( in_array($status_code_tmp, array('introduced', 'quoted')) )
{
	//��Ӷ
	$btn_type_tmp = 'hire';
}
elseif( $status_code_tmp=='paid' && $quote_info['status']==1 )
{
	//����
	$btn_type_tmp = 'review';
}
$data['btn_type'] = $btn_type_tmp;

/**
 * �����Ѳ鿴����
 * @param int $quotes_id
 * @return bool
 */
$task_quotes_obj->read_quotes($quote_id);


/**
 * �����Ѳ鿴��������
 * @param int $quotes_id
 * @return bool
 */
$task_quotes_obj->read_profile($quote_id);



$message_arr = $task_message_obj->get_message_list_by_user_id($user_id, $quote_id, false, 'message_id ASC', '0,2000');

foreach ($message_arr as $k=>$val)
{
	if($val['from_user_id']==$quote_info['user_id'])
	{
		$message_arr[$k]['direction'] = "left";
	}
	else
	{
		$message_arr[$k]['direction'] = "right";
	}
	
	if($val['is_read']==0 && $val['to_user_id']==$user_id)
	{
		$task_message_obj->update_message_read($val['message_id'], array('read_time'=>time()));
	}
	
	$message_arr[$k]['message_content'] = htmlspecialchars_decode($val["message_content"]);
	
	unset($message_arr[$k]['message_id']);
	unset($message_arr[$k]['message_type']);
	unset($message_arr[$k]['quotes_id']);
	unset($message_arr[$k]['request_id']);
	unset($message_arr[$k]['from_user_id']);
	unset($message_arr[$k]['to_user_id']);
	unset($message_arr[$k]['quotes_content']);
	unset($message_arr[$k]['is_read']);
	unset($message_arr[$k]['read_time']);
	unset($message_arr[$k]['add_time']);
}

if(!$message_arr)
{
	$message_arr = array();
}

$data['message_arr'] = $message_arr;

$data['name'] = $profile_info['title'];
$data['experience'] = $profile_info['experience'];
$data['address'] = $profile_info['address'];
$data['cellphone'] = $profile_info['cellphone'];
$data['website'] = $profile_info['website'];
$data['profile_id'] = $profile_info['profile_id'];
$data['seller_user_id'] = $profile_info['user_id'];

$review_list = $task_review_obj->get_user_review_list(false,$quote_info['user_id'],'0,1');
$count_review = $task_review_obj->get_user_review_list(true,$quote_info['user_id']);
unset($review_list[0]['review_id']);
unset($review_list[0]['quotes_id']);
unset($review_list[0]['request_id']);
unset($review_list[0]['from_user_id']);
unset($review_list[0]['to_user_id']);


$data['review'] = $review_list[0] ? $review_list[0] :array();


if($count_review>1)
{
	$data['review_text'] = "�鿴����{$count_review}������";
}
else
{
	$data['review_text'] = "";
}

$data['order_count_text'] = "����{$count_review}�ν���";
$data['pic_text'] = "��Ƭ";
$pic_arr = $task_profile_img_obj->get_profile_pic($profile_info['profile_id']);
foreach($pic_arr as $k=>$val)
{
	unset($pic_arr[$k]['id']);
	unset($pic_arr[$k]['profile_id']);
	unset($pic_arr[$k]['add_time']);
	$pic_arr[$k]['big_img'] = yueyue_resize_act_img_url($val['img']);
}

$data['pic_arr'] = $pic_arr ? $pic_arr : array();

$data['intro_text'] = "�ҵĽ���";
$data['intro'] = $profile_info['bio_content'];

$data['faq_text'] = "����������";
$faq_list = $task_profile_obj->get_profile_faq_list($profile_info['profile_id'],'0,1');
unset($faq_list[0]['id']);
unset($faq_list[0]['profile_id']);
unset($faq_list[0]['faq_id']);
unset($faq_list[0]['add_time']);
$faq_info = $faq_list[0];


$data['faq'] = $faq_info ? $faq_info :array();
$data['mid'] = '122OD04004'; //ģ��ID

$options['data'] = $data;
$cp->output($options);
