<?php 

/* 
 * 未结算
 *xiao xiao
 * 2014-1-28
*/
  include('common.inc.php');
  include('include/common_function.php');
  $page_obj = new show_page ();
  $show_count = 20;
  $payment_obj = POCO::singleton('pai_payment_class');
  $date_obj  = POCO::singleton ( 'event_date_class' );
  $event_obj = POCO::singleton('event_details_class');
  $cameraman_comment_log_obj = POCO::singleton('pai_cameraman_comment_log_class');
  $model_comment_log_obj = POCO::singleton('pai_model_comment_log_class');
  $coupon_obj = POCO::singleton('pai_coupon_class');
  $act = $_INPUT['act'] ? $_INPUT['act'] : 'yuepai';
  $list  = array();
  $price = 0;
  //约拍
  $page_obj->setvar(array('act' => $act));

  //yuepai个数
  $yuepai_count = $payment_obj->get_unsettle_trade_list($yue_login_id, $channel_module = 'yuepai',true);
  $waipai_count = $payment_obj->get_unsettle_trade_list($yue_login_id, $channel_module = 'waipai',true);
  if ($act == 'yuepai') 
  {
      $tpl = new SmartTemplate("open_billing_yuepai.tpl.htm");

      $yuepai_cash_price = $payment_obj->get_unsettle_org_amount($yue_login_id, 'yuepai');
      //获取券兑现金额
      $yuepai_coupon_price = 0;
      $event_id_arr = $payment_obj->get_unsettle_trade_event_id_arr($yue_login_id, 'yuepai');
      if( !empty($event_id_arr) )
      {
        $event_id_str = implode(',', $event_id_arr);
        $yuepai_coupon_price = $coupon_obj->get_unsettle_org_amount($yue_login_id, '', "event_id IN ({$event_id_str})");
      }
      $price = $yuepai_cash_price + $yuepai_coupon_price;
      //$total_count = $payment_obj->get_unsettle_trade_list($yue_login_id, $channel_module = 'yuepai',true);
      $page_obj->set ( $show_count, $yuepai_count );
      $list = $payment_obj->get_unsettle_trade_list($yue_login_id, $channel_module = 'yuepai',false, '', $order_by = 'trade_id', $page_obj->limit(), $fields = '*');
      //print_r($list);exit;
      if ($list) 
      {
        foreach ($list as $key => $vo) 
        {
            $event_id = $vo['event_id'];
            //var_dump($event_id);exit;
            
            $coupon_amount = $coupon_obj->get_unsettle_org_amount($yue_login_id, '', "event_id={$event_id}");
            $list[$key]['total_org_amount'] = $vo['org_amount'] + $coupon_amount;
            
            $list_data = $date_obj->get_all_event_date (false, "event_id={$event_id}");
            $event_data = $event_obj-> get_event_by_event_id($event_id);
            $list[$key]['event_status']     = $event_data['event_status'];
            $list[$key]['date_id'] = $list_data[0]['date_id'];
            $list[$key]['cameraman_nickname'] = get_user_nickname_by_user_id($list_data[0]['from_date_id']);
            $list[$key]['model_nickname']     = get_user_nickname_by_user_id($list_data[0]['to_date_id']);
            //$list[$key]['model_nickname']     = get_user_nickname_by_user_id($list_data[0]['to_date_id']);
            $list[$key]['date_style']          = $list_data[0]['date_style'];
            //$list[$key]['date_price']         = $list_data[0]['date_price'];
            $list[$key]['date_time']          = date('Y-m-d H:i:s',$list_data[0]['date_time']);
            $list[$key]['date_address']       = $list_data[0]['date_address'];
            $list[$key]['date_status']        = $list_data[0]['date_status'];
            $list[$key]['source']             = $list_data[0]['source'];
            $model_coment_info     = $model_comment_log_obj->get_model_comment_by_date_id($list_data[0]['date_id']);
            $cameraman_coment_info = $cameraman_comment_log_obj->get_cameraman_comment_by_date_id($list_data[0]['date_id']);
          //print_r($model_coment_info);exit;
          $list[$key]['model_coment_text']  = $model_coment_info['comment'];
          $list[$key]['cameraman_coment_text']  = $cameraman_coment_info['comment'];
          unset($list_data);
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
     //$total_count = $payment_obj->get_unsettle_trade_list($yue_login_id, $channel_module = 'waipai',true);
     $page_obj->set ( $show_count, $waipai_count );
     $list = $payment_obj->get_unsettle_trade_list($yue_login_id, $channel_module = 'waipai',false, '', $order_by = 'trade_id', $page_obj->limit(), $fields = '*');
     if ($list) 
     {
       foreach ($list as $key => $vo) 
       {
         $event_id = $vo['event_id'];

         $coupon_amount = $coupon_obj->get_unsettle_org_amount($yue_login_id, '', "event_id={$event_id}");
         $list[$key]['total_org_amount'] = $vo['org_amount'] + $coupon_amount;
         //var_dump($event_id);
         //$list_data = $date_obj->get_all_event_date (false, "event_id={$event_id}");
         $event_data = $event_obj->get_event_by_event_id($event_id);
         //print_r($event_data);
         $list[$key]['title']        = $event_data['title'];
         $list[$key]['event_status'] = $event_data['event_status'];
         $list[$key]['date_address'] = $event_data['address'];
         //$list[$key]['date_price']  = $list_data[0]['date_price'];
         $list[$key]['date_time']   = date('Y-m-d H:i:s',$event_data['start_time']);
         //$list[$key]['date_status'] = $list_data[0]['date_status'];
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