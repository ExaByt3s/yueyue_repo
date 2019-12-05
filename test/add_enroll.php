<?php
include_once ('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');


/*$enroll_obj = POCO::singleton ( 'event_enroll_class' );

$sql = "select user_id from topic_admin_db.cmt_bot_silent_user_lib_tbl where new_check=1 order by rand() limit 1;";
$mj_arr = db_simple_getdata($sql,true,20);

$user_id= $mj_arr['user_id'];

$data ['user_id'] = $user_id;
$data ['event_id'] = 47845;
$data ['enroll_time'] = time();
$data ['enroll_ip'] = '';
$data ['enroll_num'] = 1;
$data ['status'] = 0;
$data ['table_id'] = 3549;

echo $enroll_obj->add_enroll ( $data );*/

$user_obj = POCO::singleton ( 'pai_user_class' );
$relate_obj = POCO::singleton ( 'pai_relate_poco_class' );

$sql = "select user_id,user_name from topic_admin_db.cmt_bot_silent_user_lib_tbl where new_check=1";
$mj_arr = db_simple_getdata($sql,false,20);

foreach($mj_arr as $val)
{
	$user_info_arr ['cellphone'] = "123".rand(11111111,99999999);
	$user_info_arr ['nickname'] = $val['user_name'];
	$user_info_arr ['sex'] = "Фа";
	$user_info_arr ['pwd'] = "yueus123456";
	$user_info_arr ['poco_id'] = $val['user_id'];
		
	$relate_yue_id = $relate_obj->get_relate_yue_id ( $val['user_id'] );
	if(!$relate_yue_id)
	{
		echo $user_obj->create_account_by_pc($user_info_arr, $err_msg);
	}
}



?>