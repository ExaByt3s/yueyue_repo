<?php 

 /*
 *ģ�����޸�
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
 	echo "<script>window.alert('����Ȩ����');location.href='template_list.php';</script>";
 	exit;
 }
 //��������
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
 		echo "<script>window.alert('������ģ��ɹ�');location.href='template_list.php';</script>";
 		exit;
 	}
 	echo "<script>window.alert('������ģ��ʧ��');history.back();</script>";
    //var_dump($info);
    exit;
 }

 //ɾ������
 if ($act == 'delete') 
 {
    //check_authority($ret_type = 'exit_type',$authority_list, 'template', $val = 'is_delete');
 	$info    = $template_obj->delete_template_info_by_id($id);
 	if ($info) 
 	{
 		echo "<script>window.alert('��ɾ��ģ��ɹ�');location.href='template_list.php';</script>";
 		exit;
 	}
 	echo "<script>window.alert('��ɾ��ģ��ʧ��');location.href='template_list.php';</script>";
 	exit;
 }

 //�޸�ʱȨ��
 //check_authority($ret_type = 'exit_type',$authority_list, 'template', $val = 'is_update');
 //�޸�����
 $template_info = $template_obj->get_template_info_by_id($id);
 $tpl->assign($template_info);
 $tpl->assign('YUE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
 $tpl->output();
 ?>