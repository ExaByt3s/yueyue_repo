<?php
/**
 * @desc:   商城日列表v1
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/7/14
 * @Time:   15:39
 * version: 1.0
 */
ignore_user_abort(ture);
ini_set("max_execution_time", 3600);
ini_set('memory_limit', '256M');
include('common.inc.php');
check_auth($yue_login_id,'mall_date_order_list');
include_once("/disk/data/htdocs232/poco/pai/yue_admin_v2/common/yue_function.php");
include_once("/disk/data/htdocs232/poco/pai/yue_admin_v2/common/Excel_v2.class.php");//导出类
$mall_order_obj = POCO::singleton( 'pai_mall_order_class' );//订单类
$page_obj = new show_page();
$show_total = 30;
$tpl = new SmartTemplate( REPORT_TEMPLATES_ROOT.'mall_date_order_list.tpl.htm' );

$act = trim($_INPUT['act']);
$start_sign_time = trim($_INPUT['start_sign_time']);
$end_sign_time   = trim($_INPUT['end_sign_time']);

$setParam = array();
$where_str = '';

if(preg_match("/\d\d\d\d-\d\d-\d\d/", $start_sign_time) || preg_match("/\d\d\d\d\d\d\d\d/", $start_sign_time))
{
    $start_sign_time = date('Y-m-d',strtotime($start_sign_time));
    if(strlen($where_str)>0) $where_str .= ' AND ';
    $where_str .= "FROM_UNIXTIME(sign_time,'%Y-%m-%d') >= '".mysql_escape_string($start_sign_time)."'";
    $setParam['start_sign_time'] = $start_sign_time;
}
if(preg_match("/\d\d\d\d-\d\d-\d\d/", $end_sign_time) || preg_match("/\d\d\d\d\d\d\d\d/", $end_sign_time))
{
    $end_sign_time = date('Y-m-d',strtotime($end_sign_time));
    if(strlen($where_str)>0) $where_str .= ' AND ';
    $where_str .= "FROM_UNIXTIME(sign_time,'%Y-%m-%d') <= '".mysql_escape_string($end_sign_time)."'";
    $setParam['end_sign_time'] = $end_sign_time;
}
//接特殊条件[获取已完成数据]
if(strlen($where_str)>0) $where_str .= ' AND ';
$where_str .="status=8 GROUP BY FROM_UNIXTIME(sign_time,'%Y-%m-%d')";


$total_list = $mall_order_obj->get_order_list(0,-1,false, $where_str,'','0,99999999',"FROM_UNIXTIME(sign_time,'%Y-%m-%d')");//计算个数
$total_count = count($total_list);

if($act == 'export') //导出数据
{
    $time_start = microtime_float();
    $list = $mall_order_obj->get_order_list(0,-1,false, $where_str,'sign_time DESC,order_id DESC','0,{$total_count}',"FROM_UNIXTIME(sign_time,'%Y-%m-%d') as sign_date,count(*) AS sign_count,SUM(total_amount) AS sign_price");
    if(!is_array($list))$list = array();
    $data = array();
    foreach($list as $key=>$val)
    {
        $sql_str = "FROM_UNIXTIME(close_time,'%Y-%m-%d')='".$val['sign_date']."'";
        $ret = $mall_order_obj->get_order_list(0,7,false, $sql_str,'','0,1',"count(*) AS colse_count,SUM(total_amount) AS colse_price");
        /*$val['colse_count'] = intval($ret[0]['colse_count']);
        $val['colse_price'] = $ret[0]['colse_price']*1;*/
        $data[$key]['sign_date'] = $val['sign_date'];
        $data[$key]['total_sum'] = intval($val['sign_count']+$ret[0]['colse_count']);
        $data[$key]['total_price'] = ($val['sign_price']+$ret[0]['colse_price'])*1;
        $data[$key]['sign_price']  = $val['sign_price'];
        $data[$key]['colse_price'] = $ret[0]['colse_price']*1;
        unset($ret);
    }
    $fileName = '销售日报';
    $headArr  = array("日期","单数","金额","已收","已退");
    Excel_v2::start($headArr,$data,$fileName);
    $time_end = microtime_float();
    $time = $time_end - $time_start;
    //if($yue_login_id == 100293) echo $time;
    exit;
}

$page_obj->set($show_total,$total_count);
$page_obj->setvar($setParam);
$list = $mall_order_obj->get_order_list(0,-1,false, $where_str,'sign_time DESC,order_id DESC',$page_obj->limit(),"FROM_UNIXTIME(sign_time,'%Y-%m-%d') as sign_date,count(*) AS sign_count,SUM(total_amount) AS sign_price");
if(!is_array($list))$list = array();
foreach($list as $key=>&$val)
{
    $sql_str = "FROM_UNIXTIME(close_time,'%Y-%m-%d')='".$val['sign_date']."'";
    $ret = $mall_order_obj->get_order_list(0,7,false, $sql_str,'','0,1',"count(*) AS colse_count,SUM(total_amount) AS colse_price");
    $val['colse_count'] = intval($ret[0]['colse_count']);
    $val['colse_price'] = $ret[0]['colse_price']*1;
    $val['total_sum']   = intval($val['sign_count']+$ret[0]['colse_count']);
    $val['total_price']   = ($val['sign_price']+$ret[0]['colse_price'])*1;
    unset($ret);
}

$tpl->assign($setParam);
$tpl->assign('list', $list);
$tpl->assign ( "page", $page_obj->output ( 1 ) );
$tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
$tpl->output();
