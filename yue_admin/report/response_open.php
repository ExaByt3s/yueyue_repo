<?php

/**
 * 
 * @authors xiaoxiao (xiaojm@yueus.com)
 * @date    2015-02-26 09:18:52
 * @version 1
 *
 * 信息查看报表
 */
  include_once '/disk/data/htdocs232/poco/pai/yue_admin/audit/include/Classes/PHPExcel.php';
  include('common.inc.php');
  include('include/common_function.php');
  $response_open_obj = POCO::singleton('pai_response_open_day_class');
  $page_obj               = new show_page ();
  $show_count             = 50;
  $act                    = $_INPUT['act'] ? $_INPUT['act'] : 'list';
  $tpl = new SmartTemplate("response_open_list.tpl.htm");

  $month          = $_INPUT['month'] ? $_INPUT['month'] : date('Y-m', time()-24*3600);
  //$month = "2015-02";
  $tablename = $response_open_obj->get_tablename_by_month($month);
  if(!$tablename){echo "该月份暂未出数据";exit;}
  $where_str = "user_id>100000 GROUP BY add_time";
  $list = $response_open_obj->get_open_list_v2($tablename ,false, $where_str, 'add_time DESC,open_count DESC', '0,99999999', 'add_time,SUM(open_count) as open_count,SUM(person_count) as person_count,SUM(system_count) as system_count');
  if (!is_array($list) || empty($list)) {return false;}

  //列表
  if ($act == 'list') 
  {
  	$ret = array();
  	$open_count_up     = 0;
  	$person_count_up   = 0;
  	$system_count_up   = 0;
  	$i = 0;
  	foreach ($list as $key => $vo) 
  	{
  		$list[$key]['add_time'] = date('m月d日', strtotime($vo['add_time']));
      $open_count_up    += $vo['open_count'];
      $person_count_up  += $vo['person_count'];
      $system_count_up  += $vo['system_count'];
      $i ++ ;
  	}
    $ret['open_count_up']           = $open_count_up;
    $ret['person_count_up']         = $person_count_up;
    $ret['system_count_up']         = $system_count_up;
	  //日均
	  $ret['open_count_day']          = sprintf('%.2f',$open_count_up/$i);
	  $ret['person_count_day']        = sprintf('%.2f',$person_count_up/$i);
	  $ret['system_count_day']        = sprintf('%.2f',$system_count_up/$i);
  }
  elseif ($act == 'export') 
  {
    $ret = array();
    $open_count_up     = 0;
    $person_count_up   = 0;
    $system_count_up   = 0;
    $i = 0;
    foreach ($list as $key => $vo) 
    {
      $list[$key]['add_time'] = date('m月d日', strtotime($vo['add_time']));
      $open_count_up    += $vo['open_count'];
      $person_count_up  += $vo['person_count'];
      $system_count_up  += $vo['system_count'];
      $i ++ ;
    }
    $ret['open_count_up']           = $open_count_up;
    $ret['person_count_up']         = $person_count_up;
    $ret['system_count_up']         = $system_count_up;
    //日均
    $ret_avg['open_count_day']          = sprintf('%.2f',$open_count_up/$i);
    $ret_avg['person_count_day']        = sprintf('%.2f',$person_count_up/$i);
    $ret_avg['system_count_day']        = sprintf('%.2f',$system_count_up/$i);
  	$fileName = "信息查看报表";
    $title    = "信息查看报表";
    $headArr = array('日期', '总条数', '用户信息', '系统信息');
    getExcel($fileName,$title,$headArr,$list,$ret,$ret_avg);
    exit;
  }
  
  $tpl->assign('list', $list);
  $tpl->assign('month', $month);
  $tpl->assign($ret);
  $tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
  $tpl->output();