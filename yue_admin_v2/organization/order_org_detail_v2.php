<?php
/**
 * @desc:   交易详情
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/7/8
 * @Time:   10:41
 * version: 2.0
 */
include('common.inc.php');
$user_obj      = POCO::singleton ( 'pai_user_class' );//用户
$user_icon_obj = POCO::singleton('pai_user_icon_class');//用户图片
$model_relate_org_obj = POCO::singleton('pai_model_relate_org_class');//机构
$mall_order_obj = POCO::singleton ( 'pai_mall_order_class' ); //订单
$mall_comment_obj = POCO::singleton ( 'pai_mall_comment_class' );//评价
$tpl = new SmartTemplate("order_org_detail_v2.tpl.htm");
$page_obj = new show_page ();
$show_count = 20;

$yue_login_id   = intval($yue_login_id);
$seller_user_id = intval($_INPUT['seller_user_id']);
$setParam['seller_user_id'] = $seller_user_id;

//对数据是否非法操作进行判断
if($seller_user_id <1)
{
    echo "<script type='text/javascript'>window.alert('非法操作');location.href='order_org_list_v2.php'</script>";
    exit;
}
$info = $model_relate_org_obj->get_org_model_audit_by_user_id($seller_user_id, $yue_login_id);
if(!$info)
{
    echo "<script type='text/javascript'>window.alert('非法操作');location.href='order_org_list_v2.php'</script>";
    exit;
}
$icon = $user_icon_obj->get_user_icon($seller_user_id, 100);

//获取订单列表
$where_str = "seller_user_id={$seller_user_id} AND org_user_id={$yue_login_id}";
$total_count = $mall_order_obj->get_order_full_list(0, -1, true, $where_str);
$page_obj->setvar($setParam);
$page_obj->set ( $show_count, $total_count );
$list = $mall_order_obj->get_order_full_list(0, -1, false, $where_str, 'add_time DESC,order_id DESC',$page_obj->limit());

if(!is_array($list)) $list = array();
foreach($list as $key=>$val)
{
    //$list[$key]['buyer_nickname'] = get_user_nickname_by_user_id($val['buyer_user_id']);
    $ret =  $mall_comment_obj->get_buyer_comment_info($val['order_id'],$val['detail_list'][0]['goods_id']);
    $list[$key]['seller_comment']  = trim($ret['comment']); //卖家评价买家
    $buyer_ret =  $mall_comment_obj->get_seller_comment_info($val['order_id'],$val['detail_list'][0]['goods_id']);
    $list[$key]['buyer_comment']  = trim($buyer_ret['comment']); //买家评价卖家
}

$tpl->assign('list', $list);
$tpl->assign('icon', $icon);
$tpl->assign($setParam);
$tpl->assign ( "page", $page_obj->output ( 1 ) );
$tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
$tpl->output();