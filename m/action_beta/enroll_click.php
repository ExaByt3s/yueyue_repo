<?php
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

if(!$yue_login_id)
{
	die('no login');
}

$topic_id = (int)$_INPUT['topic_id'];
$add_time = time();

$sql = "insert ignore into pai_db.pai_enroll_click_log_tbl set user_id={$yue_login_id},topic_id={$topic_id},add_time={$add_time}";
db_simple_getdata($sql,false,101);

?>
<script>alert('您已报名，工作人员将电话与您联系')</script>