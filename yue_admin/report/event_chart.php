<?php


/**
 * @desc 最近 5个月的走势图
 * Created by PhpStorm.
 * User: xiao xiao
 * Date: 2015/5/19
 * Time: 14:38
 */


 include('common.inc.php');
 $tpl = new SmartTemplate("event_chart.tpl.htm");


 $month = date('Y-m-d', strtotime("-5 month",time()-24*3600));
// $pre_month = date("Y-m",strtotime('$month'))
 $date = date('Y-m-d',time());

 /*$sql_str = "SELECT FROM_UNIXTIME(complete_time,'%Y-%m') as complete_month,sum(budget*enroll_num) as confirm_price FROM yueyue_stat_db.yueyue_event_order_tbl
        WHERE FROM_UNIXTIME(complete_time,'%Y-%m')>='{$month}'
        AND ((event_status='2' OR (event_status='3' AND type='have_part_refunded') AND date_id>0) OR (event_status='2' AND date_id=0 AND enroll_status=1))
        AND pay_status=1 AND FROM_UNIXTIME(complete_time,'%Y-%m-%d')!='{$date}' GROUP BY FROM_UNIXTIME(complete_time,'%Y-%m') ";*/

$sql_str = "SELECT FROM_UNIXTIME(complete_time,'%Y-%m') as complete_month,sum(budget*enroll_num) as confirm_price FROM yueyue_stat_db.yueyue_event_order_tbl
        WHERE FROM_UNIXTIME(complete_time,'%Y-%m')>='{$month}'
        AND pay_status=1 AND FROM_UNIXTIME(complete_time,'%Y-%m-%d')!='{$date}' GROUP BY FROM_UNIXTIME(complete_time,'%Y-%m') ";

$ret = db_simple_getdata($sql_str,false,22);

$categories = '';
$data       = '';
foreach($ret as $key=>$val)
{
    if($key !=0)
    {
        $categories .= ',';
        $data       .= ',';
    }
    $categories .= "'{$val['complete_month']}'";
    $data       .= "{$val['confirm_price']}";
}

/*foreach($ret as $key=>$val)
{
    $ret[$key]['name'] = $val['complete_time'];
    $ret[$key]['value'] = $val['confirm_price'];

}*/



$tpl->assign('categories', $categories);
$tpl->assign('data', $data);
$tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
$tpl->output();