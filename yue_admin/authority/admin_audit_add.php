<?php 

 /*
 * Ȩ������
 *
 */
 include("common.inc.php");
 check_authority_by_list($ret_type = 'exit_type',$authority_list, 'authority', $val = 'is_insert');
 $tpl = new SmartTemplate("admin_audit_add.tpl.htm");
 $authority_obj = POCO::singleton('pai_authority_class');
 $authority_log_obj = POCO::singleton('pai_authority_log_class');
 $act  = $_INPUT['act']  ? $_INPUT['act'] : '';
 //��������
 if ($act == 'insert') 
 {
 	$user_id     = intval($_INPUT['user_id']) ? intval($_INPUT['user_id']) : 0;
 	$module      = trim($_INPUT['module']) ?  trim($_INPUT['module']) : 'module';
 	$action      = trim($_INPUT['action']) ?  trim($_INPUT['action']) : 'action';
 	$location_id = trim($_INPUT['location_id']) ?  trim($_INPUT['location_id']) : '';
 	//print_r($_INPUT);//exit;
 	$is_insert    = intval($_INPUT['is_insert']) ? intval($_INPUT['is_insert']) : 0;
 	$is_delete    = intval($_INPUT['is_delete']) ? intval($_INPUT['is_delete']) : 0;
 	$is_update    = intval($_INPUT['is_update']) ? intval($_INPUT['is_update']) : 0;
 	$is_select    = intval($_INPUT['is_select']) ? intval($_INPUT['is_select']) : 0;
 	$where_str = "user_id = {$user_id} AND module = '{$module}' AND action = '{$action}'";
 	$data  = $authority_obj->get_authority_info_by_where($where_str);
 	if (!empty($data)) 
 	{
 		//����log
        $log_data['log_txt'] = "�������ʧ��,��Ҫ��������Ϊuser_id=>{$user_id},module=>{$module},action=>{$action},is_insert=>{$is_insert},is_delete=>{$is_delete}, is_update=>{$is_update},is_select=>{$is_select},ԭ����ܸ��������Ѿ�����!";
        $log_data['user_id'] = $yue_login_id;
        $log_data['add_time'] = time();
        $authority_log_obj->insert_authority_log($log_data);
 	 	echo "<script>window.alert('������ʧ��,���û������Ѿ����ڸ�ģ������!');location.href='admin_audit_add.php';</script>";
 	 	exit;
 	}
 	$data['user_id']    = $user_id;
 	$data['module']     = $module;
 	$data['action']     = $action;
 	$data['location_id']=$location_id;
 	$data['add_time']   = strtotime(date('Y-m-d H:i:s'));
 	$data['is_insert']  = $is_insert;
 	$data['is_delete']  = $is_delete;
 	$data['is_update']  = $is_update;
 	$data['is_select']  = $is_select;
 	$info    = $authority_obj->insert_authority_info_by_id($data);
 	//����log
    $log_data['log_txt'] = "������ݳɹ�,����user_id=>{$user_id},module=>{$module},action=>{$action},is_insert=>{$is_insert},is_delete=>{$is_delete}, is_update=>{$is_update},is_select=>{$is_select}";
    $log_data['user_id'] = $yue_login_id;
    $log_data['add_time'] = time();
    $authority_log_obj->insert_authority_log($log_data);
 	echo "<script>window.alert('���������Ա�ɹ�');location.href='admin_audit_list.php';</script>";
    //var_dump($info);
    exit;
 }

 $tpl->assign('YUE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
 $tpl->output();
 ?>