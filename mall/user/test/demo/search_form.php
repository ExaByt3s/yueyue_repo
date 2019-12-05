<?php
include_once 'config.php';
$pc_wap = 'wap/';
$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'demo/search_form.tpl.htm');

// 头部css相关
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/head.php');
// 头部公共样式和js引入
$wap_global_top = _get_wbc_head();
$tpl->assign('wap_global_top', $wap_global_top);

// 底部公共文件引入
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/footer.php');
$wap_global_footer = _get_wbc_footer();
$tpl->assign('wap_global_footer', $wap_global_footer);

$ret = get_api_result("customer/search_hot_tags.php",array(
        'user_id'=> $yue_login_id,
        'type_id' => $type_id
        ),$decode = TRUE, $to_gbk = TRUE,TRUE);

$data = $ret['data'];


$seller_tags = array();//"赵小花","米奇","100004"
$service_tags = array();//"模特","私房","性感","真空"

$service_tags = mall_output_format_data($service_tags);
$seller_tags = mall_output_format_data($seller_tags);


$tpl->assign('seller_tags',$seller_tags);
$tpl->assign('service_tags',$service_tags);
$tpl->assign('type_id',$type_id);
if($yue_login_id == 100001)
{
	$tpl->assign('is_test',1);
}


$tpl->output();
?>