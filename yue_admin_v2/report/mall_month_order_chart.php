<?php
/**
 * @desc:   销售走势图(月)v2版
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/7/15
 * @Time:   14:15
 * version: 2.0
 */
include('common.inc.php');
check_auth($yue_login_id,'mall_month_order_chart');

$mall_order_obj = POCO::singleton( 'pai_mall_order_class' );//订单类
$tpl = new SmartTemplate(REPORT_TEMPLATES_ROOT.'mall_month_order_chart.tpl.htm');

$month = date('Y-m-d', strtotime("-5 month",time()));//前五个月

$where_str .="FROM_UNIXTIME(sign_time,'%Y-%m')>='{$month}' AND status=8 GROUP BY FROM_UNIXTIME(sign_time,'%Y-%m')";
$list = $mall_order_obj->get_order_list(0,-1,false, $where_str,'sign_time ASC','0,100',"FROM_UNIXTIME(sign_time,'%Y-%m') as sign_date,SUM(total_amount) AS sign_price");
if(!is_array($list)) $list = array();
$date_str = '';
$sig_price_str   = '';
$close_price_str = '';
foreach($list as $key=>$val)
{
    if($key !=0)
    {
        $date_str .= ',';
        $sig_price_str .= ',';
        $close_price_str .= ',';
    }
    $sql_str = "FROM_UNIXTIME(close_time,'%Y-%m')='".$val['sign_date']."'";
    $ret = $mall_order_obj->get_order_list(0,7,false, $sql_str,'','0,1',"SUM(total_amount) AS colse_price");
    $date_str .= "'{$val['sign_date']}'";
    $sig_price_str .= "{$val['sign_price']}";
    $close_price_str .= "{$ret[0]['colse_price']}";
}
$tpl->assign('date_str', $date_str);
$tpl->assign('sig_price_str', $sig_price_str);
$tpl->assign('close_price_str', $close_price_str);
$tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
$tpl->output();