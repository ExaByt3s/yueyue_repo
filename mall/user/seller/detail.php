<?php
include_once 'config.php';

// Ȩ�޼��
// $check_arr = mall_check_user_permissions($yue_login_id);

// �˺��л�ʱ
// if($check_arr['switch'] == 1)
// {
// 	$url='http://'.$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"]; 
// 	header("Location:{$url}");
// 	die();
// }

$pc_wap = 'wap/';
$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'seller/detail.tpl.htm');

// û�е�¼�Ĵ���
// if(empty($yue_login_id))
// {
//     $output_arr['code'] = -1;
//     $output_arr['msg']  = '��δ��¼,�Ƿ�����';
//     $output_arr['data'] = array();
//     exit();
// }

// ͷ��css���
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/head.php');
// ͷ��������ʽ��js����
$pc_global_top = _get_wbc_head();
$tpl->assign('pc_global_top', $pc_global_top);

// �ײ������ļ�����
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/footer.php');
$wap_global_footer = _get_wbc_footer();
$tpl->assign('wap_global_footer', $wap_global_footer);



$seller_user_id = intval($_INPUT['seller_user_id']);


if (empty($seller_user_id)) {
    echo "��������";
    exit() ;
}

// ====== ����Ԥ������ ======
// hudw 2015.9.6
if(intval($_INPUT['preview']) == 1)
{
	$ret = get_api_result('customer/trade_seller_detail_preview.php',array(
    'user_id' => $yue_login_id,
    'seller_user_id'=> trim($_INPUT['seller_user_id']) 
    ), true); 


}
else
{
	$ret = get_api_result('customer/trade_seller_detail.php',array(
    'user_id' => $yue_login_id,
    'seller_user_id'=> $seller_user_id 
    ), true); 
}




// print_r($ret);

if ($_INPUT['print']) 
{
    print_r($ret);
}

$info = $ret['data']['introduce'];

$tpl->assign('info',$info);

$tpl->output();

?>