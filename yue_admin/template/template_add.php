<?php 

 /*
 * 模板增加
 *
 */
 include("common.inc.php");
 //check_authority($ret_type = 'exit_type',$authority_list, 'template', $val = 'is_insert');
 $tpl = new SmartTemplate("template_add.tpl.htm");
 $template_obj = POCO::singleton('pai_template_class');
 $act  = $_INPUT['act']  ? $_INPUT['act'] : '';
 //更新数据
 if ($act == 'insert') 
 {
 	
 	$tpl_name     = $_INPUT['tpl_name'] ? $_INPUT['tpl_name'] : '';
 	$sort_order   = intval($_INPUT['sort_order']) ? intval($_INPUT['sort_order']) : 0;
 	$tpl_detail   = $_INPUT['tpl_detail'] ? $_INPUT['tpl_detail'] : '';
 	$data['tpl_name']   = $tpl_name;
 	$data['sort_order'] = $sort_order;
 	$data['tpl_detail'] = $tpl_detail;
 	$info    = $template_obj->insert_template_by_data($data);
 	echo "<script>window.alert('您插入模板成功');location.href='template_list.php';</script>";
    //var_dump($info);
    exit;
 }
 $tpl->assign('YUE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
 $tpl->output();
 ?>