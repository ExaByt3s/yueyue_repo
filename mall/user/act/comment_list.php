<?php
include_once 'config.php';

// // Ȩ�޼��
// $check_arr = mall_check_user_permissions($yue_login_id);

// // �˺��л�ʱ
// if($check_arr['switch'] == 1)
// {
// 	$url='http://'.$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"]; 
// 	header("Location:{$url}");
// 	die();
// }

$pc_wap = 'wap/';
$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'act/comment-list.tpl.htm');

// // û�е�¼�Ĵ���
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


$event_id = intval($_INPUT['event_id']);
$type = $_INPUT['type'];

// $params = parse_url("./comment_list.php") ;

//��ȡ��ҳ��ַ 
// echo $_SERVER['PHP_SELF']; 

//��ȡ��ַ���� 
// echo $_SERVER["QUERY_STRING"]; 
// echo "123";

if (!$_SERVER["QUERY_STRING"]) {
    echo "��������";
    exit();
}

$page_params = array(
    'event_id' => $event_id,
    'type' => $type
);
$page_params = mall_output_format_data($page_params);

$tpl->assign('page_params',$page_params);




$tpl->output();

?>