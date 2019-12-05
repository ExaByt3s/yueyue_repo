<?php
/**
 * @desc:   登录数据统计
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/8/28
 * @Time:   9:25
 * version: 1.0
 */
ignore_user_abort(ture);
ini_set("max_execution_time", 3600);
ini_set('memory_limit', '256M');
include('common.inc.php');
check_auth($yue_login_id,'seller_date_list');//权限控制
include_once(YUE_ADMIN_V2_PATH."common/Excel_v2.class.php");//导出类
include_once(YUE_ADMIN_V2_PATH.'report/include/pai_log_user_login_class.inc.php');

$log_user_login_obj = new pai_log_user_login_class();
$type_obj = POCO::singleton('pai_mall_goods_type_class');//商品品类
$page_obj = new show_page();
$show_page = 30;
$tpl = new SmartTemplate( REPORT_TEMPLATES_ROOT.'seller_date_list.tpl.htm' );
$act = trim($_INPUT['act']);

$start_date = trim($_INPUT['start_date']);
$end_date = trim($_INPUT['end_date']);

$type_id = intval($_INPUT['type_id']);

$where_str = '';
$setParam = array();
$type_list = $type_obj->get_type_cate(2); //商品品类选择

if(preg_match("/\d\d\d\d-\d\d-\d\d/", $start_date) || preg_match("/\d\d\d\d\d\d\d\d/", $start_date))
{
    $start_date = date('Y-m-d',strtotime($start_date));
    if(strlen($where_str)>0) $where_str .= ' AND ';
    $where_str .= "add_time >='".mysql_escape_string($start_date)."'";
    $setParam['start_date'] = $start_date;
}
if(preg_match("/\d\d\d\d-\d\d-\d\d/", $end_date) || preg_match("/\d\d\d\d\d\d\d\d/", $end_date))
{
    $end_date = date('Y-m-d',strtotime($end_date));
    if(strlen($end_date)>0) $where_str .= ' AND ';
    $where_str .= "add_time <='".mysql_escape_string($end_date)."'";
    $setParam['end_date'] = $end_date;
}
if($type_id >0){
    //商品品类选择
    foreach($type_list as $k => &$v)
    {
        $v['selected'] = $type_id==$v['id'] ? true : false;
    }
    $setParam['type_id'] = $type_id;
}

if($act == 'export')//导出数据
{
    $data = array();
    $list = $log_user_login_obj->get_log_seller_login_list(false,$type_id,$where_str,'GROUP BY add_time','add_time DESC,id DESC',"0,99999999","add_time,seller_7_login_num,seller_30_login_num,seller_7_hb_num,seller_30_hb_num,seller_count");
    foreach($list as $key=>$val)
    {
        $data[$key]['add_time'] = $val['add_time'];
        $data[$key]['seller_total_count'] = $val['seller_count'];
        $data[$key]['yueseller_7_login_num'] = $val['seller_7_login_num'];
        $data[$key]['yueseller_7_hb_num'] = $val['seller_7_hb_num'];
        $data[$key]['yueseller_30_login_num'] = $val['seller_30_login_num'];
        $data[$key]['yueseller_30_hb_num'] = $val['seller_30_hb_num'];
    }
    unset($list);
    $fileName = '商家登录统计列表';
    $headArr  = array("日期","总数","最近一周有上线的用户","环比","最近三十天有上线的用户","环比");
    Excel_v2::start($headArr,$data,$fileName);
    exit;
}

$page_obj->setvar($setParam);
$total_count = $log_user_login_obj->get_log_seller_login_list(true,$type_id,$where_str,'','','',"DISTINCT(add_time)");
$page_obj->set($show_page,$total_count);

$list = $log_user_login_obj->get_log_seller_login_list(false,$type_id,$where_str,'GROUP BY add_time','add_time DESC,id DESC',$page_obj->limit(),"add_time,seller_7_login_num,seller_30_login_num,seller_7_hb_num,seller_30_hb_num,seller_count");    //,SUM(seller_count) AS seller_total_count

if(!is_array($list)) $list = array();


$tpl->assign('type_list', $type_list);
$tpl->assign('list', $list);
$tpl->assign($setParam);
$tpl->assign('page', $page_obj->output(true));
$tpl->output();



