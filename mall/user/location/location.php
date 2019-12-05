<?php
include_once 'config.php';

// // 权限检查
// $check_arr = mall_check_user_permissions($yue_login_id);

// // 账号切换时
// if($check_arr['switch'] == 1)
// {
// 	$url='http://'.$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"]; 
// 	header("Location:{$url}");
// 	die();
// }

$pc_wap = 'wap/';
$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'location/index.tpl.htm');


// 没有登录的处理
// if(empty($yue_login_id))
// {
//     $output_arr['code'] = -1;
//     $output_arr['msg']  = '尚未登录,非法操作';
//     $output_arr['data'] = array();
//     exit();
// }

// 头部css相关
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/head.php');
// 头部公共样式和js引入
$pc_global_top = _get_wbc_head();
$tpl->assign('pc_global_top', $pc_global_top);

// 底部公共文件引入
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/footer.php');
$wap_global_footer = _get_wbc_footer();
$tpl->assign('wap_global_footer', $wap_global_footer);

// 地区配置
// $location['city'] = '广州';
// $location['location_id'] = 101029001;
// $data[] = $location;

// $location['city'] = '北京';
// $location['location_id'] = 101001001;
// $data[] = $location;



// $location['city'] = '上海';
// $location['location_id'] = 101003001;
// $data[] = $location;  
 
// $location['city'] = '成都';
// $location['location_id'] = 101022001;
// $data[] = $location;

// $location['city'] = '重庆';
// $location['location_id'] = 101004001;
// $data[] = $location;

// $location['city'] = '西安';
// $location['location_id'] = 101015001;
// $data[] = $location;

// $location['city'] = '深圳';
// $location['location_id'] = 101029002;
// $data[] = $location;

//$location['city'] = '天津';
//$location['location_id'] = 101002001;
//$data[] = $location;

// print_r($ret['data']);

// 其他城市配置
// $other_city_config = array(
//     0 => array(
//         'title' => '广东区域',
//         'item' => array(
//             0 => array(
//                 'name' => '深圳',
//                 'location_id' => '101029002'
//             ),
//             1 => array(
//                 'name' => '珠海',
//                 'location_id' => '101029003'
//             ),
//             2 => array(
//                 'name' => '中山',
//                 'location_id' => '101029004'
//             )
//         )
//     ),
//     1 => array(
//         'title' => '东部区域',
//         'item' => array(
//             0 => array(
//                 'name' => '杭州',
//                 'location_id' => '101017001'
//             ),
//             1 => array(
//                 'name' => '南京',
//                 'location_id' => '101012001'
//             )
//         )
//     )

// );

// $tpl->assign('other_city_config', $other_city_config);


// // 地区配置 end
// $tpl->assign('data_arr', $data);



$ret = get_api_result('customer/sell_location.php',array(
    'user_id' => $yue_login_id,
    'type_id' => $type_id,
    'query' => $query
    ));


$tpl->assign('ret', $ret['data']);


// 回链
$r_url = urldecode($_INPUT['r_url']);
$tpl->assign('r_url', $r_url);


// 微信二维码
$wx = mall_wx_get_js_api_sign_package();
$wx_json = mall_output_format_data($wx);
$tpl->assign('wx_json', $wx_json);

$tpl->assign('index_url', G_MALL_PROJECT_USER_ROOT.'/index.php');




$tpl->output();

?>