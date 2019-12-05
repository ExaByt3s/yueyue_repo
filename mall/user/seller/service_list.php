<?php
include_once 'config.php';


$pc_wap = 'wap/';
$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'seller/service_list.tpl.htm');


// 头部css相关
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/head.php');
// 头部公共样式和js引入
$pc_global_top = _get_wbc_head();
$tpl->assign('pc_global_top', $pc_global_top);

// 底部公共文件引入
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/footer.php');
$wap_global_footer = _get_wbc_footer();
$tpl->assign('wap_global_footer', $wap_global_footer);

$service_status = trim($_INPUT['service_status']);
$goods_type = trim($_INPUT['goods_type']);
$tag = trim($_INPUT['tag']);
$keyword = trim($_INPUT['keyword']);
$seller_user_id = intval($_INPUT['seller_user_id']);

$page_params = array(
    'user_id' => $yue_login_id,
    'page_params' => $goods_type,
    'service_status' => 'on_sell' ,
    'keyword' => $keyword,
	'seller_user_id' =>$seller_user_id
);

if(empty($tag))
{
	$tag = 'goods';
}

$page_params = mall_output_format_data($page_params);
$tpl->assign('page_params',$page_params);
$tpl->assign('tag',$tag);

$tpl->output();

?>