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
$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'location/index.tpl.htm');


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

// ��������
// $location['city'] = '����';
// $location['location_id'] = 101029001;
// $data[] = $location;

// $location['city'] = '����';
// $location['location_id'] = 101001001;
// $data[] = $location;



// $location['city'] = '�Ϻ�';
// $location['location_id'] = 101003001;
// $data[] = $location;  
 
// $location['city'] = '�ɶ�';
// $location['location_id'] = 101022001;
// $data[] = $location;

// $location['city'] = '����';
// $location['location_id'] = 101004001;
// $data[] = $location;

// $location['city'] = '����';
// $location['location_id'] = 101015001;
// $data[] = $location;

// $location['city'] = '����';
// $location['location_id'] = 101029002;
// $data[] = $location;

//$location['city'] = '���';
//$location['location_id'] = 101002001;
//$data[] = $location;

// print_r($ret['data']);

// ������������
// $other_city_config = array(
//     0 => array(
//         'title' => '�㶫����',
//         'item' => array(
//             0 => array(
//                 'name' => '����',
//                 'location_id' => '101029002'
//             ),
//             1 => array(
//                 'name' => '�麣',
//                 'location_id' => '101029003'
//             ),
//             2 => array(
//                 'name' => '��ɽ',
//                 'location_id' => '101029004'
//             )
//         )
//     ),
//     1 => array(
//         'title' => '��������',
//         'item' => array(
//             0 => array(
//                 'name' => '����',
//                 'location_id' => '101017001'
//             ),
//             1 => array(
//                 'name' => '�Ͼ�',
//                 'location_id' => '101012001'
//             )
//         )
//     )

// );

// $tpl->assign('other_city_config', $other_city_config);


// // �������� end
// $tpl->assign('data_arr', $data);



$ret = get_api_result('customer/sell_location.php',array(
    'user_id' => $yue_login_id,
    'type_id' => $type_id,
    'query' => $query
    ));


$tpl->assign('ret', $ret['data']);


// ����
$r_url = urldecode($_INPUT['r_url']);
$tpl->assign('r_url', $r_url);


// ΢�Ŷ�ά��
$wx = mall_wx_get_js_api_sign_package();
$wx_json = mall_output_format_data($wx);
$tpl->assign('wx_json', $wx_json);

$tpl->assign('index_url', G_MALL_PROJECT_USER_ROOT.'/index.php');




$tpl->output();

?>