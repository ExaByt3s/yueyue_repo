<?php 

/*
 * 摄影师作品展示及上传页
*/

	include_once 'common.inc.php';
    check_authority(array('cameraman'));
 	$cameraman_add_obj  = POCO::singleton('pai_cameraman_add_class');
 	$tpl = new SmartTemplate("cameraman_pic_show.tpl.htm");
 	$page_obj = new show_page ();
 	$show_count = 20;
 	$uid = $_INPUT['uid'] ? $_INPUT['uid'] : 0;
 	$where_str = "uid = {$uid}";
    $page_obj->setvar (array('uid' => $uid));
 	$total_count = $cameraman_add_obj ->get_cameraman_pic_list(true, $where_str);
    $page_obj->set ( $show_count, $total_count );
    $list = $cameraman_add_obj->get_cameraman_pic_list(false, $where_str, $order_by = 'uid DESC', $page_obj->limit(), $fields = '*');
    foreach ($list as $key => $vo) 
    {
    	$list[$key]['img'] = substr($vo['img_url'], 0, strrpos($vo['img_url'], ".")-4).substr($vo['img_url'],strrpos($vo['img_url'], "."), 4);
    	# code...
    }
    $tpl->assign ( "page", $page_obj->output ( 1 ) );
    $tpl->assign('list', $list);
    $tpl->assign('uid', $uid);
    $tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
    $tpl->output();
 ?>