<?php

/**
 * ��ǩ������߲���
 * @authors xiao xiao (xiaojm@yueus.com)
 * @date    2015��4��29��
 * @version 1
 */

 include_once ("common.inc.php"); 
 $cameraman_add_label_cat_obj = POCO::singleton('pai_cameraman_add_label_cat_class');
 
 if(!in_array($yue_login_id, $user_arr))
 {
 	js_pop_msg('��û�й����ǩ��Ȩ��');
 	exit;
 }
 
 $tpl = new SmartTemplate("cameraman_label_left.tpl.htm");
 //����
 $where_str = '';
 $list = $cameraman_add_label_cat_obj->get_list(false, $where_str,'cat_id DESC', '0,99999999', 'cat_id,cat_name');       
 
 if(!is_array($list)) $list = array();
 $tpl->assign('list',$list);
 $tpl->output();
 
 
?>