<?php
/**
 * 修改和删除报名
 * @author hudw 
 * 2015.10.15
 */

include_once 'config.php';

// 接收参数

$topic_id = intval($_INPUT['topic_id']);
$goods_id = intval($_INPUT['goods_id']);
$type_key = intval($_INPUT['type_key']);
$num	  = intval($_INPUT['num']);
$action   = trim($_INPUT['action']);
$price_text   = trim($_INPUT['price_text']);


$mall_topic_obj = POCO::singleton('pai_topic_class');

$price_text = mb_convert_encoding($price_text, 'GBK','UTF-8');

switch ($action) 
{
	case 'update':

		$ret = $mall_topic_obj->add_promotion_enroll($topic_id,$yue_login_id,$goods_id,$type_key,$num,$price_text);

		break;
	case 'delete':
		$ret = $mall_topic_obj->delete_promotion_enroll($topic_id,$yue_login_id,$goods_id,$type_key);

		break;
	default:
		# code...
		break;
}

$output_arr['code'] = $ret['result']==1 ? 1 :0;
$output_arr['msg'] = $ret['message'];
mall_mobile_output($output_arr,false);

?>