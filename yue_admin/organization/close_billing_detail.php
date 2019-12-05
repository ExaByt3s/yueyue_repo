<?php 

/* 
 * 已结算内容
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
  $act = $_INPUT['act'] ? $_INPUT['act'] : 'yuepai';
  $settle_id = $_INPUT['settle_id'] ? intval($_INPUT['settle_id']) : 0;
  if (empty($settle_id)) 
  {

   echo "<script type='text/javascript'>window.alert('非法操作');location.href='close_billing_list.php'</script>";
    exit;
  }
  $settle_info = $payment_obj->get_settle_info($settle_id);
  if ($yue_login_id != $settle_info['org_user_id']) 
  {
    //echo "skskks";exit;
     echo "<script type='text/javascript'>window.alert('非法操作');location.href='close_billing_list.php'</script>";
    exit;
  }
  //echo "sjsjjs";
  //var_dump($settle_info);
  //约拍
  $page_obj->setvar(array('act' => $act, 'settle_id' => $settle_id));
  //个数
  $yuepai_count = $payment_obj->get_settle_ref_trade_list($yue_login_id, $settle_id, $channel_module = 'yuepai',true);
  $waipai_count = $payment_obj->get_settle_ref_trade_list($yue_login_id, $settle_id, $channel_module = 'waipai',true);
  if ($act == 'yuepai') 
  {
      $tpl = new SmartTemplate("close_billing_yuepai.tpl.htm");
      //$payment_obj->get_settle_ref_trade_list($org_user_id, $settle_id=0, $channel_module='', $b_select_count=false, $where_str='', $order_by='', $limit='0,20', $fields='*')
      //$total_count = $payment_obj->get_settle_ref_trade_list($yue_login_id, $settle_id, $channel_module = 'yuepai', true);
      $org_amount_info = $payment_obj->get_settle_org_amount_info($settle_id, $channel_module = 'yuepai');
      $price = $org_amount_info['total_org_amount']*1;
      $page_obj->set ( $show_count, $yuepai_count );
      $list = $payment_obj->get_settle_ref_trade_list($yue_login_id, $settle_id, $channel_module = 'yuepai',false, '', $order_by = 'user_id DESC,id ASC', $page_obj->limit(), $fields = '*');
      //var_dump($list);
      if ($list) 
      {
        foreach ($list as $key => $vo) 
        {
            $event_id = $vo['event_id'];
            $list_data = $date_obj->get_all_event_date (false, "event_id={$event_id}");
            $event_data = $event_obj-> get_event_by_event_id($event_id);
            //var_dump($event_data);
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
      $list = $payment_obj->get_settle_ref_trade_list($yue_login_id, $settle_id, $channel_module = 'waipai',false, '', $order_by = 'user_id DESC,id ASC', $page_obj->limit(), $fields = '*');
     //$list = $payment_obj->get_settle_ref_trade_list($yue_login_id, $channel_module = 'waipai',false, '', $order_by = 'trade_id', $limit='0,1000', $fields = '*');
     if ($list) 
     {
       foreach ($list as $key => $vo) 
       {
         $event_id = $vo['event_id'];
         $event_data = $event_obj->get_event_by_event_id($event_id);
         //$list_data = $date_obj->get_all_event_date (false, "event_id={$event_id}");
         $list[$key]['title']        = $event_data['title'];
         $list[$key]['event_status'] = $event_data['event_status'];
         $list[$key]['date_address'] = $event_data['address'];
         //$list[$key]['date_price']  = $list_data[0]['date_price'];
         $list[$key]['date_time']   = date('Y-m-d H:i:s',$event_data['start_time']);
       }
     }
      
  }
  $tpl->assign('yuepai_count', $yuepai_count);
  $tpl->assign('waipai_count', $waipai_count);
  $tpl->assign($settle_info);
  $tpl->assign('settle_id', $settle_id);
  $tpl->assign('price', $price);
  $tpl->assign('list', $list);
  $tpl->assign('act', $act);
  $tpl->assign ( "page", $page_obj->output ( 1 ) );
  $tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
  $tpl->output();



 ?>