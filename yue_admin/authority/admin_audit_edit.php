<?php 

 /*
 * 权限修改
 *
 */
 include("common.inc.php");
 $tpl = new SmartTemplate("admin_audit_edit.tpl.htm");
 $authority_obj = POCO::singleton('pai_authority_class');
 $authority_log_obj = POCO::singleton('pai_authority_log_class');
 $id   = intval($_INPUT['id'])  ? intval($_INPUT['id']) : 0;
 $act  = $_INPUT['act']  ? $_INPUT['act'] : '';
 if (empty($id)) 
 {
 	echo "<script>window.alert('您无法操作');location.href='admin_audit_list.php';</script>";
 	exit;
 }
 //更新数据
 if ($act == 'update') 
 {
    check_authority_by_list($ret_type = 'exit_type',$authority_list, 'authority', $val = 'is_update');
 	$user_id   = intval($_INPUT['user_id']) ? intval($_INPUT['user_id']) : 0;
 	$module    = trim($_INPUT['module']) ?  trim($_INPUT['module']) : 'module';
 	$action    = trim($_INPUT['action']) ?  trim($_INPUT['action']) : 'action';
 	$location_id = trim($_INPUT['location_id']) ?  trim($_INPUT['location_id']) : '';
 	//print_r($_INPUT);//exit;
 	$is_insert    = intval($_INPUT['is_insert']) ? intval($_INPUT['is_insert']) : 0;
 	$is_delete    = intval($_INPUT['is_delete']) ? intval($_INPUT['is_delete']) : 0;
 	$is_update    = intval($_INPUT['is_update']) ? intval($_INPUT['is_update']) : 0;
 	$is_select    = intval($_INPUT['is_select']) ? intval($_INPUT['is_select']) : 0;
 	$where_str = "user_id = {$user_id} AND module = '{$module}' AND action = '{$action}'";
 	$data  = $authority_obj->get_authority_info_by_where($where_str);
 	if (!empty($data) && $data['user_id'] != $user_id) 
 	{
 		$log_data['log_txt'] = '更新数据失败,要把{$id}中的user_id=>{$user_id},module=>{$module},action=>{$action},is_insert=>{$is_insert},is_delete=>{$is_delete}, is_update=>{$is_update},is_select=>{$is_select}';
        $log_data['user_id'] = $yue_login_id;
        $log_data['add_time'] = time();
        $authority_log_obj->insert_authority_log($log_data);
 	 	echo "<script>window.alert('您修改失败');location.href='admin_audit_edit.php?id={$id}';</script>";
 	 	exit;
 	}
 	$data['user_id']    = $user_id;
 	$data['module']     = $module;
 	$data['action']     = $action;
 	$data['location_id']=addslashes($location_id);
 	$data['is_insert']  = $is_insert;
 	$data['is_delete']  = $is_delete;
 	$data['is_update']  = $is_update;
 	$data['is_select']  = $is_select;
 	$info    = $authority_obj->update_authority_info_by_id($data, $id);
 	//生成log
    $log_data['log_txt'] = "更新数据成功,把{$id}中的user_id=>{$user_id},module=>{$module},action=>{$action},is_insert=>{$is_insert},is_delete=>{$is_delete}, is_update=>{$is_update},is_select=>{$is_select}";
    $log_data['user_id'] = $yue_login_id;
    $log_data['add_time'] = time();
    $authority_log_obj->insert_authority_log($log_data);
 	echo "<script>window.alert('您更新管理员成功');location.href='admin_audit_list.php';</script>";
    //var_dump($info);
    exit;
 }

 //删除数据
 if ($act == 'delete') 
 {
    check_authority_by_list($ret_type = 'exit_type',$authority_list, 'authority', $val = 'is_delete');
 	$info    = $authority_obj->delete_authority_info_by_id($id);
 	//生成log
    $log_data['log_txt'] = "删除ID={$id}中的数据";
    $log_data['user_id'] = $yue_login_id;
    $log_data['add_time'] = time();
    $authority_log_obj->insert_authority_log($log_data);
 	echo "<script>window.alert('您删除管理员成功');location.href='admin_audit_list.php';</script>";
 	exit;
 }

 check_authority_by_list($ret_type = 'exit_type',$authority_list, 'authority', $val = 'is_update');
 //修改数据
 $audit_info = $authority_obj->get_authority_info_by_id($id);
 $selected = "checked='true'";
 if (!empty($audit_info)) 
 {
 	$audit_info['nickname'] = get_user_nickname_by_user_id($audit_info['user_id'] );
 	$audit_info['insert_sel'] = $audit_info['is_insert'] == 1 ? $selected : '';
 	$audit_info['delete_sel'] = $audit_info['is_delete'] == 1 ? $selected : '';
 	$audit_info['update_sel'] = $audit_info['is_update'] == 1 ? $selected : '';
 	$audit_info['select_sel'] = $audit_info['is_select'] == 1 ? $selected : '';
 }
 $tpl->assign($audit_info);
 $tpl->assign('YUE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
 $tpl->output();
 ?>