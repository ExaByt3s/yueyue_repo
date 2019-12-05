<?php
/**
 * Created by PhpStorm.
 * User: hudingwen
 * Date: 15/6/3
 * Time: 下午3:29
 */

/**
 * 引用资源文件定位，注意！确保引用路径争取
 */
$file_dir = dirname(__FILE__);

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

if(empty($yue_login_id))
{
    die('no login');
}

//检查模特机构
$pai_model_relate_org_obj = POCO::singleton('pai_model_relate_org_class');
$pai_model_relate_org_ret = $pai_model_relate_org_obj -> get_org_info_by_user_id($yue_login_id);

if ($pai_model_relate_org_ret) 
{
	$jg_model_url = '../bind_alipay/jg_model.php';
	header("Location:".$jg_model_url);
	die();
} 


$head_html = include_once($file_dir. '/../../webcontrol/head.php');

// 先检查是否绑定过
$pai_bind_account_obj = POCO::singleton('pai_bind_account_class');
$user_id    = $yue_login_id;
$type       = 'alipay_account';
$ret 		= $pai_bind_account_obj->get_bind_status($user_id,$type);

if($ret['status'] == '1')
{
    // 已经绑定就取绑定信息
    $pai_payment_obj 	  = POCO::singleton('pai_payment_class');
    $available_balance = $pai_payment_obj->get_user_available_balance($user_id);
    $purse_available_balance = $pai_payment_obj->get_purse_available_balance($user_id);


    $output_arr['code'] = 1;
    $output_arr['msg']  = '已审核';
    $output_arr['data'] = array(
        'third_account'=>$ret['third_account'],
        'available_balance'=>$available_balance,
        'purse_available_balance'=>$purse_available_balance,
        );
}
else
{
    $redirect_url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    header('Location:../bind_alipay/index.php?redirect_url='.urlencode($redirect_url));
    exit();
}


$tpl = $my_app_pai->getView("index.tpl.html");
$tpl ->assign('output_arr',$output_arr);
$tpl ->assign('head_html',$head_html);
$tpl->output();