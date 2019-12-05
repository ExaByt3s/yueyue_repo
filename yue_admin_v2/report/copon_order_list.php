<?php
/**
 * @desc:   优惠券对应的订单数据
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/11/4
 * @Time:   11:08
 * version: 1.0
 */
ignore_user_abort(ture);
ini_set("max_execution_time", 3600);
ini_set('memory_limit', '512M');
include_once('common.inc.php');
include_once(YUE_ADMIN_V2_CLASS_ROOT.'pai_add_coupon_class.inc.php');
$coupon_sn_obj = new pai_add_coupon_class();//优惠码统计类
$mall_order_obj = POCO::singleton( 'pai_mall_order_class' );//订单类
$pai_organization_obj = POCO::singleton("pai_organization_class");//机构库
$mall_certificate_service_obj = POCO::singleton('pai_mall_certificate_service_class');//服务审核人类
$event_order_obj = POCO::singleton('pai_event_order_report_class');//外拍和约拍订单
include_once("/disk/data/htdocs232/poco/pai/yue_admin_v2/common/Excel_v2.class.php");//导出类
$page_obj = new show_page();
$show_count = 30;

$tpl = new SmartTemplate(REPORT_TEMPLATES_ROOT."coupon_order_list.tpl.htm");


$act = trim($_INPUT['act']);
$id = (int)$_INPUT['id']; //ID
$type = trim($_INPUT['type']);

$type_arr = array('user','first_order','resell_order');
$where_str = '';
$setParam = array();

if($id <1 || !in_array($type,$type_arr)) js_pop_msg_v2('非法操作');
$setParam['id'] = $id;
$setParam['type'] = $type;
$setParam['title'] = '商城订单';

$result = $coupon_sn_obj->get_data_info_by_id($id);
if(!is_array($result)) $result = array();
if($type == 'user') $user_list = $result['user_list'] ? $result['user_list']:0;
elseif($type == 'first_order') $order_list = $result['first_order_list'] ? $result['first_order_list'] : 0 ;
elseif($type == 'resell_order') $order_list = $result['resell_order_list'] ? $result['resell_order_list'] :0 ;

if(strlen($order_list)>0)
{
    if (strlen($where_str) >0) $where_str .= ' AND ';
    $where_str .= "order_id IN ({$order_list})";
}
if(strlen($user_list)>0)
{
    if (strlen($where_str) >0) $where_str .= ' AND ';
    $where_str .= "buyer_user_id IN ({$user_list})";
}

if($act == 'export')//导出数据
{
    $data = array();
    $list = $mall_order_obj->get_order_full_list(0,8,false, $where_str,'sign_time DESC,close_time DESC,order_id DESC',"0,99999999");
    $data = array();
    foreach($list as $key=>$val)
    {
       $data[$key]['order_id'] = $val['order_id'];
       $data[$key]['order_sn'] = $val['order_sn'];
       $data[$key]['seller_name'] = $val['seller_name'];
       $data[$key]['seller_user_id'] = $val['seller_user_id'];
       $data[$key]['buyer_name'] = $val['buyer_name'];
       $data[$key]['buyer_user_id'] = $val['buyer_user_id'];
       $data[$key]['type_name'] = $val['type_name'];
       $data[$key]['goods_id'] = $val['detail_list'][0]['goods_id'];
       $data[$key]['goods_name'] = $val['detail_list'][0]['goods_name'];
       $data[$key]['total_amount'] = $val['total_amount'];
        $package_sn = get_exchange_sn_by_package_sn($val['coupon_sn']);
       $data[$key]['package_sn'] = $package_sn ? $package_sn: '--';;
       $data[$key]['coupon_sn'] = $val['coupon_sn'] ? $val['coupon_sn'] : '--';
       $data[$key]['discount_amount'] = $val['discount_amount'];
       $data[$key]['pay_time_str'] = $val['pay_time_str'];
       $data[$key]['sign_time'] = date('Y-m-d H:i:s',$val['sign_time']);
       $data[$key]['sign_time'] = date('Y-m-d H:i:s',$val['sign_time']);
       $data[$key]['service_time'] = date('Y-m-d H:i:s',$val['detail_list'][0]['service_time']);
       $data[$key]['service_address'] = $val['detail_list'][0]['service_address'];
       $data[$key]['status_str2'] = $val['status_str2'];
       $data[$key]['org_user_name'] = $val['org_user_id'] == 0 ? '无' : $pai_organization_obj->get_org_name_by_user_id($v['org_user_id']);;
       $data[$key]['audit_name'] = $mall_certificate_service_obj->get_user_option_name($val['type_id'],$val['seller_user_id']);
    }
    unset($list);
    $fileName = '订单详情';
    //$title    = '订单详情列表';
    $headArr  = array("订单ID","订单编号","商家昵称","商家ID","买家昵称","买家ID","商品品类","商品ID","商品名","交易额","套餐码","优惠码","优惠券金额","付款时间","签到时间","服务时间","服务地点","订单状态","商家机构归属","商家审核人员");
    Excel_v2::start($headArr,$data,$fileName);
    exit;
}

$page_obj->setvar($setParam);
$total_count = $mall_order_obj->get_order_full_list(0,8,true,$where_str);
$page_obj->set($show_count,$total_count);

$list = $mall_order_obj->get_order_full_list(0,8,false, $where_str,'sign_time DESC,close_time DESC,order_id DESC',$page_obj->limit(), $fields='*');
if(!is_array($list)) $list = array();
foreach($list as &$v)
{
    $v['detail_list'][0]['service_address_cut'] = poco_cutstr($v['detail_list'][0]['service_address'], 20, '....');
    $v['org_user_name'] = $v['org_user_id'] == 0 ? '无' : $pai_organization_obj->get_org_name_by_user_id($v['org_user_id']);
    $v['audit_name'] = $mall_certificate_service_obj->get_user_option_name($v['type_id'],$v['seller_user_id']);
    $package_sn = get_exchange_sn_by_package_sn($v['coupon_sn']);
    $v['package_sn']  = $package_sn ? $package_sn: '--';
}
/*if($yue_login_id == 100293)
{
    print_r($list);
}*/

//获取套餐码
function get_exchange_sn_by_package_sn($coupon_sn)
{
    $coupon_sn = trim($coupon_sn);
    if(strlen($coupon_sn)<1) return '--';
    $sql_str = "SELECT E.package_sn FROM pai_coupon_db.coupon_exchange_ref_coupon_tbl C,pai_coupon_db.coupon_exchange_tbl AS E WHERE E.exchange_id=C.exchange_id AND C.coupon_sn ='{$coupon_sn}'";
    $result = db_simple_getdata($sql_str,true,101);
    if(!is_array($result)) $result = array();
    return trim($result['package_sn']);
}

$tpl->assign($setParam);
$tpl->assign('list',$list);
$tpl->assign('page',$page_obj->output(true));
$tpl->output();



