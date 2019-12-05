<?php
/**
 * @desc:   已结算内容
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/7/7
 * @Time:   15:45
 * version: 2.0
 */
include('common.inc.php');
$payment_obj = POCO::singleton('pai_payment_class');//结算类
$date_obj  = POCO::singleton ( 'event_date_class' );//订单类
$event_obj = POCO::singleton('event_details_class');//总订单类
$date_obj  = POCO::singleton ( 'event_date_class' );
$event_obj = POCO::singleton('event_details_class');
$cameraman_comment_log_obj = POCO::singleton('pai_cameraman_comment_log_class');  //评价
$model_comment_log_obj = POCO::singleton('pai_model_comment_log_class');
$mall_order_obj = POCO::singleton ( 'pai_mall_order_class' ); //商城订单
$mall_comment_obj = POCO::singleton( 'pai_mall_comment_class' );//商城评价

$page_obj = new show_page ();
$show_count = 20;

$act = trim($_INPUT['act']) ? trim($_INPUT['act']) :'mall_order';
$settle_id = intval($_INPUT['settle_id']);
if ($settle_id <1)
{
    echo "<script type='text/javascript'>window.alert('非法操作');location.href='close_billing_list.php'</script>";
    exit;
}
$settle_info = $payment_obj->get_settle_info($settle_id);
if ($yue_login_id != $settle_info['org_user_id'])
{
    echo "<script type='text/javascript'>window.alert('非法操作');location.href='close_billing_list.php'</script>";
    exit;
}
$setParam = array();
$setParam['act'] = $act;
$setParam['settle_id'] = $settle_id;

$page_obj->setvar($setParam);

//个数
$mall_count = $payment_obj->get_settle_ref_trade_list($yue_login_id, $settle_id,'mall_order',true);
$yuepai_count = $payment_obj->get_settle_ref_trade_list($yue_login_id, $settle_id,'yuepai',true);
$waipai_count = $payment_obj->get_settle_ref_trade_list($yue_login_id, $settle_id,'waipai',true);
if ($act == 'mall_order')//商家部分
{
    $tpl = new SmartTemplate("close_billing_yuepai_v2.tpl.htm");

    $org_amount_info = $payment_obj->get_settle_org_amount_info($settle_id,'mall_order');
    $price = $org_amount_info['total_org_amount']*1;
    $page_obj->set ( $show_count, $mall_count );
    $list = $payment_obj->get_settle_ref_trade_list($yue_login_id, $settle_id,'mall_order',false, '','event_id DESC,user_id DESC,id ASC', $page_obj->limit(), $fields = '*');
      if ($list) 
      {
        foreach ($list as $key => $vo) 
        {
            $event_id = $vo['event_id'];
            $ret = $mall_order_obj->get_order_full_info_by_id($event_id);
            $list[$key]['status_str'] = trim($ret['status_str']);
            $list[$key]['order_sn']   = intval($ret['order_sn']);
            $list[$key]['goods_name']   = trim($ret['detail_list'][0]['goods_name']);
            $list[$key]['seller_nickname'] = get_user_nickname_by_user_id($vo['user_id']);
            $list[$key]['buyer_nickname']  = get_user_nickname_by_user_id($ret['buyer_user_id']);
            $list[$key]['type_name'] = trim($ret['type_name']);
            $list[$key]['service_address'] = trim($ret['detail_list'][0]['service_address']);
            $list[$key]['service_time']    = trim($ret['detail_list'][0]['service_time']);
            $seller_comment_ret = $mall_comment_obj->get_buyer_comment_info($event_id,$ret['detail_list'][0]['goods_id']);
            $buyer_comment_ret  = $mall_comment_obj->get_seller_comment_info($event_id,$ret['detail_list'][0]['goods_id']);
            $list[$key]['seller_comment']  = trim($seller_comment_ret['comment']);
            $list[$key]['buyer_comment']   = trim($buyer_comment_ret['comment']);
        }
      }
}
elseif($act == 'yuepai')//旧的约拍部分
{
    $tpl = new SmartTemplate("close_billing_yuepai.tpl.htm");
    $org_amount_info = $payment_obj->get_settle_org_amount_info($settle_id,'yuepai');
    $price = $org_amount_info['total_org_amount']*1;
    $page_obj->set ( $show_count, $yuepai_count );
    $list = $payment_obj->get_settle_ref_trade_list($yue_login_id, $settle_id,'yuepai',false, '','event_id DESC,user_id DESC,id ASC', $page_obj->limit(), $fields = '*');
    if ($list)
    {
        foreach ($list as $key => $vo)
        {
            $event_id = $vo['event_id'];
            $list_data = $date_obj->get_all_event_date (false, "event_id={$event_id}");
            $event_data = $event_obj-> get_event_by_event_id($event_id);
            $list[$key]['event_status']     = $event_data['event_status'];
            $list[$key]['date_id']          = $list_data[0]['date_id'];
            $list[$key]['cameraman_nickname'] = get_user_nickname_by_user_id($list_data[0]['from_date_id']);
            $list[$key]['model_nickname']     = get_user_nickname_by_user_id($list_data[0]['to_date_id']);
            $list[$key]['date_style']          = $list_data[0]['date_style'];
            $list[$key]['date_time']          = date('Y-m-d H:i:s',$list_data[0]['date_time']);
            $list[$key]['date_address']       = $list_data[0]['date_address'];
            $list[$key]['date_status']        = $list_data[0]['date_status'];
            $model_coment_info     = $model_comment_log_obj->get_model_comment_by_date_id($list_data[0]['date_id']);
            $cameraman_coment_info = $cameraman_comment_log_obj->get_cameraman_comment_by_date_id($list_data[0]['date_id']);
            $list[$key]['model_coment_text']  = $model_coment_info['comment'];
            $list[$key]['cameraman_coment_text']  = $cameraman_coment_info['comment'];
        }
    }
}
else
{
    $tpl = new SmartTemplate("close_billing_waipai.tpl.htm");
    //外拍
    $org_amount_info = $payment_obj->get_settle_org_amount_info($settle_id, $channel_module = 'waipai');
    $price = $org_amount_info['total_org_amount']*1;
    //$payment_obj->get_unsettle_org_amount($yue_login_id, 'waipai');
    $page_obj->set ( $show_count, $waipai_count );
    $list = $payment_obj->get_settle_ref_trade_list($yue_login_id, $settle_id, $channel_module = 'waipai',false, '', 'event_id DESC,user_id DESC,id ASC', $page_obj->limit(), $fields = '*');
    if ($list)
    {
        foreach ($list as $key => $vo)
        {
            $event_id = $vo['event_id'];
            $event_data = $event_obj->get_event_by_event_id($event_id);
            $list[$key]['title']        = $event_data['title'];
            $list[$key]['event_status'] = $event_data['event_status'];
            $list[$key]['date_address'] = $event_data['address'];
            $list[$key]['date_time']   = date('Y-m-d H:i:s',$event_data['start_time']);
        }
    }
}
$tpl->assign('mall_count', $mall_count);
$tpl->assign('yuepai_count', $yuepai_count);
$tpl->assign('waipai_count', $waipai_count);
$tpl->assign($settle_info);
$tpl->assign('price', $price);
$tpl->assign('list', $list);
$tpl->assign($setParam);
$tpl->assign ( "page", $page_obj->output ( 1 ) );
$tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
$tpl->output();
