<?php

//****************** pc版头部通用 start ******************
$pc_wap = 'pc/';
$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'goods/category.tpl.htm');

// 头部css相关
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/head.php');
// 头部bar
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/global-top-bar.php');
// 底部
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/footer.php');
// 下载区域
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/down-app-area.php');

$pc_global_top = _get_wbc_head();
$global_top_bar = _get_wbc_global_top_bar();
$footer = _get_wbc_footer();
$down_app_area = _get_wbc_down_app_area();


$tpl->assign('pc_global_top', $pc_global_top);
$tpl->assign('global_top_bar', $global_top_bar);
$tpl->assign('footer', $footer);
$tpl->assign('down_app_area', $down_app_area);

$tpl->assign('index_url', G_MALL_PROJECT_USER_INDEX_DOMAIN);
// ================== pc版头部通用 end ==================

// 特殊处理一个banner跳转
// hudw 2015.10.13
foreach ($ret['data']['banner_list'] as $key => $value) 
{

	if(preg_match('/00GPVUEW/', $value['url']))
	{
		$ret['data']['banner_list'][$key]['url'] = 'http://www.yueus.com/mall/user/topic/index.php?topic_id=712&online=1';
		break;
	}
}


$key_current = $MALL_COLUMN_CONFIG[$type_id] ;


$tpl->assign('key_current', $key_current);
$tpl->assign('resa', $ret);
$tpl->assign($ret['data']);

if ($_INPUT['print']) 
{
    print_r($ret['data']);
}


?>
