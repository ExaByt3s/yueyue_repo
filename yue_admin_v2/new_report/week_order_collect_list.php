<?php
/**
 * @desc:      
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/11/20
 * @Time:   13:50
 * version: 1.0
 */
ignore_user_abort(ture);
ini_set("max_execution_time", 3600);
ini_set('memory_limit', '256M');
include('common.inc.php');
include_once('top.php');
//check_auth($yue_login_id,'order_report_list');//权限控制

include_once("/disk/data/htdocs232/poco/pai/yue_admin_v2/common/yue_function.php");
include_once("/disk/data/htdocs232/poco/pai/yue_admin_v2/common/Excel_v2.class.php");//导出类
include_once(YUE_ADMIN_V2_CLASS_ROOT.'pai_order_report_collect_class.inc.php');

$pai_order_report_obj = POCO::singleton( 'pai_order_report_collect_class' );
$type_obj = POCO::singleton( 'pai_mall_goods_type_class' );
$page_obj = new show_page();
$show_count = 20;

$tpl = new SmartTemplate(TEMPLATES_ROOT.'week_order_collect_list.tpl.htm');

$act = trim($_INPUT['act']);
$type = 'week';
$type_id = (int)$_INPUT['type_id'];
$start_date = trim($_INPUT['start_date']);
$end_date = trim($_INPUT['end_date']);

$where_str = '';
$setParam = array();
//参数整理
//商品品类选择
$type_list = $type_obj->get_type_cate(2);
foreach($type_list as $k => &$v)
{
    $v['selected'] = $type_id==$v['id'] ? true : false;
}
if(strlen($type)>0) $setParam['type'] = $type;
if($type_id >0) $setParam['type_id'] = $type_id;
if(preg_match("/\d\d\d\d-\d\d-\d\d/", $start_date) || preg_match("/\d\d\d\d\d\d\d\d/", $start_date))
{
    $start_date = date('Y-m-d',strtotime($start_date));
    if(strlen($where_str)>0) $where_str .= ' AND ';
    $where_str .= "DATE_FORMAT(date_time,'%Y-%m-%d')>='".mysql_escape_string($start_date)."'";
    $setParam['start_date'] = $start_date;
}
if(preg_match("/\d\d\d\d-\d\d-\d\d/", $end_date) || preg_match("/\d\d\d\d\d\d\d\d/", $end_date))
{
    $end_date = date('Y-m-d',strtotime($end_date));
    if(strlen($where_str)>0) $where_str .= ' AND ';
    $where_str .= "DATE_FORMAT(date_time,'%Y-%m-%d')<='".mysql_escape_string($end_date)."'";
    $setParam['end_date'] = $end_date;
}

