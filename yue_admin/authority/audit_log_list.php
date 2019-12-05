<?php 

 /*
 * 权限列表
 *
 */
 include("common.inc.php");
 check_authority_by_list($ret_type = 'exit_type',$authority_list, 'authority', $val = 'is_select');
 $tpl = new SmartTemplate("audit_log_list.tpl.htm");
 $page_obj      = new show_page ();
 $show_count    = 20;
 $authority_log_obj = POCO::singleton('pai_authority_log_class');
 $page_obj->setvar();
 $where_str = '';
 $total_count = $authority_log_obj->get_authority_list_log(true,$where_str);
 $page_obj->set ( $show_count, $total_count );
 $list = $authority_log_obj->get_authority_list_log(false, $where_str, 'id DESC', $page_obj->limit(),'*' );
 foreach ($list as $key => $vo) 
 {
 	$list[$key]['nickname']  = get_user_nickname_by_user_id($vo['user_id'] );
 	$list[$key]['add_time']  = date('Y-m-d H:i:s', $vo['add_time']);
 }
 //print_r($list);exit;
 $tpl->assign('list', $list);
 $tpl->assign('total_count', $total_count);
 $tpl->assign ( "page", $page_obj->output ( 1 ) );
 $tpl->assign('YUE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
 $tpl->output();
 ?>