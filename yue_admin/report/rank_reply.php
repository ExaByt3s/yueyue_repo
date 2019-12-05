<?php
/**
 * 回复响应榜
 * @authors xiao xiao (xiaojm@yueus.com)
 * @date    2015-03-09 14:19:25
 * @version 1
 */

include_once '/disk/data/htdocs232/poco/pai/yue_admin/audit/include/Classes/PHPExcel.php';
include('common.inc.php');
include('include/common_function.php');
$response_reply_obj = POCO::singleton('pai_response_reply_day_class');
$tpl = new SmartTemplate("rank_reply.tpl.htm");
$page_obj           = new show_page ();
$show_count         = 50;
$act                = $_INPUT['act'] ? $_INPUT['act'] : 'list';
$timet        = mktime(0,0,0,date('m'),date('d'),date('Y'));
$begin_time   = date('Y-m-d',$timet - 8*24*3600);
$end_time     = date('Y-m-d',$timet - 2*24*3600);
$tablename = $response_reply_obj->get_tablename_by_month($begin_time);
if(!$tablename){echo "该月份暂未出数据";exit;}
//条数查询条件
$where_str_count = "user_id>100000 AND add_time BETWEEN '{$begin_time}' AND '{$end_time}'";
$total_count = $response_reply_obj->get_replay_list_v2($tablename,true, $where_str_count, '', '', 'distinct(user_id)');
$page_obj->set ( $show_count, $total_count );
//列表查询条件
$where_str = "user_id>100000 AND add_time BETWEEN '{$begin_time}' AND '{$end_time}' GROUP BY user_id desc";
//if (!is_array($list) || empty($list)) {return false;}
if($act == 'list')
{
	$list = $response_reply_obj->get_replay_list_v2($tablename,false, $where_str, '5i DESC,10i DESC',$page_obj->limit(), 'user_id,SUM(5i) as 5i,SUM(10i) as 10i,SUM(20i) as 20i,SUM(30i) as 30i,SUM(1h) as 1h,SUM(12h) as 12h,SUM(24h) as 24h,SUM(no_response) as no_response');
	//if (!is_array($list) || empty($list)) {;}
	foreach ($list as $key => $vo) 
	{
		$pre_count = $vo['5i'] + $vo['10i'] + $vo['20i'] + $vo['30i'] + $vo['1h'] + $vo['12h'] + $vo['24h'] + $vo['no_response'];
		$list[$key]['pre_5i']  = sprintf('%.2f',($vo['5i']/$pre_count)*100);
		$list[$key]['pre_10i'] = sprintf('%.2f',($vo['10i']/$pre_count)*100);
		$list[$key]['pre_20i'] = sprintf('%.2f',($vo['20i']/$pre_count)*100);
		$list[$key]['pre_30i'] = sprintf('%.2f',($vo['30i']/$pre_count)*100);
		$list[$key]['pre_1h']  = sprintf('%.2f',($vo['1h']/$pre_count)*100);
		$list[$key]['pre_12h'] = sprintf('%.2f',($vo['12h']/$pre_count)*100);
		$list[$key]['pre_24h'] = sprintf('%.2f',($vo['24h']/$pre_count)*100);
		$list[$key]['pre_no_response'] = sprintf('%.2f',($vo['no_response']/$pre_count)*100);
		$list[$key]['pre_reply'] = sprintf('%.2f', (1-$vo['no_response']/$pre_count)*100);
	}
}

//导出
if ($act == 'export') 
{
	$list = $response_reply_obj->get_replay_list_v2($tablename,false, $where_str, '5i DESC,10i DESC',"0,{$total_count}", 'user_id,SUM(5i) as 5i,SUM(10i) as 10i,SUM(20i) as 20i,SUM(30i) as 30i,SUM(1h) as 1h,SUM(12h) as 12h,SUM(24h) as 24h,SUM(no_response) as no_response');
	foreach ($list as $key => $vo) 
	{
		$data[$key]['user_id'] = $vo['user_id'];
		 $pre_count = $vo['5i'] + $vo['10i'] + $vo['20i'] + $vo['30i'] + $vo['1h'] + $vo['12h'] + $vo['24h'] + $vo['no_response'];
		  $data[$key]['pre_5i']  = $vo['5i'].'('.sprintf('%.2f',($vo['5i']/$pre_count)*100).'%)';
          $data[$key]['pre_10i'] = $vo['10i'].'('.sprintf('%.2f',($vo['10i']/$pre_count)*100).'%)';
          $data[$key]['pre_20i'] = $vo['20i'].'('.sprintf('%.2f',($vo['20i']/$pre_count)*100).'%)';
          $data[$key]['pre_30i'] = $vo['30i'].'('.sprintf('%.2f',($vo['30i']/$pre_count)*100).'%)';
          $data[$key]['pre_1h']  = $vo['1h'].'('.sprintf('%.2f',($vo['1h']/$pre_count)*100).')';
          $data[$key]['pre_12h'] = $vo['12h'].'('.sprintf('%.2f',($vo['12h']/$pre_count)*100).'%)';
          $data[$key]['pre_24h'] = $vo['24h'].'('.sprintf('%.2f',($vo['24h']/$pre_count)*100).'%)';
          $data[$key]['pre_no_response'] = $vo['no_response'].'('.sprintf('%.2f',($vo['no_response']/$pre_count)*100).'%)';
  	  	  $data[$key]['pre_reply'] = sprintf('%.2f', (1-$vo['no_response']/$pre_count)*100).'%';
	}
	//print_r($data);exit;
	$fileName = "回复响应榜报表";
    $title    = "回复响应榜报表";
    $headArr = array('用户ID', '5分钟回复', '10分钟回复', '20分钟回复', '30分钟回复', '1小时回复','12小时回复','24小时回复','无回复','回复百分比');
    getExcel($fileName,$title,$headArr,$data);
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