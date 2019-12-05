<?php

/**
 * 获取单模特订单数据
 * @authors xiao xiao (xiaojm@yueus.com)
 * @date    2015年4月14日
 * @version 1
 */
 include ('common.inc.php');
 include_once("/disk/data/htdocs232/poco/pai/yue_admin/common/yue_function.php");
 $user_obj  = POCO::singleton('pai_user_class');
 
 $tpl = new SmartTemplate("model_realtime_report_detail.tpl.htm");
 //分页类
 $page_obj   = new show_page();
 $show_count = 30;
 
 $user_id = trim($_INPUT['user_id']);
 if($user_id < 1)
 {
 	js_pop_msg('非法操作!');
 }
 
 $page_obj->setvar(array('user_id' => $user_id));
 
 $sql_str_count = "SELECT count(*) as c FROM event_db.event_date_tbl  WHERE to_date_id = {$user_id}";
 $ret1 = db_simple_getdata($sql_str_count, true);
 $total_count = $ret1['c'];
 
 $page_obj->set($show_count, $total_count);
 

 $sql_str = "SELECT dat.*,det.event_status FROM event_db.event_date_tbl dat LEFT JOIN event_db.event_details_tbl det on dat.event_id=det.event_id WHERE dat.to_date_id = {$user_id} ORDER BY det.event_status DESC,dat.date_time DESC,dat.date_id DESC  limit {$page_obj->limit()}";
 $ret = db_simple_getdata($sql_str, false);

 if(!is_array($ret)) $ret = array();
 
 $where_tmp_str = '';
 foreach ($ret as $key=> $val)
 {
 	if($key != 0) $where_tmp_str .= ',';
 	$where_tmp_str .= $val['from_date_id'];
 	$ret[$key]['date_time'] = date('Y-m-d H:i:s', $val['date_time']);
 }
 
 if(strlen($where_tmp_str) >0)
 {
 	$where_in_str = "user_id in ({$where_tmp_str})";
 	//echo $where_in_str;
 	$user_ret = $user_obj->get_user_list(false, $where_in_str, 'user_id DESC', "0,99999999",'user_id as from_date_id ,nickname');
 	//print_r($user_ret);
 	if(is_array($user_ret)) $ret = combine_arr2($ret, $user_ret, 'from_date_id');
 }
 
 
 $tpl->assign('list', $ret);
 $tpl->assign ( "page", $page_obj->output ( 1 ) );
 $tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
 $tpl->output();
   
 
 
 
 ?>