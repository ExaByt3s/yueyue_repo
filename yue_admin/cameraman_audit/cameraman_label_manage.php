<?php

/**
 * 标签管理器
 * @authors xiao xiao (xiaojm@yueus.com)
 * @date    2015年4月30日
 * @version 1
 */

 include_once 'common.inc.php';
 
 //标签分类类
 $cameraman_add_label_cat_obj = POCO::singleton('pai_cameraman_add_label_cat_class');
 
 //标签类
 $cameraman_add_label_class  = POCO::singleton('pai_cameraman_add_label_class');
 
 $tpl = new SmartTemplate("cameraman_label_manage.tpl.htm");
 
 
 
 $act = trim($_INPUT['act']);
 
 

 
 /* $user_id = intval($_INPUT['user_id']);

 if($user_id <1)
 {
 	js_pop_msg('非法操作');
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
 
 //选中的数据遍历
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
 
 
 
 