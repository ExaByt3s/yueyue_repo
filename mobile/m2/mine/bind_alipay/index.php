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

//检查模特机构
$pai_model_relate_org_obj = POCO::singleton('pai_model_relate_org_class');
$pai_model_relate_org_ret = $pai_model_relate_org_obj -> get_org_info_by_user_id($yue_login_id);

if ($pai_model_relate_org_ret) 
{
	$jg_model_url = './jg_model.php';
	header("Location:".$jg_model_url);
	die();
} 

if(empty($yue_login_id))
{
    die('no login');
}


$head_html = include_once($file_dir. '/../../webcontrol/head.php');

// 先检查是否绑定过
$pai_bind_account_obj = POCO::singleton('pai_bind_account_class');
$user_id    = $yue_login_id;
$type       = 'alipay_account';
$ret 		= $pai_bind_account_obj->get_bind_status($user_id,$type);

// 回链
$redirect_url = trim($_INPUT['redirect_url']);

// 替换手机号
if($ret['third_account'])
{
    $third_account = $ret['third_account'];
    $pattern = '/(\d{3})(\d{4})(\d{4})/i';
    $replacement = '$1****$3';
    $third_account = preg_replace($pattern, $replacement,$third_account);
}

//-1 未绑定 0 待审核 1已审核 2审核不通过
switch ($ret['status']) {
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
        $output_arr['msg']  = '已审核';
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

if($output_arr['code'] == -1 || $output_arr['code'] == 2)
{
    $output_arr['show_form'] = 1 ;
}
else{
    $output_arr['show_form'] = 0 ;
}

/**
 * 判断客户端
 */
$__is_weixin = stripos($_SERVER['HTTP_USER_AGENT'], 'micromessenger') ? true : false;
$__is_yueyue_app = (preg_match('/yue_pai/',$_SERVER['HTTP_USER_AGENT'])) ? true : false;  
$__is_yueseller_app = (preg_match('/yueseller/',$_SERVER['HTTP_USER_AGENT'])) ? true : false;  

if($__is_yueseller_app)
{
	$__is_yueyue_app = true;
}

$tpl = $my_app_pai->getView("index.tpl.html");
$tpl ->assign('is_App',$__is_yueyue_app);
$tpl ->assign('output_arr',$output_arr);
$tpl ->assign('head_html',$head_html);
$tpl->output();