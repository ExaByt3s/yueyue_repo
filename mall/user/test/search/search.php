<?php
include_once 'config.php';
$pc_wap = 'wap/';
$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'search/search.tpl.htm');

// ͷ��css���
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/head.php');
// ͷ��������ʽ��js����
$wap_global_top = _get_wbc_head();
$tpl->assign('wap_global_top', $wap_global_top);

$type_id = intval($_INPUT["type_id"]);

$ret = get_api_result("customer/search_hot_tags.php",array(
        'user_id'=> $yue_login_id,
        'type_id' => $type_id
        ),$decode = TRUE, $to_gbk = TRUE,TRUE);

$data = $ret['data'];


$seller_tags = array();//"��С��","����","100004"
$service_tags = array();//"ģ��","˽��","�Ը�","���"

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