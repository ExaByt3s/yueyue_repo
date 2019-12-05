<?php
/**
 * @desc:   交易详情
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/7/6
 * @Time:   16:01
 * version: 2.0
 */
include('common.inc.php');
$user_obj      = POCO::singleton ( 'pai_user_class' );//用户
$user_icon_obj = POCO::singleton('pai_user_icon_class');//用户图片
$order_obj     = POCO::singleton ( 'pai_order_org_class' );//机构订单
$model_relate_org_obj = POCO::singleton('pai_model_relate_org_class');//机构
$cameraman_comment_log_obj = POCO::singleton('pai_cameraman_comment_log_class');//摄影师评价
$model_comment_log_obj = POCO::singleton('pai_model_comment_log_class');//模特评价
$event_obj = POCO::singleton('event_details_class');//活动
$activity_code_obj = POCO::singleton ( 'pai_activity_code_class' );//外拍签到
$page_obj = new show_page ();
$show_count = 20;
$tpl = new SmartTemplate("order_org_detail.tpl.htm");

$user_id = intval($_INPUT['user_id']);
$yue_login_id = intval($yue_login_id);

$setParam = array();

if($user_id <1)
{
    echo "<script type='text/javascript'>window.alert('非法操作');location.href='order_org_list.php'</script>";
    exit;
}
$info = $model_relate_org_obj->get_org_model_audit_by_user_id($user_id, $yue_login_id);
if(!$info)
{
    echo "<script type='text/javascript'>window.alert('非法操作');location.href='order_org_list.php'</script>";
    exit;
}
$setParam['user_id'] = $user_id;

$page_obj->setvar($setParam);
$icon = $user_icon_obj->get_user_icon($user_id, 100);
$total_count = $order_obj->get_order_list_by_user_id($user_id,$yue_login_id, '', $order_by ='', $fields= '*', true);
$page_obj->set ( $show_count, $total_count );
$list      = $order_obj->get_order_list_by_user_id($user_id,$yue_login_id, $page_obj->limit(),'date_time DESC');
if(!is_array($list)) $list = array();
foreach ($list as $key => $vo)
{
    $list[$key]['cameraman_nickname'] = get_user_nickname_by_user_id($vo['from_date_id']);
    $list[$key]['date_time']          = date('Y-m-d H:i:s', $vo['date_time']);
    $model_coment_info     = $model_comment_log_obj->get_model_comment_by_date_id($vo['date_id']);
    $cameraman_coment_info = $cameraman_comment_log_obj->get_cameraman_comment_by_date_id($vo['date_id']);
    //print_r($model_coment_info);exit;
    $list[$key]['model_coment_text']  = $model_coment_info['comment'];
    $list[$key]['cameraman_coment_text']  = $cameraman_coment_info['comment'];
    $event_data = $event_obj-> get_event_by_event_id($vo['event_id']);
    $list[$key]['event_status']     = $event_data['event_status'];
    if($vo['enroll_id'])
    {
        $is_checked = $activity_code_obj->check_code_scan ($vo['enroll_id']);
    }
    if ($is_checked)
    {
        $list[$key] ['is_checked'] = "已签到";
    }
    else
    {
        $list[$key] ['is_checked'] = "未签到";
    }
    unset($is_checked);
}
$tpl->assign('list', $list);
$tpl->assign('icon', $icon);
$tpl->assign ( "page", $page_obj->output ( 1 ) );
$tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
$tpl->output();

