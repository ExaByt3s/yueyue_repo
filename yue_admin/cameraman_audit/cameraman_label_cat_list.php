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
 
 $tpl = new SmartTemplate("cameraman_label_cat_list.tpl.htm");
 
 //��ҳ��
 $page_obj   = new show_page ();
 $show_count = 20;
 
 
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
 	$cat_name = trim($_INPUT['cat_name']);
 	if (strlen($cat_name) <1)
 	{
 		js_pop_msg('��ǩ��Ŀ������Ϊ��',true);
 		exit;
 	}
 	$data['cat_name'] = $cat_name;
 	$data['add_time'] = time('Y-m-d H:i:s',time());
 	$info = $cameraman_add_label_cat_obj->add_info($data);
 	if($info)
 	{
 		js_pop_msg('��ǩ��Ŀ��ӳɹ�',true);
 		exit;
 	}
 	js_pop_msg('��ǩ��Ŀ���ʧ��',true);
 	exit;
 }
 
 //ɾ������
 if ($act == 'delete')
 {
 	$cat_id = intval($_INPUT['cat_id']);
 	
    if ($cat_id <1)
 	{
 		js_pop_msg('��ǩ��ĿID����Ϊ��',true);
 		exit;
 	}
 	$label_sum = $cameraman_add_label_obj->get_list(true,$cat_id);
 	
 	if($label_sum >0)
 	{
 		js_pop_msg('��ǩ��Ŀ���ڱ�ǩ�޷�ɾ��',true);
 		exit;
 	}
 	$info = $cameraman_add_label_cat_obj->del_info($cat_id);
 	if($info)
 	{
 		js_pop_msg('��ǩ��Ŀɾ���ɹ�',true);
 		exit;
 	}
 	js_pop_msg('��ǩ��Ŀɾ��ʧ��',true);
 	exit;
 }
 
 
 
 
 
 //����
 $where_str = '';
 $setParam  = array();
 
 $total_count = $cameraman_add_label_cat_obj->get_list(true,$where_str);
 
 $page_obj->setvar($setParam);
 $page_obj->set($show_count,$total_count);
 
 $list = $cameraman_add_label_cat_obj->get_list(false, $where_str,'cat_id DESC', $page_obj->limit(), 'cat_id,cat_name');       
 
 if(!is_array($list)) $list = array();
 
 
 $tpl->assign('list',$list);
 $tpl->assign($setParam);
 $tpl->assign ( "page", $page_obj->output ( 1 ) );
 //$tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
 $tpl->output();
 
 
?>