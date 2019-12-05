<?php 
/*
 *发送模板选择列表
 *xiao xiao
 *2015-1-16
*/
 include("common.inc.php");
 $ids  = $_INPUT['ids'] ? $_INPUT['ids'] : '';
 $url  = $_INPUT['url'] ? $_INPUT['url'] : '';
 $tpl = new SmartTemplate("template_select.tpl.htm");
 $template_obj = POCO::singleton('pai_template_class');
 $where_str = '';
 $list = $template_obj->get_template_list(false, $where_str, 'sort_order DESC,id DESC', '0,10','*' );
 //var_dump($list);
 //$ids = explode(',', $ids);
 $tpl->assign('ids', $ids);
 $tpl->assign('url', $url);
 $tpl->assign('list', $list);
 $tpl->assign('YUE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
 $tpl->output();

 ?>