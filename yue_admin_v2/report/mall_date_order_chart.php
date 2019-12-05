<?php
/**
 * @desc:   日销售走势图v2版
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/7/14
 * @Time:   17:54
 * version: 2.0
 */
include('common.inc.php');
check_auth($yue_login_id,'mall_date_order_chart');//权限控制

$mall_order_obj = POCO::singleton( 'pai_mall_order_class' );//订单类
$tpl = new SmartTemplate(REPORT_TEMPLATES_ROOT.'mall_date_order_chart.tpl.htm');

$month = trim($_INPUT['month']);

$where_str = '';
$setParam = array();

//月份处理
if(!preg_match("/\d\d\d\d-\d\d/", $month))
{
    $month = date('Y-m',time()-24*3600);
}
if(strlen($month) >0)
{
    if(strlen($where_str) >0) $where_str .= ' AND ';
    $where_str .= "FROM_UNIXTIME(sign_time,'%Y-%m')= '".mysql_escape_string($month)."'";
    $setParam['month'] = $month;
}
//接特殊条件[获取已完成数据]
if(strlen($where_str)>0) $where_str .= ' AND ';
$where_str .="status=8 GROUP BY FROM_UNIXTIME(sign_time,'%Y-%m-%d')";

$list = $mall_order_obj->get_order_list(0,-1,false, $where_str,'sign_time ASC','0,100',"FROM_UNIXTIME(sign_time,'%Y-%m-%d') as sign_date,SUM(total_amount) AS sign_price");
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
    $sql_str = "FROM_UNIXTIME(close_time,'%Y-%m-%d')='".$val['sign_date']."'";
    $ret = $mall_order_obj->get_order_list(0,7,false, $sql_str,'','0,1',"SUM(total_amount) AS colse_price");
    //print_r($ret);
    $date = date('m-d',strtotime($val['sign_date'])) ;
    $date_str .= "'{$date}'";
    $sig_price_str .= "{$val['sign_price']}";
    $close_price_str .= "{$ret[0]['colse_price']}";
}
$tpl->assign('date_str', $date_str);
$tpl->assign('sig_price_str', $sig_price_str);
$tpl->assign('close_price_str', $close_price_str);
$tpl->assign($setParam);
$tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
$tpl->output();