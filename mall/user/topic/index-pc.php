<?php

//****************** pc版头部通用 start ******************
$pc_wap = 'pc/';
$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'/topic/topic.tpl.htm');

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

if(MALL_UA_IS_YUEYUE != 1)
{
	
    $pat = '/<a(.*?)href=[\'\"]?([^\'\" ]+).*?>/i';

    $ret['content'] = preg_replace_callback($pat,"mall_topic_page_replace_function",$ret['content']);
	
	
}


// 指定专题，宽度为1000 特殊情况处理
if( $id == 712 || $id == 714 )
{
    $page_width = '1000px' ;
}



$tpl->assign('pc_global_top', $pc_global_top);
$tpl->assign('global_top_bar', $global_top_bar);
$tpl->assign('footer', $footer);
$tpl->assign('down_app_area', $down_app_area);
$tpl->assign('page_width', $page_width);

$tpl->assign('index_url', G_MALL_PROJECT_USER_INDEX_DOMAIN);
// ================== pc版头部通用 end ==================


$tpl->assign('ret',$ret);
$tpl->assign('share_text',$share_text);

?>