<?php 

 /*
 * 权限列表
 *
 */
 include("common.inc.php");
 include("include/common_function.php");
 check_authority_by_list($ret_type = 'exit_type',$authority_list, 'authority', $val = 'is_select');
 $tpl = new SmartTemplate("admin_audit_list.tpl.htm");
 $page_obj          = new show_page ();
 $show_count        = 20;
 $authority_obj     = POCO::singleton('pai_authority_class');
 $authority_log_obj = POCO::singleton('pai_authority_log_class');
 $user_obj          = POCO::singleton ( 'pai_user_class' );
 $user_id  = $_INPUT['user_id'] ? intval($_INPUT['user_id']) : '';
 $nickname = $_INPUT['nickname'] ? $_INPUT['nickname'] : '';
 $module   = $_INPUT['module'] ? $_INPUT['module'] : '';
 $action   = $_INPUT['action'] ? $_INPUT['action'] : '';
 $where_str = '1';
 if ($user_id) 
 {
 	$user_id = (int)$user_id;
 	$where_str .= " AND user_id = {$user_id}";
 }
 //昵称
 //var_dump($nickname);
 if ($nickname) 
 {
 	$app_user_id = $user_obj->get_user_id_by_nickname($nickname);
 	//var_dump($app_user_id);
 	if (!empty($app_user_id) && is_array($app_user_id)) 
 	{
 		$app_user_id = array_change_by_val($app_user_id, 'user_id');
 		if ($user_id == null) 
 		{
 			$get_user_id = implode(',', $app_user_id);
 			$where_str .= " AND user_id in ({$get_user_id})";
 		}
 		elseif (!in_array($user_id, $app_user_id)) 
 		{
 			$where_str .= " AND user_id = 110";
 		}
 	}
 	elseif (empty($app_user_id) || !is_array($app_user_id)) 
 	{
 		$where_str .= " AND user_id = 110";
 	}
 }
 //module
 if ($module) 
 {
 	$where_str .= " AND module = '{$module}'";
 	# code...
 }
 if ($action) 
 {
 	$where_str .= " AND action = '{$action}'";
 	# code...
 }
 //var_dump($where_str);
 $page_obj->setvar(
 		array
 		(
 			'user_id'   => $user_id ,
 			'nickname'  => $nickname,
 			'module'    => $module,
 			'action'    => $action
 			)
 	);
 
 $total_count = $authority_obj->get_authority_list_user(true,$where_str);
 $page_obj->set ( $show_count, $total_count );
 $list = $authority_obj->get_authority_list_user(false, $where_str, 'id DESC', $page_obj->limit(),'*' );
 foreach ($list as $key => $vo) 
 {
 	$list[$key]['nickname']  = get_user_nickname_by_user_id($vo['user_id'] );
 	$list[$key]['add_time']  = date('Y-m-d H:i:s', $vo['add_time']);
 }
 //print_r($list);exit;
 $tpl->assign('user_id', $user_id);
 $tpl->assign('nickname', $nickname);
 $tpl->assign('module', $module);
 $tpl->assign('action', $action);
 $tpl->assign('list', $list);
 $tpl->assign('total_count', $total_count);
 $tpl->assign ( "page", $page_obj->output ( 1 ) );
 $tpl->assign('YUE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
 $tpl->output();
 ?>