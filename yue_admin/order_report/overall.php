<?php

include_once 'common.inc.php';

if($_INPUT['fenbu']==1)
{
    $tpl = new SmartTemplate("order_overall.tpl.htm");
}
else
{
    $tpl = new SmartTemplate("overall.tpl.htm");
}




$page_obj = new show_page ();

$month = $_INPUT['month'];
$day = $_INPUT['day'];

if($_INPUT['begin_time'])
    $begin_time = date("Y-m-d",strtotime($_INPUT['begin_time']));

if($_INPUT['end_time'])
    $end_time = date("Y-m-d",strtotime($_INPUT['end_time']));

$channel_cate = $_INPUT['channel_cate'];



$where = "WHERE 1";
$select_type = "FROM_UNIXTIME(paid_time,'%Y-%m')";
$chart_select = "{$select_type} AS `date`";
$date_text = "月";


if($begin_time && $end_time)
{
    $where .= " AND FROM_UNIXTIME(paid_time,'%Y-%m-%d') BETWEEN '{$begin_time}' AND '{$end_time}'";

    $select_type = "FROM_UNIXTIME(paid_time,'%Y-%m-%d')";
    $chart_select = "{$select_type} AS `date`";
    $date_text = "日";
}

$not_where_channel = $where;

if($channel_cate)
{
    $channel_cate_where = " AND channel_cate='{$channel_cate}'";
    $where .= $channel_cate_where;
}



//订单走势图
$sql = "SELECT COUNT(DISTINCT(in_user_id_str)) AS trade_num,COUNT(*) AS num,SUM(in_amount) AS amount,ROUND(SUM(in_amount)/COUNT(*)) AS pct,(COUNT(*)-COUNT(DISTINCT(out_user_id)))/COUNT(*) AS rebuy, {$chart_select} FROM pai_finance_db.pai_report_order_tbl {$where} GROUP BY `date`;";
$chart_arr=db_simple_getdata($sql);

//饼图
$sql = "SELECT channel_cate_name as label,SUM(in_amount) AS data,count(*) as num FROM pai_finance_db.pai_report_order_tbl {$not_where_channel} GROUP BY channel_cate_name;";
$pie_arr = db_simple_getdata($sql);
foreach($pie_arr as $k=>$val)
{
    $pie_arr[$k]['data'] = (int)$val['data'];
    $pie_arr[$k]['num'] = (int)$val['num'];
}
$pie_arr=poco_iconv_arr($pie_arr,'GBK', 'UTF-8');

//订单总数
$sql = "SELECT COUNT(*) AS num FROM pai_finance_db.pai_report_order_tbl {$where}";
$count_order_arr=db_simple_getdata($sql,true);
$total_order = $count_order_arr['num'];


//独立消费人数
$sql = "SELECT COUNT(DISTINCT(out_user_id)) AS num FROM pai_finance_db.pai_report_order_tbl {$where};";
$unique_order_arr=db_simple_getdata($sql,true);
$unique_order = $unique_order_arr['num'];

//客单价
$sql = "SELECT SUM(in_amount)/COUNT(*) AS pct FROM pai_finance_db.pai_report_order_tbl {$where};";
$pct_arr=db_simple_getdata($sql,true);
$pct = round($pct_arr['pct']);

//最高客单价
$sql = "SELECT MAX(in_amount) AS pct FROM pai_finance_db.pai_report_order_tbl {$where};";
$max_pct_arr=db_simple_getdata($sql,true);
$max_pct = $max_pct_arr['pct'];



//复购率 =（订单数-独立消费人数）/订单数
$rebuy = round((($total_order-$unique_order)/$unique_order)*100,2);

//复购订单数=订单数-独立消费人数
$rebuy_order = $total_order-$unique_order;

//复购人数
$sql = "SELECT COUNT(*) AS c FROM pai_finance_db.pai_report_order_tbl {$where} GROUP BY out_user_id HAVING c>1;";
$rebuy_num_arr =db_simple_getdata($sql);
$rebuy_num = count($rebuy_num_arr);

//最高月日
$sql = "SELECT COUNT(*) AS num, {$chart_select} FROM pai_finance_db.pai_report_order_tbl {$where} GROUP BY `date` ORDER BY num DESC LIMIT 1;";
$max_arr=db_simple_getdata($sql,true);
if($begin_time && $end_time)
{
    $max_date = "最高日<br />".date("j",strtotime($max_arr['date']))."日";
}
else
{
    $max_date = "最高月<br />".date("n",strtotime($max_arr['date']))."月";
}
//最高月日订单数
$sql = "SELECT COUNT(*) AS num FROM pai_finance_db.pai_report_order_tbl WHERE {$select_type}='{$max_arr['date']}' {$channel_cate_where};";
$max_order_arr=db_simple_getdata($sql,true);
$max_order = $max_order_arr['num'];

//最高月日平均客单价
$sql = "SELECT SUM(in_amount)/COUNT(*) AS pct FROM pai_finance_db.pai_report_order_tbl WHERE {$select_type}='{$max_arr['date']}' {$channel_cate_where};";
$max_max_pct_arr=db_simple_getdata($sql,true);
$max_max_pct = round($max_max_pct_arr['pct']);


