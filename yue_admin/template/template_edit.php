<?php 

 /*
 *模板限修改
 *xiao xiao
 *2014-1-16
 */
 include("common.inc.php");
 $tpl = new SmartTemplate("template_edit.tpl.htm");
 $template_obj = POCO::singleton('pai_template_class');
 $id   = intval($_INPUT['id'])  ? intval($_INPUT['id']) : 0;
 $act  = $_INPUT['act']  ? $_INPUT['act'] : '';
 if (empty($id)) 
 {
 	echo "<script>window.alert('您无权操作');location.href='template_list.php';</script>";
 	exit;
 }
 //更新数据
 if ($act == 'update') 
 {
    //check_authority($ret_type = 'exit_type',$authority_list, 'template', $val = 'is_update');
    $tpl_name   = $_INPUT['tpl_name'] ?  $_INPUT['tpl_name'] : '';
    $sort_order = intval($_INPUT['sort_order']) ? intval($_INPUT['sort_order']) : 0;
 	$tpl_detail = $_INPUT['tpl_detail'] ? $_INPUT['tpl_detail'] : '';
 	$data['tpl_name']   = $tpl_name;
 	$data['sort_order'] = $sort_order;
 	$data['tpl_detail'] = $tpl_detail;
 	$info    = $template_obj->update_template_info_by_id($data, $id);
 	if ($info) 
 	{
 		echo "<script>window.alert('您更新模板成功');location.href='template_list.php';</script>";
 		exit;
 	}
 	echo "<script>window.alert('您更新模板失败');history.back();</script>";
    //var_dump($info);
    exit;
 }

 //删除数据
 if ($act == 'delete') 
 {
    //check_authority($ret_type = 'exit_type',$authority_list, 'template', $val = 'is_delete');
 	$info    = $template_obj->delete_template_info_by_id($id);
 	if ($info) 
 	{
 		echo "<script>window.alert('您删除模板成功');location.href='template_list.php';</script>";
 		exit;
 	}
 	echo "<script>window.alert('您删除模板失败');location.href='template_list.php';</script>";
 	exit;
 }

 //修改时权限
 //check_authority($ret_type = 'exit_type',$authority_list, 'template', $val = 'is_update');
 //修改数据
 $template_info = $template_obj->get_template_info_by_id($id);
 $tpl->assign($template_info);
 $tpl->assign('YUE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
 $tpl->output();
 ?>