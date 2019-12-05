<?php 

/* 
 * 结算详情
 *xiao xiao
 * 2014-1-28
*/
  include('common.inc.php');
  include('include/common_function.php');
  $tpl = new SmartTemplate("billing_detail.tpl.htm");
  //$model_relate_org_obj = POCO::singleton ( 'pai_model_relate_org_class' );
  $payment_obj = POCO::singleton('pai_payment_class');
  //优惠券类
  $coupon_obj = POCO::singleton('pai_coupon_class');
  //获取所有机构用户
  //私拍价格
  $yuepai_price = 0;
  //外拍价格
  $waipai_price = 0;
  //总价格
  $total_price  = 0;

  $yuepai_cash_price = $payment_obj->get_unsettle_org_amount($yue_login_id, 'yuepai');
  //获取券兑现金额
  $yuepai_coupon_price = 0;
  $event_id_arr = $payment_obj->get_unsettle_trade_event_id_arr($yue_login_id, 'yuepai');
  if( !empty($event_id_arr) )
  {
    $event_id_str = implode(',', $event_id_arr);
    $yuepai_coupon_price = $coupon_obj->get_unsettle_org_amount($yue_login_id, '', "event_id IN ({$event_id_str})");
  }
  $yuepai_price = $yuepai_cash_price + $yuepai_coupon_price;


  $waipai_cash_price = $payment_obj->get_unsettle_org_amount($yue_login_id, 'waipai');
  //获取券兑现金额
  $waipai_coupon_price = 0;
  $event_id_arr = $payment_obj->get_unsettle_trade_event_id_arr($yue_login_id, 'waipai');
  if( !empty($event_id_arr) )
  {
    $event_id_str = implode(',', $event_id_arr);
    $waipai_coupon_price = $coupon_obj->get_unsettle_org_amount($yue_login_id, '', "event_id IN ({$event_id_str})");
  }
  $waipai_price = $waipai_cash_price + $waipai_coupon_price;

  $cash_price  = $payment_obj->get_unsettle_org_amount($yue_login_id);
  //获取券兑现金额
  $coupon_price = 0;
  $event_id_arr = $payment_obj->get_unsettle_trade_event_id_arr($yue_login_id);
  if( !empty($event_id_arr) )
  {
    $event_id_str = implode(',', $event_id_arr);
    $coupon_price = $coupon_obj->get_unsettle_org_amount($yue_login_id, '', "event_id IN ({$event_id_str})");
  }

  //总的未结算金额
  $total_price = $cash_price + $coupon_price;

  $tpl->assign('yuepai_price', $yuepai_price);
  $tpl->assign('waipai_price', $waipai_price);
  $tpl->assign('total_price',  $total_price);
  $tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
  $tpl->output();



 ?>