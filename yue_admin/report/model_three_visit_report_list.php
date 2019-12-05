<?php

/**
 *@desc 三天点击次数 
 * @authors xiao xiao (xiaojm@yueus.com)
 * @date    2015年4月20日
 * @version 1
 */
 
 include_once '/disk/data/htdocs232/poco/pai/yue_admin/audit/include/Classes/PHPExcel.php';
 include('common.inc.php');
 include_once("/disk/data/htdocs232/poco/pai/yue_admin/common/yue_function.php");
 //3天排行榜类
 
 $model_three_visit_obj = POCO::singleton('pai_model_three_visit_report_class');
 $user_obj                = POCO::singleton('pai_user_class');
 
 $tpl   = new SmartTemplate("model_three_visit_report_list.tpl.htm");
 $date        = $_INPUT['date'] ? trim($_INPUT['date']) : date('Y-m-d',time()-24*3600);
 $location_id = $_INPUT['location_id'] ? intval($_INPUT['location_id']) : 101029001;
 $act   = trim($_INPUT['act']);
 
 //条件
 $setParam = array();
 $where_str = '';
 
 if(strlen($date) > 0)  $setParam['date'] = $date;
 if(strlen($act) >0)    $setParam['act']  = $act;
 //地区
 if(strlen($location_id) > 0)
 {
 	$setParam['location_id'] = $location_id;
 }
 
 $list = $model_three_visit_obj->get_three_visit_list($date,false,$location_id,$where_str,'count_visit DESC,user_id DESC','0,30','*');
 //get_three_visit_list();
 if(!is_array($list)) $list = array();
 
 $sql_tmp_str = '';
 foreach ($list as $key=>$val)
 {
 	if($key != 0) $sql_tmp_str .= ',';
 	$sql_tmp_str .= $val['user_id'];
 }
 
 if(strlen($sql_tmp_str) >0)
 {
 	$sql_in_str = "user_id IN ({$sql_tmp_str})";
 	$user_ret = $user_obj->get_user_list(false, $sql_in_str, 'user_id DESC', "0,30",'user_id,nickname');
 	if(is_array($user_ret)) $list = combine_arr($list, $user_ret, 'user_id');
 }
 
 if(!is_array($list)) $list = array();
 
 //导出数据
 if($act == 'export')
 {
 	$data = array();
 	foreach ($list as $key=>$vo)
 	{
 		$data[$key]['user_id']        = $vo['user_id'];
 		$data[$key]['nickname']       = $vo['nickname'];
 		$data[$key]['count_visit']    = $vo['count_visit'];
 	}
 	$fileName = '模特3天被点击的次数';
 	$title    = '模特3天被点击的次数';
 	$headArr  = array("模特ID","模特昵称","模特被点击次数");
 	getExcel($fileName,$title,$headArr,$data);
 	exit;
 }
 
 $tpl->assign($setParam);
 $tpl->assign('list', $list);
 $tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
 
 $tpl->output();
 
 ?>