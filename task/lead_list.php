<?php
/** 
 * 
 * tt
 * 汤圆
 * 2015-4-11
 * 
 */
 

/**
 * 引用资源文件定位，注意！确保引用路径争取
 */
$file_dir = dirname(__FILE__);

include_once($file_dir.'/./task_common.inc.php');




// 权限文件
include_once($file_dir.'/./task_auth_common.inc.php');

include_once($file_dir. '/./webcontrol/head.php');
include_once($file_dir. '/./webcontrol/top_nav.php');
include_once($file_dir. '/./webcontrol/footer.php');
 
$tpl = $my_app_pai->getView('lead_list.tpl.htm');

$tpl->assign('time', time());  //随机数


// 公共样式和js引入
$pc_global_top = _get_wbc_head();
$tpl->assign('pc_task_top', $pc_global_top);

$pc_global_nav = _get_wbc_top_nav(array('cur_page'=>'lead_list'));
$tpl->assign('pc_global_nav', $pc_global_nav);

// 底部
$footer_html = _get_wbc_footer();
$tpl->assign('footer_html', $footer_html);

$task_lead_obj = POCO::singleton('pai_task_lead_class');


/**********分页处理**********/
$page_obj = new show_page ();

$where = "";

$total_count = $task_lead_obj->get_lead_list_valid_by_user_id($yue_login_id,true, $where);

$show_count = 10;

$page_obj->setvar ();

$page_obj->set ( $show_count, $total_count );

$lead_list = $task_lead_obj->get_lead_list_valid_by_user_id($yue_login_id,false, $where, $page_obj->limit ());

if ($show_count > $total_count) 
{
    $page_show = '';
}
else
{
    $page_show = $page_obj->output ( 1 ) ;
}
$tpl->assign ( "page", $page_show );

if ($total_count <= 0) {
    $tpl->assign ( "no_page_data", '1' );
}








/*
 * 获取需求问卷问答
 * @param int $request_id
 * @return array
 */

foreach ($lead_list as $k => $val) {
    $request_id = $val['request_id'];
    $obj = POCO::singleton('pai_task_questionnaire_class');
    $arr = $obj->show_questionnaire_data($request_id);
    $lead_list[$k]['answer'] = $arr['data'] ;
}




$tpl->assign('list', $lead_list);





// print_r($lead_list);

$tpl->output();
 ?>