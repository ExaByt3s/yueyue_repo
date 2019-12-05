<?php 

/*
 * 模特相册展示及上传页
*/

	include_once 'common.inc.php';
 	$model_add_obj  = POCO::singleton('pai_model_add_class');
 	$tpl = new SmartTemplate("model_pic_show.tpl.htm");
 	$page_obj = new show_page ();
 	$show_count = 20;
 	$uid = $_INPUT['uid'] ? $_INPUT['uid'] : 0;
 	$where_str = "uid = {$uid}";
    $page_obj->setvar (array('uid' => $uid));
 	$total_count = $model_add_obj ->get_pic_list(true, $where_str);
    $page_obj->set ( $show_count, $total_count );
    $list = $model_add_obj->get_pic_list(false, $where_str, $order_by = 'uid DESC', $page_obj->limit(), $fields = '*');
    foreach ($list as $key => $vo) 
    {
    	//$list[$key]['img'] = substr($vo['img_url'], 0, strrpos($vo['img_url'], ".")-4).substr($vo['img_url'],strrpos($vo['img_url'], "."), 4);
    	# code...
        $list[$key]['img'] = str_replace('_260.','.',$vo['img_url']); 
    }
    $tpl->assign ( "page", $page_obj->output ( 1 ) );
    $tpl->assign('list', $list);
    $tpl->assign('uid', $uid);
    $tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
    $tpl->output();
 ?>