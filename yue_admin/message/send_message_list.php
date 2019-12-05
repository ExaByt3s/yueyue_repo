<?php 

 /**
 *
 *信息列表
 * xiao xiao
 */
 include("common.inc.php");
 include_once("/disk/data/htdocs232/poco/pai/yue_admin/common/locate_file.php");
 include_once ("/disk/data/htdocs232/poco/php_common/poco_location_fun.inc.php");
 $tpl = new SmartTemplate("send_mall_message_list.tpl.htm");
 $user_obj = POCO::singleton('pai_user_class');
 $message_log_obj = POCO::singleton('pai_send_message_log_class');
 /*$message_log_obj->del_info(18);
 exit*/;
 $page_obj = new show_page ();
 $show_count = 20;
 $act = $_INPUT['act'] ? $_INPUT['act'] : 'list';
 $where_str = "1";
 $total_count = $message_log_obj->get_info_list(true, $where_str);
 $page_obj->setvar ();
 $page_obj->set ( $show_count, $total_count );
 $list = $message_log_obj->get_info_list(false, $where_str, 'add_time DESC', $page_obj->limit(), $fields = '*');
 if ($list)
 {
 	foreach ($list as $key => $vo) 
 	{
 		$list[$key]['add_time']      = date('Y-m-d H:i:s', $vo['add_time']);
 		$list[$key]['update_time']   = date('Y-m-d H:i:s', $vo['update_time']);
 		$list[$key]['city'] = get_poco_location_name_by_location_id ($vo['location_id']);
 		//$list[$key]['role_name'] = get_poco_location_name_by_location_id ($vo['location_id']);
 		$list[$key]['send_name']  = get_user_nickname_by_user_id($vo['add_id']);
 		$list[$key]['desc']       = poco_cutstr($vo['content'], 20, '....');
        $list[$key]['card_text1'] = poco_cutstr($vo['card_text1'], 20, '....');
 	}
 }
 $tpl->assign('list', $list);
 $tpl->assign('total_count', $total_count);
 $tpl->assign('YUE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
 $tpl->assign ( "page", $page_obj->output ( 1 ) );
 $tpl->output();
 ?>