<?php
/**
 * �޸ĺ�ɾ������
 * @author hudw 
 * 2015.10.15
 */

include_once 'config.php';

// ���ղ���

$topic_id = intval($_INPUT['topic_id']);

if(empty($yue_login_id))
{
	$output_arr['code'] = 0;
	$output_arr['msg'] = '�Ƿ���¼';
	mall_mobile_output($output_arr,false);
	die();
}
elseif(empty($topic_id))
{
	$output_arr['code'] = 0;
	$output_arr['msg'] = 'ȱ�ٻID';
	mall_mobile_output($output_arr,false);
	die();
}

$mall_topic_obj = POCO::singleton('pai_topic_class');

$ret = $mall_topic_obj->delete_promotion_user_enroll($topic_id,$yue_login_id);

$output_arr['code'] = $ret['result']==1 ? 1 :0;
$output_arr['msg'] = $ret['message'];
mall_mobile_output($output_arr,false);

?>