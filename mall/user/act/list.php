<?php
include_once 'config.php';

$keyword_query = trim($_INPUT['keyword_query']);
$title = trim($_INPUT['title']);
$time_querys = trim($_INPUT['time_querys']);
$price_querys = trim($_INPUT['price_querys']);
$start_querys = trim($_INPUT['start_querys']);
$remarks_querys = trim($_INPUT['remarks_querys']);

if(MALL_UA_IS_PC == 1)
{
	$act_list_url = 'http://event.poco.cn/event_list.php?category=0#search_key_word='.$keyword_query;
	header("Location:{$act_list_url}");
	die();
}
$pc_wap = 'wap/';
$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'act/list.tpl.html');

// 头部css相关
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/head.php');

// 头部公共样式和js引入
$wap_global_top = _get_wbc_head();
$tpl->assign('wap_global_top', $wap_global_top);

// 底部公共文件引入
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/footer.php');
$wap_global_footer = _get_wbc_footer();
$tpl->assign('wap_global_footer', $wap_global_footer);



$keyword_query = mb_convert_encoding(urldecode($keyword_query), 'gbk', 'utf8');

$title = mb_convert_encoding(urldecode($title), 'gbk', 'utf8');

if(!$title)
{
	$title = $keyword_query;
}

$tpl->assign('title',$title);
$tpl->assign('keyword_query',$keyword_query);

$tpl->assign('time_querys',$time_querys);
$tpl->assign('price_querys',$price_querys);
$tpl->assign('start_querys',$start_querys);
$tpl->assign('remarks_querys',$remarks_querys);



$tpl->output();
?>
