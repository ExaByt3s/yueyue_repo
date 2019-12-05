<?php

//****************** pc版头部通用 start ******************

$pc_wap = 'pc/';
$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'/category/index.tpl.htm');

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





$tpl->assign('pc_global_top', $pc_global_top);
$tpl->assign('global_top_bar', $global_top_bar);
$tpl->assign('footer', $footer);
$tpl->assign('down_app_area', $down_app_area);

$tpl->assign('index_url', G_MALL_PROJECT_USER_INDEX_DOMAIN);
// ================== pc版头部通用 end ==================


$output_arr['input_data']['data'] = array
(

    0 => array(
        'text' => '',
        'place_holder' => '关键字',
        'default_text' => '',
        'default_url' => '',
        'type_id' => $type_id,
        'search_type' => 'goods',
        'show' => true
    ),
    1 => array(
        'text' => '',
        'place_holder' => '商品ID/商家名称',
        'default_text' => '',
        'default_url' => '',
        'type_id' => $type_id,
        'search_type' => 'seller',
    )

);
$output_arr['input_data']['search_type'] = "goods";
$tpl->assign('page_data',mall_output_format_data($output_arr));


$key_current = $MALL_COLUMN_CONFIG[$type_id] ;

$tpl->assign('key_current', $key_current);


$tpl->assign('ret', $ret['data']);



?>