if($act == 'export')//导出数据
{
    $data = array();
    $list = $pai_order_report_obj->get_order_report_list(false,$type,$type_id,$where_str,'date_time DESC,id DESC',"0,99999999");
    if(!is_array($list)) $list = array();
    foreach($list as $key=>$val)
    {
        $ret = $type_obj->get_type_info($val['type_id']);
        $data[$key]['type_name'] = trim($ret['name']);
        $data[$key]['date_key'] = $val['date_key'];
        $data[$key]['total_price'] = $val['total_price'];
        $data[$key]['total_price_hb_scala'] = $val['total_price_hb_scala'];
        $data[$key]['total_price_tb_scala'] = $val['total_price_tb_scala'];
        $data[$key]['order_num'] = $val['order_num'];
        $data[$key]['order_num_hb_scala'] = $val['order_num_hb_scala'];
        $data[$key]['order_num_tb_scala'] = $val['order_num_tb_scala'];
        $data[$key]['avg_buyer_price'] = $val['avg_buyer_price'];
        $data[$key]['avg_buyer_price_hb_scala'] = $val['avg_buyer_price_hb_scala'];
        $data[$key]['avg_buyer_price_tb_scala'] = $val['avg_buyer_price_tb_scala'];
        $data[$key]['buyer_user_count'] = $val['buyer_user_count'];
        $data[$key]['buyer_user_count_hb_scala'] = $val['buyer_user_count_hb_scala'];
        $data[$key]['buyer_user_count_tb_scala'] = $val['buyer_user_count_tb_scala'];
        $data[$key]['buyer_reply_buy_scala'] = $val['buyer_reply_buy_scala']*100 .'%';
        $data[$key]['buyer_reply_buy_scala_hb_scala'] = $val['buyer_reply_buy_scala_hb_scala'];
        $data[$key]['buyer_reply_buy_scala_tb_scala'] = $val['buyer_reply_buy_scala_tb_scala'];
        $data[$key]['keep_buy_user_scala'] = $val['keep_buy_user_scala']*100 .'%';
        $data[$key]['keep_buy_user_scala_hb_scala'] = $val['keep_buy_user_scala_hb_scala'];
        $data[$key]['keep_buy_user_scala_tb_scala'] = $val['keep_buy_user_scala_tb_scala'];
        $data[$key]['new_buy_user_scala'] = $val['new_buy_user_scala']*100 .'%';
        $data[$key]['new_buy_user_scala_hb_scala'] = $val['new_buy_user_scala_hb_scala'];
        $data[$key]['new_buy_user_scala_tb_scala'] = $val['new_buy_user_scala_tb_scala'];
        $data[$key]['seller_user_count'] = $val['seller_user_count'];
        $data[$key]['seller_user_count_hb_scala'] = $val['seller_user_count_hb_scala'];
        $data[$key]['seller_user_count_tb_scala'] = $val['seller_user_count_tb_scala'];
        $data[$key]['avg_seller_price'] = $val['avg_seller_price'];
        $data[$key]['avg_seller_price_hb_scala'] = $val['avg_seller_price_hb_scala'];
        $data[$key]['avg_seller_price_tb_scala'] = $val['avg_seller_price_tb_scala'];
        $data[$key]['user_pin_scala'] = $val['user_pin_scala']*100 .'%';
        $data[$key]['user_pin_scala_hb_scala'] = $val['user_pin_scala_hb_scala'];
        $data[$key]['user_pin_scala_tb_scala'] = $val['user_pin_scala_tb_scala'];
        $data[$key]['new_gain_seller_num'] = $val['new_gain_seller_num'];
        $data[$key]['new_gain_seller_num_hb_scala'] = $val['new_gain_seller_num_hb_scala'];
        $data[$key]['new_gain_seller_num_tb_scala'] = $val['new_gain_seller_num_tb_scala'];
        unset($ret);
    }
    unset($list);
    $log_date = date('Y_m',time());
    $fileName = "周订单统计列表_{$log_date}";
    $headArr  = array("分类","时间段","周销售总额","环比","同比","订单数","环比","同比","平均客单价","环比","同比","消费人数","环比","同比","复购率","环比","同比","消费留存率","环比","同比","消费新客率","环比","同比","收入商家数","环比","同比","商家平均收入","环比","同比","动销率","环比","同比","新增有收入商家","环比","同比");
    Excel_v2::start($headArr,$data,$fileName);
    exit;
}

$page_obj->setvar($setParam);

$total_count = $pai_order_report_obj->get_order_report_list(true,$type,$type_id,$where_str);
$page_obj->set($show_count,$total_count);
$list = $pai_order_report_obj->get_order_report_list(false,$type,$type_id,$where_str,'date_time DESC,id DESC',$page_obj->limit());
if(!is_array($list)) $list = array();
foreach($list as &$val)
{
    $ret = $type_obj->get_type_info($val['type_id']);
    $val['type_name'] = trim($ret['name']);
    $val['buyer_reply_buy_scala'] = $val['buyer_reply_buy_scala']*100;
    $val['keep_buy_user_scala'] = $val['keep_buy_user_scala']*100;
    $val['new_buy_user_scala'] = $val['new_buy_user_scala']*100;
    $val['user_pin_scala'] = $val['user_pin_scala']*100;
}
$tpl->assign($setParam);
$tpl->assign('list',$list);
$tpl->assign('type_list', $type_list);
$tpl->assign('page',$page_obj->output(true));
$tpl->assign('YUE_ADMIN_V2_ADMIN_TEST_HEADER',$_YUE_ADMIN_V2_ADMIN_TEST_HEADER);
$tpl->output();