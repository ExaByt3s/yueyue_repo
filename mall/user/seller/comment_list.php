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
$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'seller/comment-list.tpl.htm');

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

$buyer_id = intval($_INPUT['buyer_id']);
$seller_user_id = intval($_INPUT['seller_user_id']);

$user_id = intval($_INPUT['user_id']);
$type = $_INPUT['type'];

// $params = parse_url("./comment_list.php") ;

//��ȡ��ҳ��ַ 
// echo $_SERVER['PHP_SELF']; 

//��ȡ��ַ���� 
// echo $_SERVER["QUERY_STRING"]; 


if (!$_SERVER["QUERY_STRING"]) {
    echo "��������";
    exit();
}

$page_params = $_GET;

$page_params['type'] = 'seller';



if($_INPUT['print'] == 1)
{
	print_r($buyer_id);
	die();
}

$page_params = mall_output_format_data($page_params);
$tpl->assign('page_params',$page_params);




$tpl->output();

?>