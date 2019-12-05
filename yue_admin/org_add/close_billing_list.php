<?php 

/* 
 *已结算
 *xiao xiao
 * 2014-1-28
*/
  include('common.inc.php');
  include('include/common_function.php');
  $tpl = new SmartTemplate("close_billing_list.tpl.htm");
  $page_obj = new show_page ();
  $show_count = 5;
  $payment_obj = POCO::singleton('pai_payment_class');
  $user_id      = $_INPUT['user_id'] ? intval($_INPUT['user_id']) : '';
  if (empty($user_id)) 
  {
    echo "<script type='text/javascript'> window.alert('非法操作');parent.location.href='org_list.php';</script>";
    exit;
  }
  $where_str = "1";
  $total_count = $payment_obj->get_settle_list($user_id, true, $where_str);
  //$page_obj->setvar($setvar);
  $page_obj->setvar(array('user_id'=> $user_id));
  $page_obj->set ( $show_count, $total_count );
  $list = $payment_obj->get_settle_list($user_id,false, $where_str, $order_by = 'settle_date DESC', $page_obj->limit(), $fields = '*');
  $tpl->assign('list', $list);
  //$tpl->assign('act', $act);
  $tpl->assign('total_count', $total_count);
  $tpl->assign ( "page", $page_obj->output ( 1 ) );
  //$tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
  $tpl->output();



 ?>