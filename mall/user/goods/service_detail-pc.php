<?php

//****************** pc版头部通用 start ******************
$pc_wap = 'pc/';
$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'goods/service_detail.tpl.html');

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






$type_id = $ret['data']['profile_type'];





// 关键词配置
$keywords_key ='';
$description_key ='';



$tpl->assign('keywords_key', $keywords_key);
$tpl->assign('description_key', $description_key);

$tpl->assign('type_id', $type_id);
$tpl->assign('type_id_title', $MALL_COLUMN_CONFIG[$type_id]['key_nav']);


foreach ($ret['data']['standard'] as $key => $value) 
{
    $ret['data']['standard'][$key]['name'] = preg_replace("/\|@\|.*$/","",$value['name']);
}



 if ($_INPUT['print']) 
 {
     print_r($ret);
 }

?>
