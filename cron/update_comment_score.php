<?php 
/*
 * 更新评论平均分
 */

include_once ('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$comment_score_rank_obj = POCO::singleton('pai_comment_score_rank_class');

$sql = "select cameraman_user_id,sum(overall_score) as sum_score,count(*) as num from pai_db.pai_cameraman_comment_log_tbl group by cameraman_user_id";

$cameraman_comment_arr = db_simple_getdata($sql,false,101);

foreach($cameraman_comment_arr as $val){
	$user_id = $val['cameraman_user_id'];
	$sum_score = round($val['sum_score']/$val['num'],1);

	$sql = "select location_id,role from pai_db.pai_user_tbl where user_id={$user_id}";
	$user_info = db_simple_getdata($sql,true,101);
	$location_id = $user_info['location_id'];
	$role = $user_info['role'];


	$sql = "replace into pai_db.pai_comment_score_rank_tbl set num='{$sum_score}', user_id={$user_id}, location_id={$location_id},role='{$role}'";
	db_simple_getdata($sql,false,101);
	
	$cache_key = $comment_score_rank_obj->get_comment_rank_cache_key($user_id);
	POCO::deleteCache ( $cache_key ); //清缓存
}


$sql = "select model_user_id,sum(overall_score) as sum_score,count(*) as num from pai_db.pai_model_comment_log_tbl group by model_user_id;";
$model_comment_arr = db_simple_getdata($sql,false,101);

foreach($model_comment_arr as $val){
	$user_id = $val['model_user_id'];
	$sum_score = round($val['sum_score']/$val['num'],1);

	$sql = "select location_id,role from pai_db.pai_user_tbl where user_id={$user_id}";
	$user_info = db_simple_getdata($sql,true,101);
	$location_id = (int)$user_info['location_id'];
	$role = $user_info['role'];
	
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

	$sql = "replace into pai_db.pai_comment_score_rank_tbl set num='{$sum_score}', user_id={$user_id}, location_id={$location_id} ,role='{$role}',is_bad_list={$is_bad_list}";
	db_simple_getdata($sql,false,101);
	
	$cache_key = $comment_score_rank_obj->get_comment_rank_cache_key($user_id);
	POCO::deleteCache ( $cache_key ); //清缓存
}

$date = date("Y-m-d H:i:s");
echo '更新评论平均分成功'.$date;
?>