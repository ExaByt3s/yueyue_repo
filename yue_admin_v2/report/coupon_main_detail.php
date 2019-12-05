<?php
/**
 * @desc:   优惠券的
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/11/2
 * @Time:   17:06
 * version: 1.0
 */
include_once('common.inc.php');
check_auth($yue_login_id,'coupon_main_detail');//权限控制
include_once(YUE_ADMIN_V2_CLASS_ROOT.'pai_add_coupon_class.inc.php');
$coupon_sn_obj = new pai_add_coupon_class();
//$page_obj = new show_page();
//$show_total = 30;

$tpl = new SmartTemplate( REPORT_TEMPLATES_ROOT.'coupon_main_detail.tpl.htm' );

//$act = trim($_INPUT['act']);
$id = (int)$_INPUT['id'];
if($id <1) js_pop_msg_v2('非法操作');
$main_result = $coupon_sn_obj->get_coupon_by_main_id($id);
$ret = $coupon_sn_obj->get_data_info_by_id($id);
if(!is_array($ret)) $ret = array();
if($ret)
{
    //登录行为
    $login_num=$ret['login_1_num']+$ret['login_2_num']+$ret['login_3_num']+$ret['login_4_num']+$ret['login_5_up_num'];
    $ret['login_1_num_scala'] = sprintf('%.2f',($ret['login_1_num']/$login_num*100));
    $ret['login_2_num_scala'] = sprintf('%.2f',($ret['login_2_num']/$login_num*100));
    $ret['login_3_num_scala'] = sprintf('%.2f',($ret['login_3_num']/$login_num*100));
    $ret['login_4_num_scala'] = sprintf('%.2f',($ret['login_4_num']/$login_num*100));
    $ret['login_5_up_scala'] = sprintf('%.2f',($ret['login_5_up_num']/$login_num*100));
    //$ret['login_num'] = $login_num;
    //兑换行为计算
    $ret['trade_to_order_scala'] = sprintf('%.2f',($ret['first_total_user_num']/$ret['trade_num']*100));
    //二次购买比例
    $ret['resell_to_user_scala'] = sprintf('%.2f',($ret['resell_use_coupon_num']/$ret['first_total_user_num']*100));
}

//列表改为详情页
/*//参数整理
$where_str = '';
$setParam = array();

if($id >0) $setParam['id'] = $id;

//数据查询
$page_obj->setvar($setParam);
$total_count = $coupon_sn_obj->get_coupon_data_list(true,$id,$where_str);
$page_obj->set($show_total,$total_count);

$list = $coupon_sn_obj->get_coupon_data_list(false,$id,$where_str,"id DESC",$page_obj->limit());
if(!is_array($list)) $list = array();

$title = '优惠券使用详情';
if(strlen($title)>0) $setParam['title'] = $title;*/

$tpl->assign($main_result);
$tpl->assign($ret);
$tpl->output();
