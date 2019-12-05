<?php 
/*
 * 更新出勤率
 */
include_once ('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$user_obj = POCO::singleton ( 'pai_user_class' );

$sql = "select count(*) as c,enroll_user_id from pai_db.pai_activity_code_tbl group by enroll_user_id order by c desc;";

$code_arr = db_simple_getdata($sql,false,101);

foreach($code_arr as $val){
	$total = $val['c'];
	$enroll_user_id = $val['enroll_user_id'];
	
	$sql = "select count(*) as c from pai_db.pai_activity_code_tbl where is_checked=1 and enroll_user_id={$enroll_user_id}";
	$check_arr = db_simple_getdata($sql,true,101);
	
	$check_count = (int)$check_arr['c'];
	
	$attendance = sprintf("%.2f", $check_count/$total);
	
	$attendance = $attendance*100;
	
	$update_data['attendance'] = $attendance;
	$user_obj->update_user($update_data, $enroll_user_id);

}

$date = date("Y-m-d H:i:s");
echo '更新出勤率成功'.$date;
?>