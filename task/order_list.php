<?php
/** 
 * 
 * tt
 * 汤圆
 * 2015-4-11
 * 
 */
 
include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');

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

$tpl = $my_app_pai->getView('order_list.tpl.htm');

$tpl->assign('test', $test);  //随机数

$tpl->assign('time', time());  //随机数

$type = $_INPUT['type'] ? $_INPUT['type'] : 'pending';


// 公共样式和js引入
$pc_global_top = _get_wbc_head();
$tpl->assign('pc_task_top', $pc_global_top);

$pc_global_nav = _get_wbc_top_nav(array('cur_page'=>'order_list'));
$tpl->assign('pc_global_nav', $pc_global_nav);

// 底部
$footer_html = _get_wbc_footer();
$tpl->assign('footer_html', $footer_html);



$task_quotes_obj = POCO::singleton('pai_task_quotes_class');


$page_obj = new show_page ();
$show_count = 10;
$page_obj->setvar (array("type"=>$type));



switch ($type) {
	case 'pending':
		$total_count = $task_quotes_obj->get_pending_quotes_list($yue_login_id, true);
		$page_obj->set ( $show_count, $total_count );
		$list = $task_quotes_obj->get_pending_quotes_list($yue_login_id, false, $page_obj->limit (), '', 'is_important DESC,quotes_id DESC');

		$title_txt = "待处理订单";

		// 组合回答
		foreach ($list as $k => $val) {
			$request_id = $val['request_id'];
			$obj = POCO::singleton('pai_task_questionnaire_class');
			$arr = $obj->show_questionnaire_data($request_id);
			$list[$k]['answer'] = $arr['data'] ;
		}
        
	break;
	
	case 'process':
		$total_count = $task_quotes_obj->get_process_quotes_list($yue_login_id, true);
		$page_obj->set ( $show_count, $total_count );
		$list = $task_quotes_obj->get_process_quotes_list($yue_login_id, false, $page_obj->limit (), '', 'is_important DESC,quotes_id DESC');

		$title_txt = "进行中订单";

		// 组合回答
		foreach ($list as $k => $val) {
			$request_id = $val['request_id'];
			$obj = POCO::singleton('pai_task_questionnaire_class');
			$arr = $obj->show_questionnaire_data($request_id);
			$list[$k]['answer'] = $arr['data'] ;
		}
        
	break;
	
	case 'archive':
		$total_count = $task_quotes_obj->get_archive_quotes_list($yue_login_id, true);
		$page_obj->set ( $show_count, $total_count );
		$list = $task_quotes_obj->get_archive_quotes_list($yue_login_id, false, $page_obj->limit (), '', 'is_important DESC,quotes_id DESC');

		$title_txt = "收藏";


		// 组合回答
		foreach ($list as $k => $val) {
			$request_id = $val['request_id'];
			$obj = POCO::singleton('pai_task_questionnaire_class');
			$arr = $obj->show_questionnaire_data($request_id);
			$list[$k]['answer'] = $arr['data'] ;
		}

	break;
	
}

// 标题
$tpl->assign('title_txt', $title_txt);



//  翻页
// $tpl->assign ( "page", $page_obj->output ( 1 ) );

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




//红色数字
$pending_remind_num = $task_quotes_obj->get_pending_quotes_list($yue_login_id, true, '', 'is_important=1');
if( $pending_remind_num>99 ) $pending_remind_num = '99+';
$process_remind_num = $task_quotes_obj->get_process_quotes_list($yue_login_id, true, '', 'is_important=1');
if( $process_remind_num>99 ) $process_remind_num = '99+';
$archive_remind_num = $task_quotes_obj->get_archive_quotes_list($yue_login_id, true, '', 'is_important=1');
if( $archive_remind_num>99 ) $archive_remind_num = '99+';



$tpl->assign('list', $list);
$tpl->assign('pending_remind_num', $pending_remind_num);
$tpl->assign('process_remind_num', $process_remind_num);
$tpl->assign('archive_remind_num', $archive_remind_num);

$tpl->assign('type', $type);

// print_r($list);


/*
 * 获取需求问卷问答
 * @param int $request_id
 * @return array
 */



$tpl->output();
 ?>