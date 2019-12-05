<?php
/**
 * @desc:   登录数据统计
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/8/28
 * @Time:   9:25
 * version: 1.0
 */
include('common.inc.php');
check_auth($yue_login_id,'seller_date_list');//权限控制
include_once(YUE_ADMIN_V2_PATH.'report/include/pai_log_user_login_class.inc.php');
$log_user_login_obj = new pai_log_user_login_class();
$type_obj = POCO::singleton('pai_mall_goods_type_class');//商品品类
$tpl = new SmartTemplate( REPORT_TEMPLATES_ROOT.'seller_date_chart.tpl.htm' );
$act = trim($_INPUT['act']);

$month = trim($_INPUT['month']);

$type_id = intval($_INPUT['type_id']);

$where_str = '';
$setParam = array();
$type_list = $type_obj->get_type_cate(2); //商品品类选择

if(!preg_match("/\d\d\d\d-\d\d/", $month)) $month = date('Y-m',time()-24*3600);
if(strlen($month)>0)
{
    if(strlen($where_str)>0) $where_str .= ' AND ';
    $where_str .= "FROM_UNIXTIME(UNIX_TIMESTAMP(add_time),'%Y-%m')='".mysql_escape_string($month)."'";
    $setParam['month'] = $month;
}
if($type_id >0){
    //商品品类选择
    foreach($type_list as $k => &$v)
    {
        $v['selected'] = $type_id==$v['id'] ? true : false;
    }
    $setparam['type_id'] = $type_id;
}

$list = $log_user_login_obj->get_log_seller_login_list(false,$type_id,$where_str,'GROUP BY add_time','add_time ASC,id DESC',"0,31","add_time,seller_7_login_num,seller_30_login_num");

$date_str = '';
$seller_7_login_str   = '';
$seller_30_login_str = '';
foreach($list as $key=>$val)
{
    if($key !=0)
    {
        $date_str .= ',';
        $seller_7_login_str .= ',';
        $seller_30_login_str .= ',';
    }
    $date = date('m-d',strtotime($val['add_time'])) ;
    $date_str .= "'{$date}'";
    $seller_7_login_str .= "{$val['seller_7_login_num']}";
    $seller_30_login_str .= "{$val['seller_30_login_num']}";
}

$tpl->assign('type_list', $type_list);
$tpl->assign('date_str', $date_str);
$tpl->assign('seller_7_login_str', $seller_7_login_str);
$tpl->assign('seller_30_login_str', $seller_30_login_str);
$tpl->assign($setParam);
$tpl->output();



