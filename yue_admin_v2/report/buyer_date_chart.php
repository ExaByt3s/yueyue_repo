<?php
/**
 * @desc:   消费者登录数据折线图
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/8/31
 * @Time:   9:47
 * version: 1.0
 */
include('common.inc.php');
check_auth($yue_login_id,'buyer_date_list');//权限控制
include_once(YUE_ADMIN_V2_PATH.'report/include/pai_log_user_login_class.inc.php');
$log_user_login_obj = new pai_log_user_login_class();

$tpl = new SmartTemplate( REPORT_TEMPLATES_ROOT.'buyer_date_chart.tpl.htm' );
$act = trim($_INPUT['act']);

$month = trim($_INPUT['month']);

$where_str = '';
$setParam = array();

if(!preg_match("/\d\d\d\d-\d\d/", $month))
{
    $month = date('Y-m',time()-24*3600);
}

if(strlen($month)>0)
{
    if(strlen($where_str)>0) $where_str .= ' AND ';
    $where_str .= "FROM_UNIXTIME(UNIX_TIMESTAMP(add_time),'%Y-%m')='".mysql_escape_string($month)."'";
    $setParam['month'] = $month;
}

$list = $log_user_login_obj->get_log_buyer_login_list(false,$where_str,'GROUP BY add_time','add_time ASC,id DESC',"0,31","add_time,buyer_7_login_num,buyer_30_login_num");

$date_str = '';
$buyer_7_login_str   = '';
$buyer_30_login_str = '';
foreach($list as $key=>$val)
{
    if($key !=0)
    {
        $date_str .= ',';
        $buyer_7_login_str .= ',';
        $buyer_30_login_str .= ',';
    }
    $date = date('m-d',strtotime($val['add_time'])) ;
    $date_str .= "'{$date}'";
    $buyer_7_login_str .= "{$val['buyer_7_login_num']}";
    $buyer_30_login_str .= "{$val['buyer_30_login_num']}";
}

$tpl->assign('date_str', $date_str);
$tpl->assign('buyer_7_login_str', $buyer_7_login_str);
$tpl->assign('buyer_30_login_str', $buyer_30_login_str);
$tpl->assign($setParam);
$tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
$tpl->output();
