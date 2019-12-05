<?php 

/* 
 * 交易详情
 *xiao xiao
 * 2014-1-20
*/
  include('common.inc.php');
  //查看权限
  //check_authority_by_list($ret_type = 'exit_type',$authority_list, 'organization', 'is_select');
  include('include/common_function.php');
  $page_obj = new show_page ();
  $show_count = 20;
  $tpl = new SmartTemplate("order_org_detail.tpl.htm");
  $order_obj     = POCO::singleton ( 'pai_order_org_class' );
  $user_obj      = POCO::singleton ( 'pai_user_class' );
  $user_icon_obj = POCO::singleton('pai_user_icon_class');
  $model_relate_org_obj = POCO::singleton('pai_model_relate_org_class');
  $cameraman_comment_log_obj = POCO::singleton('pai_cameraman_comment_log_class');
  $model_comment_log_obj = POCO::singleton('pai_model_comment_log_class');
  //获取
  $event_obj = POCO::singleton('event_details_class');
  //活动表
  $activity_code_obj = POCO::singleton ( 'pai_activity_code_class' );
  $user_id = isset($_INPUT['user_id']) ? intval($_INPUT['user_id']) : 0;
  if (empty($user_id)) 
  {
  	 echo "<script type='text/javascript'>window.alert('非法操作');location.href='order_org_list.php'</script>";
  	 exit;
  }
  $info = $model_relate_org_obj->get_org_model_audit_by_user_id($user_id, $yue_login_id);
  /*$settle_info = $payment_obj->get_settle_info($settle_id);*/
  if (!$info) 
  {
    //echo "skskks";exit;
     echo "<script type='text/javascript'>window.alert('非法操作');location.href='order_org_list.php'</script>";
    exit;
  }
  $icon        = $user_icon_obj->get_user_icon($user_id, 100);
  $page_obj->setvar(array('user_id' => $user_id));
  $total_count = $order_obj->get_order_list_by_user_id($user_id,$yue_login_id, '', $order_by ='', $fields= '*', true);
  $page_obj->set ( $show_count, $total_count );
  $list      = $order_obj->get_order_list_by_user_id($user_id,$yue_login_id, $page_obj->limit(), $order_by ='date_time DESC', $fields= '*');
  /*if($yue_login_id == 100293)
  {
    print_r($list);exit;
  }*/
  if (!empty($list) && is_array($list)) 
  {
      foreach ($list as $key => $vo) 
      {
          $list[$key]['cameraman_nickname'] = get_user_nickname_by_user_id($vo['from_date_id']);
          //$list[$key]['model_nickname']     = get_user_nickname_by_user_id($vo['to_date_id']);
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
  }
  //print_r($list);
  $tpl->assign('list', $list);
  $tpl->assign('icon', $icon);
  $tpl->assign ( "page", $page_obj->output ( 1 ) );
  $tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
  $tpl->output();



 ?>