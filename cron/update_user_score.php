<?php
/*
 * 更新用户积分排名
 */

include_once ('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');

$sql = "select * from pai_score_db.pai_user_score_tbl where recently_score!=0;";
$score_arr = db_simple_getdata ( $sql, false, 101 );

foreach ( $score_arr as $val )
{
	$user_id = $val ['user_id'];
	$recently_score = $val ['recently_score'];
	$level = $val ['level'];
	
	$sql = "select location_id,role from pai_db.pai_user_tbl where user_id={$user_id}";
	$user_info = db_simple_getdata ( $sql, true, 101 );
	
	$location_id = ( int ) $user_info ['location_id'];
	$role = $user_info ['role'];
	
	if ($role == 'model')
	{
		$sql = "select count(*) as num from pai_db.pai_model_audit_tbl where user_id={$user_id} and is_approval=1";
		$is_approval = db_simple_getdata ( $sql, true, 101 );
		if ($is_approval['num'])
		{
			$is_bad_list = 0;
		}
		else
		{
			$is_bad_list = 1;
		}
		
		$sql = "replace into pai_db.pai_score_rank_tbl set user_id={$user_id}, score={$recently_score}, level={$level}, location_id={$location_id},is_bad_list={$is_bad_list}";
		db_simple_getdata ( $sql, false, 101 );
		
		$cache_key = "YUEYUE_INTERFACE_SCORE_RANK__" . $user_id;
		POCO::deleteCache ( $cache_key ); //清缓存
	}

}

$date = date ( "Y-m-d H:i:s" );
echo '约拍积分排名更新成功' . $date;

?>