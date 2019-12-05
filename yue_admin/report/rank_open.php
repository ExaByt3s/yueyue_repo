<?php
/**
 *
 * 信息打开榜
 * @authors xiao xiao (xiaojm@yueus.com)
 * @date    2015-03-09 15:40:08
 * @version 1
 */

  include_once '/disk/data/htdocs232/poco/pai/yue_admin/audit/include/Classes/PHPExcel.php';
  include('common.inc.php');
  include('include/common_function.php');
  $response_open_obj = POCO::singleton('pai_response_open_day_class');
  $page_obj               = new show_page ();
  $show_count             = 50;
  $act                    = $_INPUT['act'] ? $_INPUT['act'] : 'list';
  $tpl = new SmartTemplate("rank_open.tpl.htm");
  $timet        = mktime(0,0,0,date('m'),date('d'),date('Y'));
  $begin_time   = date('Y-m-d',$timet - 8*24*3600);
  $end_time     = date('Y-m-d',$timet - 2*24*3600);
  $tablename = $response_open_obj->get_tablename_by_month($begin_time);
  if(!$tablename){echo "该月份暂未出数据";exit;}
  $where_str_count = "user_id>100000 AND add_time BETWEEN '{$begin_time}' AND '{$end_time}'";
  $total_count = $response_open_obj->get_open_list_v2($tablename ,true, $where_str_count, '', '', 'distinct(user_id)');
  $page_obj->set ( $show_count, $total_count );
  $where_str = "user_id>100000 AND add_time BETWEEN '{$begin_time}' AND '{$end_time}' GROUP BY user_id desc";
  if ($act == 'list') 
  {
  	  $list = $response_open_obj->get_open_list_v2($tablename ,false, $where_str, 'open_count DESC,person_count DESC,system_count DESC', $page_obj->limit(), 'user_id,SUM(open_count) as open_count,SUM(person_count) as person_count,SUM(system_count) as system_count');
  }
  //导出
  elseif ($act == 'export') 
  {
  	$list = $response_open_obj->get_open_list_v2($tablename ,false, $where_str, 'open_count DESC,person_count DESC,system_count DESC', "0,{$total_count}", 'user_id,SUM(open_count) as open_count,SUM(person_count) as person_count,SUM(system_count) as system_count');
  	$fileName = "信息打开榜报表";
    $title    = "信息打开榜报表";
    $headArr = array('用户ID', '总条数', '用户信息', '系统信息');
    getExcel($fileName,$title,$headArr,$list,$ret,$ret_avg);
    exit;
  	# code...
  }
  $tpl->assign('begin_time', $begin_time);
  $tpl->assign('end_time', $end_time);
  $tpl->assign('list', $list);
  $tpl->assign ( "page", $page_obj->output ( 1 ) );
  $tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
  $tpl->output();
  
  ?>