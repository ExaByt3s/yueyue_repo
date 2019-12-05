<?php
/**
 * @desc:   消费者分�?
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/10/13
 * @Time:   10:49
 * version: 1.0
 */
header("content-type:text/html;charset=utf-8");
include_once('common.inc.php');
check_auth($yue_login_id,'buyer_city_chart');//权限控制
include_once ("/disk/data/htdocs232/poco/php_common/poco_location_fun.inc.php");
include_once(YUE_ADMIN_V2_CLASS_ROOT."pai_buyer_location_reg_class.inc.php"); //地区统计
$buyer_location_obj = new pai_buyer_location_reg_class();

$tpl = new SmartTemplate( REPORT_TEMPLATES_ROOT.'buyer_city_chart.tpl.htm' );

$act = trim($_INPUT['act']);

$start_date = trim($_INPUT['start_date']);
$end_date = trim($_INPUT['end_date']);

//修改今日实时添加的用户
$buyer_location_obj->add_reg_userinfo_by_date();

//初始化数据
$where_str = '';
$setParam = array();

//preg_match("/\d\d\d\d-\d\d-\d\d/", $begin_day) || preg_match("/\d\d\d\d\d\d\d\d/", $begin_day)
if(preg_match("/\d\d\d\d-\d\d-\d\d/", $start_date) || preg_match("/\d\d\d\d\d\d\d\d/", $start_date)) //开始日期
{
    $start_date = date('Y-m-d',strtotime($start_date));
    if(strlen($where_str)>0) $where_str .= ' AND ';
    $where_str .= "date_time >= '".mysql_escape_string($start_date)."'";
    $setParam['start_date'] = $start_date;
}
if(preg_match("/\d\d\d\d-\d\d-\d\d/", $end_date) || preg_match("/\d\d\d\d\d\d\d\d/", $end_date)) //结束日期
{
    $end_date = date('Y-m-d',strtotime($end_date));
    if(strlen($where_str)>0) $where_str .= ' AND ';
    $where_str .= "date_time <= '".mysql_escape_string($end_date)."'";
    $setParam['end_date'] = $end_date;
}


$ret = $buyer_location_obj->get_buyer_location_reg_list(false,$where_str,"GROUP BY location_id","date_time DESC,id DESC","0,99999999","SUM(user_count) AS user_count,location_id AS location");

/*print_r($list);

exit;

$sql_str = "SELECT LEFT(location_id,6) AS location,COUNT(user_id) AS user_count  FROM pai_db.pai_user_tbl WHERE location_id>0
GROUP BY LEFT(location_id,6) ORDER BY user_count DESC";
$ret = db_simple_getdata($sql_str, false, 101);*/

if(!is_array($ret)) $ret = array();

$data ='';
foreach($ret as $key=>$v)
{
    $city = get_poco_location_name_by_location_id($v['location']);
    if($key !=0) $data .= ',';
    $city = iconv('gbk', 'utf-8', $city);
    $data .= "{name: '".$city."',value: '".$v['user_count']."'}";
}

$tpl->assign($setParam);
$tpl->assign('data',$data);
$tpl->output();