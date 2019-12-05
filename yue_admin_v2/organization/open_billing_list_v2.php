<?php

/**
 * @desc:   未结算
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/7/7
 * @Time:   14:42
 * version: 2.0
 */
include('common.inc.php');
include('include/common_function.php');
$payment_obj = POCO::singleton('pai_payment_class');
$date_obj    = POCO::singleton ( 'event_date_class' );
$event_obj   = POCO::singleton('event_details_class');
$cameraman_comment_log_obj = POCO::singleton('pai_cameraman_comment_log_class');
$model_comment_log_obj = POCO::singleton('pai_model_comment_log_class');
$mall_order_obj = POCO::singleton ( 'pai_mall_order_class' ); //商城订单
$coupon_obj = POCO::singleton('pai_coupon_class'); //优惠券类
$mall_comment_obj = POCO::singleton( 'pai_mall_comment_class' );//商城评价
$page_obj = new show_page ();
$show_count = 20;


$act = trim($_INPUT['act']);

//初始化
$setParam = array();
$list  = array();
$price = 0;

if(strlen($act) >0) $setParam['act'] = $act;

$page_obj->setvar($setParam);
//个数
$yuepai_count = $payment_obj->get_unsettle_trade_list($yue_login_id, $channel_module = 'mall_order',true);
$waipai_count = $payment_obj->get_unsettle_trade_list($yue_login_id, $channel_module = 'waipai',true);
//商城部分
if ($act == 'mall_order')
{
      $tpl = new SmartTemplate("open_billing_yuepai_v2.tpl.htm");

      $yuepai_cash_price = $payment_obj->get_unsettle_org_amount($yue_login_id, 'mall_order');
      //获取券兑现金额
      $yuepai_coupon_price = 0;
      $event_id_arr = $payment_obj->get_unsettle_trade_event_id_arr($yue_login_id, 'mall_order');
      if( !empty($event_id_arr) )
      {
        $event_id_str = implode(',', $event_id_arr);
        $yuepai_coupon_price = $coupon_obj->get_unsettle_org_amount($yue_login_id, 'mall_order', "event_id IN ({$event_id_str})");
      }
      $price = $yuepai_cash_price + $yuepai_coupon_price;
      $page_obj->set ( $show_count, $yuepai_count );
      $list = $payment_obj->get_unsettle_trade_list($yue_login_id,'mall_order',false, '','add_time DESC,trade_id DESC',$page_obj->limit(), $fields = '*');
      if ($list) 
      {
        foreach ($list as $key => $vo) 
        {
            $event_id = $vo['event_id'];
            $coupon_amount = $coupon_obj->get_unsettle_org_amount($yue_login_id, 'mall_order', "event_id={$event_id}");
            $list[$key]['total_org_amount'] = $vo['org_amount'] + $coupon_amount;
            $ret = $mall_order_obj->get_order_full_info_by_id($event_id);
            $list[$key]['status_str'] = trim($ret['status_str']);
            $list[$key]['order_sn']   = intval($ret['order_sn']);
			$list[$key]['type_name']   = trim($ret['type_name']);
            $list[$key]['goods_name']   = trim($ret['detail_list'][0]['goods_name']);
            $list[$key]['seller_nickname'] = get_user_nickname_by_user_id($vo['user_id']);
            $list[$key]['buyer_nickname']  = get_user_nickname_by_user_id($ret['buyer_user_id']);
            $list[$key]['service_address'] = trim($ret['detail_list'][0]['service_address']);
            $list[$key]['service_time']    = trim($ret['detail_list'][0]['service_time']);
            $seller_comment_ret = $mall_comment_obj->get_buyer_comment_info($event_id,$ret['detail_list'][0]['goods_id']);
            $buyer_comment_ret  = $mall_comment_obj->get_seller_comment_info($event_id,$ret['detail_list'][0]['goods_id']);
            $list[$key]['seller_comment']  = trim($seller_comment_ret['comment']);
            $list[$key]['buyer_comment']   = trim($buyer_comment_ret['comment']);
        }
      }
  
}
  else
  {
     
     $tpl = new SmartTemplate("open_billing_waipai.tpl.htm");
     //外拍
     $waipai_cash_price = $payment_obj->get_unsettle_org_amount($yue_login_id, 'waipai');
     //获取券兑现金额
     $waipai_coupon_price = 0;
     $event_id_arr = $payment_obj->get_unsettle_trade_event_id_arr($yue_login_id, 'waipai');
     if( !empty($event_id_arr) )
     {
       $event_id_str = implode(',', $event_id_arr);
       $waipai_coupon_price = $coupon_obj->get_unsettle_org_amount($yue_login_id, '', "event_id IN ({$event_id_str})");
     }
     $price = $waipai_cash_price + $waipai_coupon_price;
      $page_obj->set ( $show_count, $waipai_count );
     $list = $payment_obj->get_unsettle_trade_list($yue_login_id, $channel_module = 'waipai',false, '', 'add_time DESC,trade_id DESC',$page_obj->limit(), $fields = '*');
     if ($list) 
     {
       foreach ($list as $key => $vo) 
       {
         $event_id = $vo['event_id'];

         $coupon_amount = $coupon_obj->get_unsettle_org_amount($yue_login_id, '', "event_id={$event_id}");
         $list[$key]['total_org_amount'] = $vo['org_amount'] + $coupon_amount;
         $event_data = $event_obj->get_event_by_event_id($event_id);
         //print_r($event_data);
         $list[$key]['title']        = $event_data['title'];
         $list[$key]['event_status'] = $event_data['event_status'];
         $list[$key]['date_address'] = $event_data['address'];
         $list[$key]['date_time']   = date('Y-m-d H:i:s',$event_data['start_time']);
       }
     }
      
  }
  $tpl->assign('yuepai_count', $yuepai_count);
  $tpl->assign('waipai_count', $waipai_count);
  $tpl->assign('price', $price);
  $tpl->assign('list', $list);
  $tpl->assign('act', $act);
  $tpl->assign ( "page", $page_obj->output ( 1 ) );
  $tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
  $tpl->output();



 ?>