<?php

/**
 * ��ǩ������
 * @authors xiao xiao (xiaojm@yueus.com)
 * @date    2015��4��30��
 * @version 1
 */

 include_once 'common.inc.php';
 
 //��ǩ������
 $cameraman_add_label_cat_obj = POCO::singleton('pai_cameraman_add_label_cat_class');
 
 //��ǩ��
 $cameraman_add_label_class  = POCO::singleton('pai_cameraman_add_label_class');
 
 $tpl = new SmartTemplate("cameraman_label_manage.tpl.htm");
 
 
 
 $act = trim($_INPUT['act']);
 
 

 
 /* $user_id = intval($_INPUT['user_id']);

 if($user_id <1)
 {
 	js_pop_msg('�Ƿ�����');
 	exit;
 } */
 
 $label_cat_ret = $cameraman_add_label_cat_obj->get_list(false,'','cat_id DESC','0,10');
 if(!is_array($label_cat_ret)) $label_cat_ret = array();
 
 foreach ($label_cat_ret as $key=>$val)
 {
 	$label_cat_ret[$key]['label_ret'] = $cameraman_add_label_class->get_list(false, $val['cat_id'],'','id DESC','0,10');
 }
 
 $ids = $_INPUT['ids'];
 $ids = explode(',', $ids);
 
 //ѡ�е����ݱ���
 foreach ($ids as $id)
 {
 	$id = intval($id);
 	foreach ($label_cat_ret as $key_cat=>$label_val)
 	{
 		foreach ($label_val['label_ret'] as $key=>$kval)
 		{
 			if($kval['id'] == $id) $label_cat_ret[$key_cat]['label_ret'][$key]['label_selected'] = "class='cur'";
 		}
 	}
 }
 //print_r($label_cat_ret);
 
 $tpl->assign('label_cat_ret',$label_cat_ret);
 $tpl->output();
 
 ?>
 
 
 
 