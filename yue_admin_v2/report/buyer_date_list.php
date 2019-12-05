<?php
/**
 * @desc:  消费者登录数据列表
 *@User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/8/31
 * @Time:   9:47
 * version: 1.0
 */
ignore_user_abort(ture);
ini_set("max_execution_time", 3600);
ini_set('memory_limit', '256M');
include('common.inc.php');
check_auth($yue_login_id,'buyer_date_list');//权限控制
include_once(YUE_ADMIN_V2_PATH."common/Excel_v2.class.php");//导出类
include_once(YUE_ADMIN_V2_PATH.'report/include/pai_log_user_login_class.inc.php');
$log_user_login_obj = new pai_log_user_login_class();
$page_obj = new show_page();
$show_page = 30;


$tpl = new SmartTemplate( REPORT_TEMPLATES_ROOT.'buyer_date_list.tpl.htm' );
$act = trim($_INPUT['act']);
$start_date = trim($_INPUT['start_date']);
$end_date = trim($_INPUT['end_date']);
$where_str = '';
$setParam = array();

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
if($act == 'export')//导出数据
{
    $data = array();
    $list = $log_user_login_obj->get_log_buyer_login_list(false,$where_str,'GROUP BY add_time','add_time DESC,id DESC',"0,99999999","add_time,buyer_count,buyer_7_login_num,buyer_7_hb_num,buyer_30_login_num,buyer_30_hb_num");
    /*foreach($list as $key=>$val)
    {
        $data[$key]['add_time'] = $val['add_time'];
        $data[$key]['buyer_total_count'] = $val['buyer_total_count'];
        $data[$key]['buyer_7_login_num'] = $val['buyer_7_login_num'];
        $data[$key]['buyer_7_hb_num'] = $val['buyer_7_hb_num'];
        $data[$key]['buyer_30_login_num'] = $val['buyer_30_login_num'];
        $data[$key]['buyer_30_hb_num'] = $val['buyer_30_hb_num'];
    }
    unset($list);*/
    if(!is_array($list)) $list = array();
    $fileName = '消费者登录统计列表';
    $headArr  = array("日期","总数","最近一周有上线的用户","环比","最近三十天有上线的用户","环比");
    Excel_v2::start($headArr,$list,$fileName);
    exit;
}


$page_obj->setvar($setParam);
$total_count = $log_user_login_obj->get_log_buyer_login_list(true,$where_str,'','','',"DISTINCT(add_time)");
$page_obj->set($show_page,$total_count);

$list = $log_user_login_obj->get_log_buyer_login_list(false,$where_str,'GROUP BY add_time','add_time DESC,id DESC',$page_obj->limit(),"add_time,buyer_7_login_num,buyer_30_login_num,buyer_30_hb_num,buyer_7_hb_num,buyer_count");

$tpl->assign('list', $list);
$tpl->assign($setParam);
$tpl->assign('page', $page_obj->output(true));
$tpl->output();
