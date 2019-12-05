<?php

/**
 * ��ǩ���������
 * @authors xiao xiao (xiaojm@yueus.com)
 * @date    2015��4��29��
 * @version 1
 */

 include_once ("common.inc.php");
 //��ǩ����
 $cameraman_add_label_cat_obj = POCO::singleton('pai_cameraman_add_label_cat_class');
 //��ǩ
 $cameraman_add_label_obj  = POCO::singleton('pai_cameraman_add_label_class');
 
 $tpl = new SmartTemplate("cameraman_label_list.tpl.htm");
 
 
 $act = trim($_INPUT['act']);
 
 //����Ȩ��
 if(!in_array($yue_login_id, $user_arr))
 {
 	js_pop_msg('��û�й����ǩ��Ȩ��');
 	exit;
 }
 
 //�������
 if($act == 'insert')
 {
 	$label = trim($_INPUT['label']);
 	if (strlen($label) <1)
 	{
 		js_pop_msg('��ǩ������Ϊ��',true);
 		exit;
 	}
 	$cat_id = intval($_INPUT['cat_id']);
 	if ($cat_id <1)
 	{
 		js_pop_msg('��ǩ��ĿID����Ϊ��',true);
 		exit;
 	}
 	$data['label']  = $label;
 	$data['cat_id'] = $cat_id;
 	$info = $cameraman_add_label_obj->add_info($data);
 	if($info)
 	{
 		js_pop_msg('��ǩ��ӳɹ�',true);
 		exit;
 	}
 	js_pop_msg('��ǩ���ʧ��',true);
 	exit;
 }
 
 //ɾ������
 if ($act == 'delete')
 {
 	$id = intval($_INPUT['id']);
 	
    if ($id <1)
 	{
 		js_pop_msg('��ǩID����Ϊ��',true);
 		exit;
 	}
 	$info = $cameraman_add_label_obj->del_info($id);
 	if($info)
 	{
 		js_pop_msg('��ǩɾ���ɹ�',true);
 		exit;
 	}
 	js_pop_msg('��ǩɾ��ʧ��',true);
 	exit;
 }
 
 
 
 $cat_id = intval($_INPUT['cat_id']);
 
 if($cat_id <1)
 {
 	js_pop_msg('��ѡ����ĿID');
 	exit;
 }
 
 $ret = $cameraman_add_label_cat_obj->get_info($cat_id);
 //����
 $where_str = '';
 $list = $cameraman_add_label_obj->get_list(false,$cat_id, $where_str,'cat_id DESC,id DESC', '0,99999999', 'id,label');       
 //print_r();
 
 if(!is_array($list)) $list = array();
 
 $tpl->assign($ret);
 $tpl->assign('list',$list);
 $tpl->output();
 
 
?>