//平均月增长率
$first_order_arr = $chart_arr[0];
$first_order_num = $first_order_arr['num'];

$end_order_arr = end($chart_arr);
$end_order_num = $end_order_arr['num'];
$total_date = count($chart_arr);

$average_rise = (($end_order_num/$first_order_num)^(1/($total_date-1))-1)*100;
$average_rise = round($average_rise,2);



//平均月订单
$sum_order=0;
$sum_pct=0;
foreach($chart_arr as $k=>$val)
{
    $sum_order += $val['num'];
    $sum_pct += $val['pct'];
    $sum_rebuy += $val['rebuy'];


}

$reverse_arr = array_reverse($chart_arr);
//print_r($reverse_arr);
foreach($reverse_arr as $k=>$val)
{
    if($k+1<$total_date)
    {
        $next_order = $reverse_arr[$k]['num'];
        $prev_order = $reverse_arr[$k+1]['num'];
        if($prev_order)
        {
            $sum_average_order += ($next_order-$prev_order)/$prev_order;
        }


        $next_pct = $reverse_arr[$k]['pct'];
        $prev_pct = $reverse_arr[$k+1]['pct'];
        if($prev_pct)
        {
            $sum_pct_order += ($next_pct-$prev_pct)/$prev_pct;
        }

    }

}

$average_order = round(($sum_average_order/$total_date)*100,2);
$average_pct = round(($sum_pct_order/$total_date)*100,2);


//订单价格分布
$fenbu_type = array("0"=>"0-100","1"=>"101-300","2"=>"301-500","3"=>"501-1000","4"=>"1001-5000","5"=>"5001-10000","6"=>"10000以上");

$sql="SELECT COUNT(name.trade_id) AS num ,c FROM (
SELECT trade_id ,
CASE
WHEN in_amount<=100 THEN '0'
WHEN (in_amount>101 AND in_amount<=300) THEN '1'
WHEN (in_amount>301 AND in_amount<=500) THEN '2'
WHEN (in_amount>501 AND in_amount<=1000) THEN '3'
WHEN (in_amount>1001 AND in_amount<=5000) THEN '4'
WHEN (in_amount>5001 AND in_amount<=10000) THEN '5'
ELSE '6'
END AS c FROM pai_finance_db.pai_report_order_tbl {$where}
)  AS `name` GROUP BY `name`.c ORDER BY c ASC;";
$fenbu_arr=db_simple_getdata($sql);
foreach($fenbu_arr as $k=>$val)
{
    $fenbu_arr[$k]['c'] = $fenbu_type[$val['c']];
}
$fenbu_arr=poco_iconv_arr($fenbu_arr,'GBK', 'UTF-8');


//有交易供应商  供应商平均收入
$sql="SELECT COUNT(DISTINCT(in_user_id_str)) AS num, ROUND(SUM(in_amount)/COUNT(DISTINCT(in_user_id_str)),2) AS average FROM pai_finance_db.pai_report_order_tbl {$where};";
$gongyi_arr=db_simple_getdata($sql,true);
$trade_user_num = $gongyi_arr['num'];
$average_trade_num = $gongyi_arr['average'];
//平均客单价
//$average_pct = round($sum_pct/$total_date);

//平均复购率
//$average_rebuy = round($sum_rebuy/$total_date,2)*100;


$tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_MOBILE_ADMIN_REPORT_HEADER);
$tpl->assign('chart_json', json_encode($chart_arr));
$tpl->assign('pie_json', json_encode($pie_arr));
$tpl->assign('fenbu_json', json_encode($fenbu_arr));
$tpl->assign('total_order', $total_order);
$tpl->assign('unique_order', $unique_order);
$tpl->assign('pct', $pct);
$tpl->assign('max_pct', $max_pct);
$tpl->assign('min_pct', $min_pct);
$tpl->assign('rebuy', $rebuy);
$tpl->assign('rebuy_order', $rebuy_order);
$tpl->assign('rebuy_num', $rebuy_num);
$tpl->assign('max_date', $max_date);
$tpl->assign('max_order', $max_order);
$tpl->assign('max_max_pct', $max_max_pct);
$tpl->assign('max_rebuy', $max_rebuy);
$tpl->assign('average_rise', $average_rise);
$tpl->assign('average_order', $average_order);
$tpl->assign('average_pct', $average_pct);
$tpl->assign('average_rebuy', $average_rebuy);
$tpl->assign('trade_user_num', $trade_user_num);
$tpl->assign('average_trade_num', $average_trade_num);
$tpl->assign('month', $month);
$tpl->assign('day', $day);
$tpl->assign('channel_cate', $channel_cate);
$tpl->assign('date_text', $date_text);
$tpl->assign('begin_time', $begin_time);
$tpl->assign('end_time', $end_time);
$tpl->assign ( "page", $page_obj->output ( 1 ) );
$tpl->output();

?>