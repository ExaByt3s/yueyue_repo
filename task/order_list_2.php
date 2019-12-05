<?php
/** 
 * 
 * tt
 * 汤圆
 * 2015-4-11
 * 
 */
 
include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');
$tpl = $my_app_pai->getView('order_list.tpl.htm');

$tpl->assign('test', $test);  //随机数

$tpl->assign('time', time());  //随机数

$type = $_INPUT['type'] ? $_INPUT['type'] : 'pending';


// 公共样式和js引入
$pc_global_top = $my_app_pai->webControl('pc_task_top', array(), true);
$tpl->assign('pc_task_top', $pc_global_top);
echo time().'====';

// // 头部引入
//$header_html = $my_app_pai->webControl('pc_task_header', array("cur_page"=>"order_list"), true);
echo time().'====';

$tpl->assign('header_html', $header_html);
exit('cccccccc');



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



//红色数字
$pending_remind_num = $task_quotes_obj->get_pending_quotes_list($yue_login_id, true, '', 'is_important=1');
if( $pending_remind_num>99 ) $pending_remind_num = '99+';
$process_remind_num = $task_quotes_obj->get_process_quotes_list($yue_login_id, true, '', 'is_important=1');
if( $process_remind_num>99 ) $process_remind_num = '99+';
$archive_remind_num = $task_quotes_obj->get_archive_quotes_list($yue_login_id, true, '', 'is_important=1');
if( $archive_remind_num>99 ) $archive_remind_num = '99+';

// 底部
$footer_html = $my_app_pai->webControl('pc_task_footer', array(), true);
$tpl->assign('footer_html', $footer_html);


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