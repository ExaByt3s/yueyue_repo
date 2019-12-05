<?php
/**
 * @desc:      
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/10/16
 * @Time:   11:23
 * version: 1.0
 */

include_once('common.inc.php');
check_auth($yue_login_id,'seller_summary_list');//权限控制
include_once(YUE_ADMIN_V2_PATH."common/Excel_v2.class.php");//导出类
include_once(YUE_ADMIN_V2_PATH.'report/include/pai_seller_summary_class.inc.php');

$seller_summary_obj = new pai_seller_summary_class();
$page_obj = new show_page();
$show_count = 30;
$type_obj = POCO::singleton('pai_mall_goods_type_class');//商品品类
$tpl = new SmartTemplate( REPORT_TEMPLATES_ROOT.'seller_summary_list.tpl.htm' );

$act = trim($_INPUT['act']);
$type_id = (int)$type_id;
$start_date = trim($_INPUT['start_date']);
$end_date = trim($_INPUT['end_date']);

//初始化数据
$where_str = '';
$setParam = array();
$type_list = $type_obj->get_type_cate(2); //商品品类选择


//条件数据
if($type_id >=0)
{
    //商品品类选择
    foreach($type_list as $k => &$v)
    {
        $v['selected'] = $type_id==$v['id'] ? true : false;
    }
    $setParam['type_id'] = $type_id;
}

if(preg_match("/\d\d\d\d-\d\d-\d\d/", $start_date) || preg_match("/\d\d\d\d\d\d\d\d/", $start_date))//开始时间
{
    $start_date = date('Y-m-d',strtotime($start_date));
    if(strlen($where_str)>0) $where_str .= ' AND ';
    $where_str .= "date_time >= '".mysql_escape_string($start_date)."'";
    $setParam['start_date'] = $start_date;
}
if(preg_match("/\d\d\d\d-\d\d-\d\d/", $end_date) || preg_match("/\d\d\d\d\d\d\d\d/", $end_date))//结束时间
{
    $end_date = date('Y-m-d',strtotime($end_date));
    if(strlen($where_str)>0) $where_str .= ' AND ';
    $where_str .= "date_time <= '".mysql_escape_string($end_date)."'";
    $setParam['end_date'] = $end_date;
}

if($act == 'export')//导出数据
{
    $data = array();
    $list = $seller_summary_obj->get_seller_summary_list(false,$type_id,$where_str,'date_time DESC,id DESC',"date_time,seller_total,seller_add_date_count,seller_login_count,seller_contact_count,trade_seller_count,be_evaluated_seller_count");
    if(!is_array($list)) $list = array();
    foreach($list as $key=>$v)
    {
        $data[$key]['date_time'] = $v['date_time'];
        $data[$key]['seller_total'] = $v['seller_total'];
        $data[$key]['seller_add_date_count'] = $v['seller_add_date_count'];
        $data[$key]['seller_login_count'] = $v['seller_login_count'];
        $data[$key]['seller_contact_count'] = $v['seller_contact_count'];
        $data[$key]['trade_seller_count'] = $v['trade_seller_count'];
        $data[$key]['be_evaluated_seller_count'] = $v['be_evaluated_seller_count'];
    }
    unset($list);
    $fileName = '商家总计列表';
    $headArr  = array("日期","认证商家总数","当日新增商家数","当日登陆商家数","当日互动商家数","当日交易商家数","当日被评价数商家数");
    Excel_v2::start($headArr,$data,$fileName);
    exit;
}

$page_obj->setvar($setParam);

$total_count = $seller_summary_obj->get_seller_summary_list(true,$type_id,$where_str);
$page_obj->set($show_count,$total_count);

$list = $seller_summary_obj->get_seller_summary_list(false,$type_id,$where_str,'date_time DESC,id DESC',$page_obj->limit());

if(!is_array($list)) $list = array();

$tpl->assign('type_list', $type_list);
$tpl->assign($setParam);
$tpl->assign('list',$list);
$tpl->assign('page',$page_obj->output(true));
$tpl->output();

