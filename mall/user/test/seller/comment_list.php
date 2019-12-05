<?php
include_once 'config.php';

// 权限检查
// $check_arr = mall_check_user_permissions($yue_login_id);

// 账号切换时
// if($check_arr['switch'] == 1)
// {
// 	$url='http://'.$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"]; 
// 	header("Location:{$url}");
// 	die();
// }

$pc_wap = 'wap/';
$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'seller/comment-list.tpl.htm');

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

$buyer_id = intval($_INPUT['buyer_id']);
$seller_user_id = intval($_INPUT['seller_user_id']);

$user_id = intval($_INPUT['user_id']);
$type = $_INPUT['type'];

// $params = parse_url("./comment_list.php") ;

//获取网页地址 
// echo $_SERVER['PHP_SELF']; 

//获取网址参数 
// echo $_SERVER["QUERY_STRING"]; 


if (!$_SERVER["QUERY_STRING"]) {
    echo "参数错误";
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