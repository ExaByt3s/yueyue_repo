<?php 

/* 
 *ря╫АкЦ
 *xiao xiao
 * 2014-1-28
*/
  include('common.inc.php');
  include('include/common_function.php');
  $tpl = new SmartTemplate("close_billing_list.tpl.htm");
  $page_obj = new show_page ();
  $show_count = 20;
  $payment_obj = POCO::singleton('pai_payment_class');
  /*$date_obj  = POCO::singleton ( 'event_date_class' );
  $event_obj = POCO::singleton('event_details_class');
  $cameraman_comment_log_obj = POCO::singleton('pai_cameraman_comment_log_class');
  $model_comment_log_obj = POCO::singleton('pai_model_comment_log_class');*/
  $act = $_INPUT['act'] ? $_INPUT['act'] : 'search';
  $start_settle_date = $_INPUT['start_settle_date'] ? $_INPUT['start_settle_date'] : '';
  $end_settle_date   = $_INPUT['end_settle_date'] ? $_INPUT['end_settle_date'] : '';
  $where_str = "1";
  if ($start_settle_date && $end_settle_date) 
  {
    //$start_settle_date_tmp = strtotime($start_settle_date);
    //$end_settle_date_tmp   = strtotime($end_settle_date)+24*3600;
    $where_str .= " AND settle_date BETWEEN '{$start_settle_date}' AND '{$end_settle_date}'";
  }
  //var_dump($where_str);
  $setvar = array
  (
      'start_settle_date' => $start_settle_date,
      'end_settle_date'   => $end_settle_date,    
    );
  $total_count = $payment_obj->get_settle_list($yue_login_id, true, $where_str);
  $page_obj->setvar($setvar);
  $page_obj->set ( $show_count, $total_count );
  $list = $payment_obj->get_settle_list($yue_login_id,false, $where_str, $order_by = 'settle_date DESC,settle_id DESC', $page_obj->limit(), $fields = '*'); 
 /* if ($list) 
  {
    foreach ($list as $key => $vo) 
    {
       $list[$key]['settle_time'] = date('Y-m-d', $vo['settle_time']);
    }
  }*/
  $tpl->assign($setvar);
  $tpl->assign('list', $list);
  $tpl->assign('act', $act);
  $tpl->assign('total_count', $total_count);
  $tpl->assign ( "page", $page_obj->output ( 1 ) );
  $tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
  $tpl->output();



 ?>