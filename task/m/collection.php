<?php

 
include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');
include_once('./common_head.php');
$tpl = $my_app_pai->getView('collection.tpl.htm');

// 公共样式和js引入
$m_task_top = $my_app_pai->webControl('m_task_top', array(), true);
$tpl->assign('m_task_top', $m_task_top);

// // 头部引入
$m_global_top = $my_app_pai->webControl('m_global_top', array(), true);
$tpl->assign('m_global_top', $m_global_top);

// // 底部引入
$m_global_bot = $my_app_pai->webControl('m_global_bot', array(), true);
$tpl->assign('m_global_bot', $m_global_bot);

$task_quotes_obj = POCO::singleton('pai_task_quotes_class');
$page_obj = new show_page ();

$show_count = 10;

$page_obj->setvar (array("type"=>$type));

$type = 'archive';
switch ($type) {
	
	
	case 'archive':
		$total_count = $task_quotes_obj->get_archive_quotes_list($yue_login_id, true);
		$page_obj->set ( $show_count, $total_count );
		$list = $task_quotes_obj->get_archive_quotes_list($yue_login_id, false, $page_obj->limit (), '', 'is_important DESC,quotes_id DESC');
        $tpl->assign('type', $type);

        $title_txt = "收藏";


		// 组合回答
		foreach ($list as $k => $val) {
			$request_id = $val['request_id'];
			$obj = POCO::singleton('pai_task_questionnaire_class');
			$arr = $obj->show_questionnaire_data($request_id);
			$list[$k]['answer'] = $arr['data'] ;
		}
		if($total_count != 0)
		{
			$tpl->assign ( "show_pager", 1);
		}
		else
		{
			$tpl->assign ( "show_pager", 0);
		}
	break;
	
}

/*
 * 获取需求问卷问答
 * @param int $request_id
 * @return array
 */

foreach ($list as $k => $val) {
    $request_id = $val['request_id'];
    $obj = POCO::singleton('pai_task_questionnaire_class');
    $arr = $obj->show_questionnaire_data($request_id);
    $list[$k]['answer'] = $arr['data'] ;
}

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

// p 是默认页面参数
$tpl->assign ( "total_page", $total_page);
$tpl->assign ( "pre", $pre);
$tpl->assign ( "next", $next);
$tpl->assign ( "page_config_script", "<script>window.__page_config={total_page : ".$total_page.",pre:".$pre.",next:".$next.",cur_page:".$cur_page."}</script>");
/**********分页处理**********/

$task_quotes_obj = POCO::singleton('pai_task_quotes_class');
	$pending_remind_num = $task_quotes_obj->get_pending_quotes_list($yue_login_id, true, '', 'is_important=1');
	$process_remind_num = $task_quotes_obj->get_process_quotes_list($yue_login_id, true, '', 'is_important=1');
	$archive_remind_num = $task_quotes_obj->get_archive_quotes_list($yue_login_id, true, '', 'is_important=1');
	$tpl ->assign('pending_remind_num',$pending_remind_num);
	$tpl ->assign('process_remind_num',$process_remind_num);
	$tpl ->assign('archive_remind_num',$archive_remind_num);

$tpl->assign('list', $list);
$tpl->assign ( "page", $page_obj->output ( 1 ) );
$tpl->assign('type', $type);
$tpl->assign('time', time());  //随机数

$tpl->output();
 ?>