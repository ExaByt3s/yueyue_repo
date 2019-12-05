<?php
/**
 *
 * 用户登录报表
 * @authors xiao xiao (xiaojm@yueus.com)
 * @date    2015-03-10 16:06:04
 * @version 1
 */
  include_once '/disk/data/htdocs232/poco/pai/yue_admin/audit/include/Classes/PHPExcel.php';
  include('common.inc.php');
  include('include/common_function.php');
  $loginlog_report_obj = POCO::singleton('pai_loginlog_report_class');
  $tpl = new SmartTemplate("loginlog_report.tpl.htm");
  $page_obj           = new show_page ();
  $show_count         = 50;
  $act                = $_INPUT['act'] ? $_INPUT['act'] : 'list';
  $timet        = mktime(0,0,0,date('m'),date('d'),date('Y'));
  $begin_time   = date('Y-m-d',$timet - 7*24*3600);
  $end_time     = date('Y-m-d',$timet - 1*24*3600);
  $tablename = $loginlog_report_obj->get_tablename_by_month($begin_time);
  $where_str_count = "date_time >= '{$begin_time}' AND date_time <= '{$end_time}'";
  $total_count = $loginlog_report_obj->get_login_list($tablename, true, $where_str_count, '', '','distinct(user_id)');
  $where_str = "date_time >= '{$begin_time}' AND date_time <= '{$end_time}' GROUP BY user_id desc";
  //列表
  if ($act == 'list') 
  {
  	 $page_obj->setvar ();
  	 $page_obj->set ( $show_count, $total_count );

  	 $list = $loginlog_report_obj->get_login_list($tablename, false, $where_str, 'num DESC,user_id DESC', $page_obj->limit(), 'user_id,SUM(num) AS num');
  }
  //导出
  elseif($act == 'export') 
  {
  	  $list = $loginlog_report_obj->get_login_list($tablename, false, $where_str, 'num DESC,user_id DESC', '0,{$total_count}', 'user_id,SUM(num) AS num');
  	 $fileName = "{$begin_time}到{$end_time}用户登录榜报表";
     $title    = "用户登录榜报表";
     $headArr = array('用户ID', '登录次数');
     getExcel($fileName,$title,$headArr,$list);
     exit;
  }
  $tpl->assign('begin_time', $begin_time);
  $tpl->assign('end_time', $end_time);
  $tpl->assign('list', $list);
  $tpl->assign ( "page", $page_obj->output ( 1 ) );
  $tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
  $tpl->output();
