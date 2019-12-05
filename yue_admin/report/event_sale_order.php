<?php

/**
 * @desc 销售日报 
 * @authors xiao xiao (xiaojm@yueus.com)
 * @date    2015年5月18日
 * @version 1.0
 */

 include_once ('/disk/data/htdocs232/poco/pai/yue_admin/audit/include/Classes/PHPExcel.php');
 //常用函数
 include_once("/disk/data/htdocs232/poco/pai/yue_admin/common/yue_function.php");
 ini_set('memory_limit', '256M');

 include('common.inc.php');
 
 $event_order_obj = POCO::singleton('pai_event_order_report_class');
 
 $tpl = new SmartTemplate("event_sale_order.tpl.htm");
 
 
 $page_obj = new show_page();
 $show_total = 30;
 
 $act = trim($_INPUT['act']);
 
 $start_time = trim($_INPUT['start_time']);
 $end_time   = trim($_INPUT['end_time']);

 $date = date('Y-m-d',time());
 
 $setParam = array();
 /*$where_str = "((event_status='2' OR (event_status='3' AND type='have_part_refunded') AND date_id>0)
              OR (event_status='2' AND date_id=0)) AND pay_status=1 AND FROM_UNIXTIME(complete_time,'%Y-%m-%d')!='{$date}'";*/

$where_str = "pay_status=1 AND FROM_UNIXTIME(complete_time,'%Y-%m-%d')!='{$date}'";
 
 if(strlen($start_time)>0)
 {
 	if(strlen($where_str)>0) $where_str .= ' AND ';
 	$where_str .= "FROM_UNIXTIME(complete_time,'%Y-%m-%d') >= '".mysql_escape_string($start_time)."'";
 	$setParam['start_time'] = $start_time;
 }
 
 if(strlen($end_time) >0)
 {
 	if(strlen($where_str)>0) $where_str .= ' AND ';
 	$where_str .= "FROM_UNIXTIME(complete_time,'%Y-%m-%d') <= '".mysql_escape_string($end_time)."'";
 	$setParam['end_time'] = $end_time;
 }

$sql_str_total = "SELECT  FROM_UNIXTIME(complete_time,'%Y-%m-%d') FROM yueyue_stat_db.yueyue_event_order_tbl
                     WHERE {$where_str} GROUP BY FROM_UNIXTIME(complete_time,'%Y-%m-%d')";

$sql_str = "SELECT  FROM_UNIXTIME(complete_time,'%Y-%m-%d') as complete_time,count(*) as c,
            sum(budget*enroll_num) as price,sum(budget*enroll_num*refund_rep) as ref_price FROM yueyue_stat_db.yueyue_event_order_tbl
            WHERE {$where_str} GROUP BY FROM_UNIXTIME(complete_time,'%Y-%m-%d') ORDER BY complete_time DESC";

$ret_total = db_simple_getdata($sql_str_total,true,22);
$total_count = count($ret_total);


  //导出数据
  if($act == 'export')
  {
      $ret = db_simple_getdata($sql_str,false,22);
      if(!is_array($ret)) $ret = array();
      $data = array();
      foreach($ret as $key=>$val)
      {
          $data[$key]['complete_time'] = $val['complete_time'];
          $data[$key]['c']             = $val['c'];
          $data[$key]['price']         = $val['price'];
          $confirm_price = comfirm_price($val['complete_time']);
          $reprice       = reprice($val['complete_time']);
          $data[$key]['confirm_price'] = $confirm_price+$reprice;
          $data[$key]['ref_price']     = sprintf('%.2f',$val['price']-$confirm_price-$reprice);
      }
      $fileName = '销售日报';
      $title    = '销售日报列表';
      $headArr  = array("日期","单数","金额","已收","已退");
      getExcel($fileName,$title,$headArr,$data);
      exit;
  }


 $page_obj->set($show_total,$total_count);
 $page_obj->setvar($setParam);

 $sql_str .= " LIMIT {$page_obj->limit()}";

 $ret = db_simple_getdata($sql_str,false,22);

 if(!is_array($ret)) $ret = array();



 foreach ($ret as $key=>$val)
 {
 	//$ret[$key]['ref_price'] = sprintf('%.2f',$val['price']-$val['confirm_price']);
     $confirm_price = comfirm_price($val['complete_time']);
     $reprice       = reprice($val['complete_time']);
     $ret[$key]['confirm_price'] = $confirm_price+$reprice;
     $ret[$key]['ref_price'] = sprintf('%.2f',$val['price']-$confirm_price-$reprice);
 }
 

function comfirm_price($date_time)
{
    $sql_str = "SELECT sum(budget*enroll_num) as confirm_price FROM yueyue_stat_db.yueyue_event_order_tbl
        WHERE FROM_UNIXTIME(complete_time,'%Y-%m-%d')='{$date_time}'
        AND ((event_status='2' AND date_id>0) OR (event_status='2' AND date_id=0 AND enroll_status=1)) AND pay_status=1";
    $ret_price = db_simple_getdata($sql_str,true,22);
    if(is_array($ret_price))
    {
        //echo $ret_price['confirm_price'];
        return floatval($ret_price['confirm_price']);
    }
    return 0;
}

//37获取价格
function reprice($date_time)
{
    $sql_str = "SELECT sum(budget*enroll_num*refund_rep) as reprice FROM yueyue_stat_db.yueyue_event_order_tbl
        WHERE FROM_UNIXTIME(complete_time,'%Y-%m-%d')='{$date_time}'
        AND event_status='3' AND type='have_part_refunded' AND pay_status=1";
    $ret_price = db_simple_getdata($sql_str,true,22);
    if(is_array($ret_price))
    {
        //echo $ret_price['confirm_price'];
        return floatval($ret_price['reprice']);
    }
    return 0;
}
 
 
 
 /* print_r($ret);
 exit; */
 
 $tpl->assign($setParam);
 $tpl->assign('list', $ret);
 $tpl->assign ( "page", $page_obj->output ( 1 ) );
 $tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
 $tpl->output();
 
 ?>