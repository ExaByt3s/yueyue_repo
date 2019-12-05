<?php
/** 
 * 
 * tt
 * hudw
 * 2015-4-30
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

$tpl = $my_app_pai->getView('order_list.tpl.html');

$tpl->assign('test', $test);  //随机数

$tpl->assign('time', time());  //随机数

$type = $_INPUT['type'] ? $_INPUT['type'] : 'pending';


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
$tpl->assign('type', $type);


/**********分页处理**********/

// 总页数
$total_page = $page_obj->tpage;
// 当前页
$cur_page = $page_obj->curr;
// 上一页 下一页
$pre = $cur_page - 1 ;
$next = $cur_page + 1;
if($cur_page == 1)
{
	$pre = 1;
}
if($cur_page == $total_page)
{
	$next = $total_page;
}

if($total_count != 0)
{
	$tpl->assign ( "show_pager", 1);
}
else
{
	$tpl->assign ( "show_pager", 0);
}

// p 是默认页面参数
$tpl->assign ( "total_page", $total_page);
$tpl->assign ( "pre", $pre);
$tpl->assign ( "next", $next);
$tpl->assign ( "page_config_script", "<script>window.__page_config={total_page : ".$total_page.",pre:".$pre.",next:".$next.",cur_page:".$cur_page."}</script>");
/**********分页处理**********/




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