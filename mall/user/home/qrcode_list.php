<?php
include_once 'config.php';

// Ȩ�޼��
$check_arr = mall_check_user_permissions($yue_login_id);

// �˺��л�ʱ
if($check_arr['switch'] == 1)
{
	$url='http://'.$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"]; 
	header("Location:{$url}");
	die();
}

$pc_wap = 'wap/';
$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'home/qrcode_list.tpl.htm');

// û�е�¼�Ĵ���
if(empty($yue_login_id))
{
    $output_arr['code'] = -1;
    $output_arr['msg']  = '��δ��¼,�Ƿ�����';
    $output_arr['data'] = array();
    exit();
}

// ͷ��css���
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/head.php');
// ͷ��������ʽ��js����
$pc_global_top = _get_wbc_head();
$tpl->assign('pc_global_top', $pc_global_top);

// �ײ������ļ�����
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/footer.php');
$wap_global_footer = _get_wbc_footer();
$tpl->assign('wap_global_footer', $wap_global_footer);


$ret = get_api_result('customer/get_qrcode_list.php',array(
    'user_id' => $yue_login_id,
    'page' => 1 ,
    'row' => 5
    ), true); 

if ($_INPUT['print']) 
{
    print_r($ret );
}


$tpl->assign('user_id', $ret['data']['user_id']);
$tpl->assign('nickname', $ret['data']['nickname']);
$tpl->assign('icon', $ret['data']['icon']);

$tpl->output();

?>