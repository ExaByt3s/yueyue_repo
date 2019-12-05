<?php
$task_templates_root = TASK_TEMPLATES_ROOT;

// 新版首页的变量
// hudw 2015.9.7
if(isset($index_template_root))
{
	$task_templates_root = $index_template_root.'/templates/default/';
}
//****************** wap版 头部通用 start  ******************
$pc_wap = 'wap/';
$tpl = $my_app_pai->getView($task_templates_root.$pc_wap.'/index.tpl.htm');

// 头部css相关
include_once($task_templates_root.$pc_wap. '/webcontrol/head.php');
// 底部公共文件引入
include_once($task_templates_root.$pc_wap. '/webcontrol/footer.php');
// 公共tips 引用
include_once($task_templates_root.$pc_wap. '/webcontrol/global_tips.php');


$pc_global_top = _get_wbc_head();
$wap_global_footer = _get_wbc_footer();
$wap_global_tips =  _get_wbc_tips();

$tpl->assign('wap_global_top', $pc_global_top);
$tpl->assign('wap_global_footer', $wap_global_footer);
$tpl->assign('wap_global_tips', $wap_global_tips);

//****************** wap版 头部通用 end  ******************

// hudw 搜索弹层数组配置
// 2015.11.10
$search_url = G_MALL_PROJECT_USER_ROOT . '/search/search.php?type=goods';
$search_icon_arr = array(
	0 => array(
		'class' => 'y-mt',
		'url'   => $search_url.'&type_id=31',
		'con'   => '约模特'
	),
	1 => array(
		'class' => 'y-px',
		'url'   => $search_url.'&type_id=5',
		'con'   => '约培训'
	),
	2 => array(
		'class' => 'y-hz',
		'url'   => $search_url.'&type_id=3',
		'con'   => '约化妆'
	),
	3 => array(
		'class' => 'y-dz',
		'url'   => $search_url.'&type_id=12',
		'con'   => '商业定制'
	),
	4 => array(
		'class' => 'y-hd',
		'url'   => $search_url.'&type_id=42',
		'con'   => '约活动'
	),
	5 => array(
		'class' => 'y-sy',
		'url'   => $search_url.'&type_id=40',
		'con'   => '约摄影'
	),	
	6 => array(
		'class' => 'y-ms',
		'url'   => $search_url.'&type_id=41',
		'con'   => '约美食'
	),	
	7 => array(
		'class' => 'y-yx',
		'url'   => $search_url.'&type_id=43',
		'con'   => '约有趣'
	)
);
$tpl->assign('search_icon_arr', $search_icon_arr);
$tpl->assign('ret_index_v2', $ret_index_v2['data']);


// 底部连接
// 首页 
$tpl->assign('index_link', G_MALL_PROJECT_USER_ROOT);
// 我的
$tpl->assign('my_link', G_MALL_PROJECT_USER_ROOT. '/home/');

$tpl->assign('location_id',$_COOKIE['yue_location_id']);


?>