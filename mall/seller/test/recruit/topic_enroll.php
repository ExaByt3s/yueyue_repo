<?php
include_once ('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');

$topic_enroll_obj = POCO::singleton ( 'pai_topic_enroll_class' );

$topic_id = (int)$_INPUT['topic_id'];

if(!$yue_login_id)
{
	$msg = "请先登录";
	$json_arr['code'] = -1;
	$json_arr['msg'] = iconv('gbk','utf-8',$msg);
	echo json_encode($json_arr);
	exit;
}


$check_enroll = $topic_enroll_obj->check_topic_enroll($topic_id,$yue_login_id);


if($check_enroll)
{
	$code = -2;
	$msg = '已报名';
}
else
{
	$insert_data['topic_id'] = $topic_id;
	$insert_data['user_id'] = $yue_login_id;
	$insert_data['add_time'] = time();
	
	if($yue_login_id)
	{
		$topic_enroll_obj->add_topic_enroll($insert_data);
	}

	$code = 1;
	$msg = '报名成功';
}

$json_arr['code'] = $code;
$json_arr['msg'] = iconv('gbk','utf-8',$msg);

echo json_encode($json_arr);

?>