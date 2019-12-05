<?php

//****************** wap版 头部通用 start  ******************

$pc_wap = 'wap/';
$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'channel/index.tpl.htm');

// 头部css相关
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/head.php');
// 底部公共文件引入
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/footer.php');

// 输出页面参数
$page_params = mall_output_format_data(array
	(
		'query'=>$query,
		'type_id'=>$type_id,
	));

$ret = get_api_result('customer/classify_list_min.php',array(
    'query' => $query,
    'type_id' => $type_id,
	'user_id' => $yue_login_id,
	'limit' => $limit,
    'location_id' => empty($_COOKIE['yue_location_id']) ? 101029001 : $_COOKIE['yue_location_id']
	));

$title = $ret['data']["title"];

$wap_global_top = _get_wbc_head();
$wap_global_footer = _get_wbc_footer();

$tpl->assign('wap_global_top', $wap_global_top);
$tpl->assign('wap_global_footer', $wap_global_footer);
//****************** wap版 头部通用 end  ******************



// $tpl->assign('ret', $ret['data']);

$tpl->assign('page_params',$page_params);
$tpl->assign('title', $title);


?>