<?php 

/* 
 *榜单log控制器
 *xiao xiao
 * 2014-2-9
*/
  include('common.inc.php');
  include('include/common_function.php');
  $rank_event_log_obj = POCO::singleton('pai_rank_event_log_class');
  $rank_event_obj     = POCO::singleton('pai_rank_event_class');
  $tpl                = new SmartTemplate("rank_event_log_list.tpl.htm");
  $page_obj           = new show_page ();
  $show_count         = 20;
  $act                = $_INPUT['act'] ? $_INPUT['act'] : 'list';
  if ($act == 'restore') 
  {
    $id = $_INPUT['id'] ? intval($_INPUT['id']) : 0;
    if (empty($id)) 
    {
       echo "<script type='text/javascript'>window.alert('非法操作');location.href='rank_event_log_list.php'</script>";
       exit;
    }
    $list = $rank_event_log_obj->get_unserialize_rank_event_info($id);
    if ($list) 
    {
      $rank_event_obj->delete_info('',true);
      foreach ($list as $key => $vo) 
      {
         $rank_event_obj->add_info($vo);
      }
      # code...
    }
    echo "<script type='text/javascript'>window.alert('恢复成功');location.href='rank_event.php'</script>";
       exit;
    # code...
  }
  //列表
  $where_str   = "1";
  $total_count = $rank_event_log_obj->get_rank_event_log_list(true, $where_str);
  $page_obj->setvar ();
  $page_obj->set ( $show_count, $total_count );
  $list = $rank_event_log_obj->get_rank_event_log_list(false , $where_str, 'audit_time DESC,id DESC', $page_obj->limit(), '*');
  if ($list) 
  {
    foreach ($list as $key => $vo) 
    {
       $list[$key]['audit_time']    = date('Y-m-d H:i:s', $vo['audit_time']);
       $list[$key]['nick_name']   = get_user_nickname_by_user_id($vo['audit_id']);
    }
  }
  $tpl->assign('total_count', $total_count);
  $tpl->assign('list', $list);
  $tpl->assign ( "page", $page_obj->output ( 1 ) );  
  $tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
  $tpl->output();
 ?>