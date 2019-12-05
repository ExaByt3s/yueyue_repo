<?php

//****************** wap版 头部通用 start  ******************

$pc_wap = 'wap/';
$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'category/index.tpl.htm');

// 头部css相关
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/head.php');
// 底部公共文件引入
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/footer.php');

$wap_global_top = _get_wbc_head();
$wap_global_footer = _get_wbc_footer();

$tpl->assign('wap_global_top', $wap_global_top);
$tpl->assign('wap_global_footer', $wap_global_footer);
//****************** wap版 头部通用 end  ******************

// 临时加上的分享内容
// hudw 2015.10.1

$share_txt_arr = array
(
	31 => '约模特 | 100000+模特随心约',// 约模特
	5  => '约培训 | 名师大咖让你学习无忧', // 约培训
	3  => '约化妆 | 海量妆容为你量身定制', // 约化妆
	12 => '商业定制 | 一站式满足多种商拍需求', // 商业定制
	99 => '约活动 | 最火爆的精彩活动尽在掌握', // 约活动
	40 => '约摄影 | 多快好省的拍摄套餐想拍就拍',// 约摄影
	41 => '约美食 | 美食达人带你开启独特的味蕾之旅', // 约美食
	43 => '约有趣 | 全国最TOP的潮玩达人带你飞' // 约有趣 
);

$type_id = intval($_INPUT['type_id']);
$share_txt_str = $share_txt_arr[$type_id];


$tpl->assign('share_txt_str', $share_txt_str);
$tpl->assign('ret', $ret['data']);



?>