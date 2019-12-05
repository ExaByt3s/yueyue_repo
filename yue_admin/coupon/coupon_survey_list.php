<?php
/**
 *商城优惠券、约拍、外拍券使用概况
 * @authors xiao xiao (xiaojm@yueus.com)
 * @date    2015-03-30 16:09:29
 * @version 1
 */

include_once ('common.inc.php');
//后台常用函数
include_once("/disk/data/htdocs232/poco/pai/yue_admin/common/yue_function.php");
//优惠券类
$coupon_obj = POCO::singleton('pai_coupon_class');
//订单类
$order_obj = POCO::singleton('pai_order_org_class');

$tpl = new SmartTemplate('coupon_survey_list.tpl.htm');


$channel_module = trim($_INPUT['channel_module']) ? trim($_INPUT['channel_module']) : 'mall_order';
//默认为这个月的明细
$begin_time = $_INPUT['begin_time'] ? trim($_INPUT['begin_time']): date('Y-m-d',mktime(0,0,0,date('m'),1,date('Y')));
$end_time   = $_INPUT['end_time']  ? trim($_INPUT['end_time']) : date('Y-m-d', mktime(0,0,0,date('m'),date('t'),date('Y')));

$list = $coupon_obj->get_stat_cash_list($channel_module, strtotime($begin_time)
    , strtotime($end_time)+24*3600);
//总计
if(!is_array($list)) $list = array();
$ret = array();
foreach ($list as $key => $vo)
{
    //获取订单金额数据
    $where_order_str = "channel_module = '".mysql_escape_string($channel_module)."' AND  is_cash=1 AND FROM_UNIXTIME(cash_time,'%Y-%m-%d') = '{$vo['cash_date']}'";
    $order_total     = $coupon_obj->get_ref_order_list(true, $where_order_str);
    $order_list      = $coupon_obj->get_ref_order_list(false, $where_order_str, 'cash_time DESC,id DESC', '0,{$order_total}','channel_oid');
    if(!is_array($order_list)) $order_list = array();
    $where_in_str = '';
    foreach ($order_list as $order_key => $val)
    {
        if($order_key != 0) $where_in_str .= ',';
        $where_in_str .= "{$val['channel_oid']}";
    }
    $price = 0;
    if($channel_module == 'yuepai')
    {

        if(strlen($where_in_str) > 0)
        {
            $where_tmp_str = "date_id IN ({$where_in_str})";
            $price  = $order_obj->get_yuepai_price_by_where_str($where_tmp_str);
        }

    }
    elseif($channel_module == 'mall_order')//获取价格
    {
        if(strlen($where_in_str)>0)
        {
            $sql_str = "SELECT SUM(total_amount) AS price FROM mall_db.mall_order_tbl WHERE order_id IN ({$where_in_str}) AND status=8";
            $ret = db_simple_getdata($sql_str, true, 101);
            if(!is_array($ret)) $ret = array();
            $price = $ret['price'] *1;
            unset($ret);
        }
    }
    else
    {
        if(strlen($where_in_str) > 0)
        {
            $where_tmp_str = "enroll_id IN ({$where_in_str})";
            $price  = $order_obj->get_waipai_price_by_where_str($where_tmp_str);
        }
    }
    unset($order_list);
    //获取订单金额数据 END
    $list[$key]['channel_module'] = $channel_module;
    $list[$key]['price'] = $price;
    $ret['uv_total'] += $vo['uv'];
    $ret['pv_total'] += $vo['pv'];
    $ret['total_price'] += $price;
    $ret['cash_amount_total'] += $vo['cash_amount'];
}
$ret['total_price'] = sprintf('%.2f', $ret['total_price']);
$ret['cash_amount_total'] = sprintf('%.2f', $ret['cash_amount_total']);

$tpl->assign($ret);
$tpl->assign('channel_module', $channel_module);
$tpl->assign('begin_time', $begin_time);
$tpl->assign('end_time', $end_time);
$tpl->assign('list', $list);
$tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
$tpl->output();