<?php
include_once 'config.php';

// 权限检查
// $check_arr = mall_check_user_permissions($yue_login_id);

// // 账号切换时
// if($check_arr['switch'] == 1)
// {
// 	$url='http://'.$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"]; 
// 	header("Location:{$url}");
// 	die();
// }

$pc_wap = 'wap/';
$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'home/user_info.tpl.htm');

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

if(empty($buyer_id))
{
	$buyer_id = $yue_login_id;
}

if($yue_login_id == $buyer_id)
{
	// 看自己
	$is_your_self = true;
}
else
{
	// 看别人
	$is_your_self = false;
}

$ret = get_api_result('customer/buyer_user.php',array(
	'user_id' => $yue_login_id,
    'buyer_id'=> $buyer_id
    ), true); 



// 计算星星宽度 start
// $stars =  4.5 ;
$stars =  $ret['data']['business']['score'] ;

$stars_width = (($stars/5)*100)."%";
$tpl->assign('stars_width',$stars_width);
// 计算星星宽度 end


// 只显示5个消费记录
$new_recore = array_slice($ret['data']['record'], 0, 5); 
$ret['data']['new_recore'] = $new_recore ;



if ($_INPUT['print']) 
{
    print_r($ret);
}


// 输出分享内容
$share = mall_output_format_data($ret['data']['share']);


$tpl->assign($ret['data']);
$tpl->assign('is_your_self',$is_your_self);
$tpl->assign('share',$share);

$tpl->output();

?>