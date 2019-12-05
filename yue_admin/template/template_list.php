<?php 
/*
 *模板列表
 *xiao xiao
 *2015-1-16
*/
 include("common.inc.php");
 //check_authority($ret_type = 'exit_type',$authority_list, 'template', 'is_select');
 $tpl = new SmartTemplate("template_list.tpl.htm");
 $page_obj      = new show_page ();
 $show_count    = 20;
 $template_obj = POCO::singleton('pai_template_class');
 $page_obj->setvar();
 $where_str = '';
 $total_count = $template_obj->get_template_list(true,$where_str);
 $page_obj->set ( $show_count, $total_count );
 $list = $template_obj->get_template_list(false, $where_str, 'sort_order DESC,id DESC', $page_obj->limit(),'*' );
 foreach ($list as $key => $vo) 
 {
 	$list[$key]['desc']  = poco_cutstr($vo['tpl_detail'], 20, '....');
 	//$list[$key]['add_time']  = date('Y-m-d H:i:s', $vo['add_time']);
 }
 //var_dump($list);
 $tpl->assign('list', $list);
 $tpl->assign('total_count', $total_count);
 $tpl->assign ( "page", $page_obj->output ( 1 ) );
 $tpl->assign('YUE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
 $tpl->output();

 ?>