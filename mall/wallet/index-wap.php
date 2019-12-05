<?php
$task_templates_root = TASK_TEMPLATES_ROOT;

// 新版首页的变量
// hudw 2015.9.7
if(isset($index_template_root))
{
	$task_templates_root = $index_template_root.'/templates/default/';
}
//****************** wap版 头部通用 start  ******************
$pc_wap = 'wap/';
$tpl = $my_app_pai->getView($task_templates_root.$pc_wap.'/index.tpl.htm');

// 头部css相关
include_once($task_templates_root.$pc_wap. '/webcontrol/head.php');
// 底部公共文件引入
include_once($task_templates_root.$pc_wap. '/webcontrol/footer.php');



$pc_global_top = _get_wbc_head();
$wap_global_footer = _get_wbc_footer();


$tpl->assign('wap_global_top', $pc_global_top);
$tpl->assign('wap_global_footer', $wap_global_footer);


//****************** wap版 头部通用 end  ******************
//****************** wap版 用户绑定支付宝 start  ******************
// 先检查是否绑定过
$pai_bind_account_obj = POCO::singleton('pai_bind_account_class');
$user_id    = $yue_login_id;
$type       = 'alipay_account';
$ret_weixin 		= $pai_bind_account_obj->get_bind_status($user_id,$type);

// 替换手机号
if($ret_weixin['third_account'])
{
    $third_account = $ret_weixin['third_account'];
    $pattern = '/(\d{3})(\d{4})(\d{4})/i';
    $replacement = '$1****$3';
    $third_account = preg_replace($pattern, $replacement,$third_account);
}

//-1 未绑定 0 待审核 1已审核 2审核不通过
switch ($ret_weixin['status']) {
    case '-1':

        $output_arr['code'] = -1;
        $output_arr['msg']  = '未绑定支付宝账号';
        $output_arr['data'] = array();
        break;
    case '0':
        $output_arr['code'] = 0;
        $output_arr['msg']  = '待审核';

        $output_arr['data'] = array('third_account'=>$third_account);
        break;
    case '1':
        // 已经绑定就取绑定信息

        $output_arr['code'] = 1;
        $output_arr['msg']  = '已经绑定账号';
        $output_arr['data'] = array(
            'third_account'=>$third_account
        );
        break;
    case '2':
        $output_arr['code'] = 2;
        $output_arr['msg']  = '审核不通过';
        $output_arr['data'] = array();
        break;
    default:
        break;
}
//****************** wap版 用户支付宝绑定 end  ******************


$ret = get_api_result("customer/my.php",array
    (
        'user_id' =>$yue_login_id
    ),TRUE,TRUE,TRUE);

// 权限检查
$check_arr = mall_check_user_permissions($yue_login_id);

// 账号切换时
if(intval($check_arr['switch']) == 1)
{
	$url='http://'.$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"];
	header("Location:{$url}");
	die();
}

// 获取个人信息
$user_obj = POCO::singleton('pai_user_class');
$user_info = $user_obj->get_user_info_by_user_id($yue_login_id);

// 钱包余额
$available_balance =  $user_info['available_balance'];

$data = $ret['data'];

$nickname = $data['nickname'];
$coupon_num = $data['coupon_num'];
$icon = $data['icon'];


$tpl->assign('buyer_id',$yue_login_id);
$tpl->assign('nickname',$nickname);
$tpl->assign('available_balance',$available_balance);
$tpl->assign('coupon_num',$coupon_num);
$tpl->assign('icon',$icon);
$tpl ->assign('output_arr',$output_arr);
$tpl ->assign('is_not_weixin',TRUE);


?>