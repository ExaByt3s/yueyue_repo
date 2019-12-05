<?php
include_once 'config.php';

// 权限检查
$check_arr = mall_check_user_permissions($yue_login_id);

// 账号切换时
if($check_arr['switch'] == 1)
{
	$url='http://'.$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"]; 
	header("Location:{$url}");
	die();
}


$pc_wap = 'wap/';
$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'order/detail.tpl.htm');

// 没有登录的处理
if(empty($yue_login_id))
{
    $output_arr['code'] = -1;
    $output_arr['msg']  = '尚未登录,非法操作';
    $output_arr['data'] = array();
    exit();
}




$order_sn = trim($_INPUT['order_sn']);
if(empty($order_sn))
{
    echo "缺少订单参数";
    exit();
}

// 订单类：pai_mall_order_class
$mall_order_obj = POCO::singleton('pai_mall_order_class');
$ret_order_full_info = $mall_order_obj -> get_order_full_info($order_sn, $yue_login_id);

if($ret_order_full_info['order_type'] == 'activity')
{
    $url='http://'.$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"]; 
    if(!preg_match('test', $url))
    {
        $url = str_replace('/user/', '/user/test/', $url);
    }
    
    header("Location:{$url}");
    die();
}

if($ret_order_full_info['buyer_user_id'] != $yue_login_id) 
{
	header('Location:./list.php');
	die();
}


if($_INPUT['print'] == 1)
{
	print_r($ret_order_full_info);
	die();
}

if(!$ret_order_full_info)
{
	header("Location:./list.php");
	die();
}

// 处理星星bug
// hudw 2015.9.28
$ret_order_full_info['detail_list'][0]['prices_spec'] = preg_replace('/\|@\|/', '', $ret_order_full_info['detail_list'][0]['prices_spec']);


//$ret_order_full_info['is_special'] 是否特殊商品，1不允许跳转到商品详情，0可以跳转到商品详情

$btn_action = btn_action($ret_order_full_info['status'], $ret_order_full_info['is_buyer_comment']);
$ret_order_full_info['btn_action'] = $btn_action;


// 设置按钮状态
function btn_action($status, $is_buyer_comment) {
    if ($is_buyer_comment == 1) {  // 商家已评价
        return array();
    }
    // 按钮文案
    $action_arr = array(
        0 => '关闭.close|支付.pay',
        1 => '取消订单.cancel',
        2 => '申请退款.refund|出示二维码.sign',
        7 => '删除订单.delete',
        8 => '评价订单.appraise'
    );
    $btn = explode('|', $action_arr[$status]);
    $arr = array();
    foreach ($btn as $value) {
        if (empty($value)) {
            continue;
        }
        list($name, $request) = explode('.', $value);
        if (empty($name) || empty($request)) {
            continue;
        }
        $arr[] = array(
            'title' => $name,
            'request' => $request, // $user_id, $order_sn
        );
    }
    return $arr;
}


// 二维码图片转换 start 
$activity_code_obj = POCO::singleton('pai_activity_code_class');
foreach ($ret_order_full_info['code_list'] as $k => $val) 
{
    $qr_code_url_img = $activity_code_obj->get_qrcode_img($val['qr_code_url']);
    $ret_order_full_info['code_list'][$k]['qr_code_url_img'] = $qr_code_url_img;
}
// 二维码图片转换 end


// print_r($ret_order_full_info);

$tpl->assign('ret_order_full_info',$ret_order_full_info);
$tpl->assign('order_sn',$order_sn);
// $tpl->assign($ret_order_full_info);







if ($_INPUT['print']) 
{
    print_r($ret_order_full_info);
	die();
}



// 根据订单查询用户信息



// **********  用户聊天信息配置  start  ******************
$order_info = $mall_order_obj -> get_order_info($order_sn);
$seller_user_id = $order_info['seller_user_id'];
$sendername = get_user_nickname_by_user_id($yue_login_id);
$receivername = get_seller_nickname_by_user_id($seller_user_id);
$sendericon = get_user_icon($yue_login_id,165);
$receivericon = get_seller_user_icon($seller_user_id,165);
$ret_json = array(
    'senderid' => $yue_login_id,
    'receiverid' => $seller_user_id,
    'sendername' => $sendername,
    'receivername' => $receivername,
    'sendericon' => $sendericon,
    'receivericon' => $receivericon
);
// print_r($ret_json);
$ret_json = mall_output_format_data($ret_json);
$tpl->assign('chat_json',urlencode($ret_json));
// **********  用户聊天信息配置  end   ******************

$tpl->assign('pay_url', '../pay/?order_sn='.$order_sn);

// 头部css相关
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/head.php');
// 头部公共样式和js引入
$pc_global_top = _get_wbc_head();
$tpl->assign('pc_global_top', $pc_global_top);

// 底部公共文件引入
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/footer.php');
$wap_global_footer = _get_wbc_footer();
$tpl->assign('wap_global_footer', $wap_global_footer);

$tpl->output();
?>