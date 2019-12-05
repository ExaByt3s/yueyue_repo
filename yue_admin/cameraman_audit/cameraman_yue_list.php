<?php 

/**
 * 摄影师约拍信息json报表
 * @authors xiao xiao (xiaojm@yueus.com)
 * @date    2015年4月27日
 * @version 2
 */

	include_once 'common.inc.php';
	include_once ("/disk/data/htdocs232/poco/pai/yue_admin/common/yue_function.php");
	
	//用户表
 	$user_obj  = POCO::singleton('pai_user_class');
    //模特对摄影师的评价类
    $cameraman_comment_log_obj = POCO::singleton('pai_cameraman_comment_log_class');
    
    $act     = trim($_INPUT['act']);
    $user_id = intval($_INPUT['user_id']);
    
    $limit = '';
    //判断获取多少条
    if($act == 'more')
    {
    	$limit = "0,99999999";
    }
    else
    {
    	$limit = "0,3";
    }
    
    //约拍信息
    $sql_str = "SELECT dat.date_time,dat.date_id,dat.date_style,dat.to_date_id,dat.date_style,det.budget FROM event_db.event_date_tbl dat,event_db.event_details_tbl det WHERE dat.from_date_id= {$user_id} AND dat.event_id = det.event_id AND dat.date_status='confirm' AND det.event_status='2' ORDER BY date_time DESC,to_date_id DESC LIMIT {$limit} ";
    $date_ret = db_simple_getdata($sql_str,false);
    if(!is_array($date_ret)) $date_ret = array();
    //获取评价
    $date_tmp_str = '';
    $nickname_tmp_str = '';
    foreach ($date_ret as $key=>$date_val)
    {
    	if($key !=0)
    	{
    		$date_tmp_str .= ',';
    		$nickname_tmp_str .= ',';
    	}
    	$date_ret[$key]['date_time'] = date('Y-m-d H:i:s',$date_val['date_time']);
    	$date_tmp_str .= $date_val['date_id'];
    	$nickname_tmp_str .= $date_val['to_date_id'];
    }
    
    //获取评价
    if(strlen($date_tmp_str) >0)
    {
    	$date_sql_str = "date_id IN ({$date_tmp_str})";
    	$comment_ret = $cameraman_comment_log_obj->get_comment_list(false,$date_sql_str,'id DESC','0,99999999','date_id,overall_score');
    	//print_r($comment_ret);
    	if(is_array($comment_ret)) $date_ret = combine_arr2($date_ret, $comment_ret, 'date_id');
    }
    //获取昵称
    if(strlen($nickname_tmp_str) >0)
    {
    	$nickname_sql_str = "user_id IN ({$nickname_tmp_str})";
    	$nickname_ret = $user_obj->get_user_list(false, $nickname_sql_str,'user_id DESC','0,10','user_id AS to_date_id,nickname');
    	if(is_array($nickname_ret)) $date_ret = combine_arr2($date_ret, $nickname_ret, 'to_date_id');
    }
    
    //转格式
    foreach ($date_ret as $key=>$val)
    {
    	$date_ret[$key] = gbk_to_utf8($val);
    }
    
    $arr  = array
    (
    	'msg'  => 'success',
    	'list' =>  $date_ret
    );
    echo json_encode($arr);
    
 ?>