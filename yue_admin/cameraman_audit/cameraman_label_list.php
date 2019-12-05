<?php

/**
 * 标签分类管理器
 * @authors xiao xiao (xiaojm@yueus.com)
 * @date    2015年4月29日
 * @version 1
 */

 include_once ("common.inc.php");
 //标签分类
 $cameraman_add_label_cat_obj = POCO::singleton('pai_cameraman_add_label_cat_class');
 //标签
 $cameraman_add_label_obj  = POCO::singleton('pai_cameraman_add_label_class');
 
 $tpl = new SmartTemplate("cameraman_label_list.tpl.htm");
 
 
 $act = trim($_INPUT['act']);
 
 //特殊权限
 if(!in_array($yue_login_id, $user_arr))
 {
 	js_pop_msg('您没有管理标签的权限');
 	exit;
 }
 
 //添加数据
 if($act == 'insert')
 {
 	$label = trim($_INPUT['label']);
 	if (strlen($label) <1)
 	{
 		js_pop_msg('标签名不能为空',true);
 		exit;
 	}
 	$cat_id = intval($_INPUT['cat_id']);
 	if ($cat_id <1)
 	{
 		js_pop_msg('标签类目ID不能为空',true);
 		exit;
 	}
 	$data['label']  = $label;
 	$data['cat_id'] = $cat_id;
 	$info = $cameraman_add_label_obj->add_info($data);
 	if($info)
 	{
 		js_pop_msg('标签添加成功',true);
 		exit;
 	}
 	js_pop_msg('标签添加失败',true);
 	exit;
 }
 
 //删除数据
 if ($act == 'delete')
 {
 	$id = intval($_INPUT['id']);
 	
    if ($id <1)
 	{
 		js_pop_msg('标签ID不能为空',true);
 		exit;
 	}
 	$info = $cameraman_add_label_obj->del_info($id);
 	if($info)
 	{
 		js_pop_msg('标签删除成功',true);
 		exit;
 	}
 	js_pop_msg('标签删除失败',true);
 	exit;
 }
 
 
 
 $cat_id = intval($_INPUT['cat_id']);
 
 if($cat_id <1)
 {
 	js_pop_msg('请选择类目ID');
 	exit;
 }
 
 $ret = $cameraman_add_label_cat_obj->get_info($cat_id);
 //条件
 $where_str = '';
 $list = $cameraman_add_label_obj->get_list(false,$cat_id, $where_str,'cat_id DESC,id DESC', '0,99999999', 'id,label');       
 //print_r();
 
 if(!is_array($list)) $list = array();
 
 $tpl->assign($ret);
 $tpl->assign('list',$list);
 $tpl->output();
 
 
?>