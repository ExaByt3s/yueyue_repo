<?php 
/*
 * 更新要审核的模特
 */

include_once ('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$user_obj = POCO::singleton('pai_user_class');

$model_list = $user_obj->get_user_list(false, "role='model'", 'user_id DESC', '','user_id,cellphone');



foreach($model_list as $val){
	$user_id = (int)$val['user_id'];
	$cellphone = $val['cellphone'];
	$add_time = time();
	$sql = "insert ignore pai_db.pai_model_audit_tbl set user_id={$user_id},add_time={$add_time},cellphone={$cellphone}";

	db_simple_getdata($sql,false,101);
	
}

$date = date("Y-m-d H:i:s");
echo '更新审核模特成功'.$date;
?>