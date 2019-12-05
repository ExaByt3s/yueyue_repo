<?php
/**
 * @desc:   订单流水日走势图
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/5/29
 * @Time:   15:05
 * version: 1.0
 */

include('common.inc.php');

$event_order_obj = POCO::singleton('pai_event_order_report_class');

$tpl = new SmartTemplate("event_date_chat.tpl.htm");

$month  = trim($month) ? trim($month) : date('Y-m',time()-24*3600);

$date = date('Y-m-d',time());

$setParam = array();

if(strlen($month) >0) $setParam['month'] = $month;

$where_str = "pay_status=1 AND
              FROM_UNIXTIME(complete_time,'%Y-%m')='".mysql_escape_string($month)."' AND FROM_UNIXTIME(complete_time,'%Y-%m-%d')!='{$date}'";

$sql_str = "SELECT  FROM_UNIXTIME(complete_time,'%d') as complete_time,
            sum(budget*enroll_num) as price FROM yueyue_stat_db.yueyue_event_order_tbl
            WHERE {$where_str} GROUP BY FROM_UNIXTIME(complete_time,'%Y-%m-%d') ORDER BY complete_time ASC";

$ret = db_simple_getdata($sql_str,false,22);

if(!is_array($ret)) $ret = array();

$categories = '';
$data       = '';
foreach($ret as $key=>$val)
{
    if($key !=0)
    {
        $categories .= ',';
        $data       .= ',';
    }
    $categories .= "'{$val['complete_time']}日'";
    $data       .= "{$val['price']}";
}


$tpl->assign($setParam);
$tpl->assign('categories', $categories);
$tpl->assign('data', $data);
$tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
$tpl->output();
