<?php
/**
 * Created by PhpStorm.
 * User: hudingwen
 * Date: 15/6/3
 * Time: ����3:29
 */

/**
 * ������Դ�ļ���λ��ע�⣡ȷ������·����ȡ
 */
$file_dir = dirname(__FILE__);

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

//���ģ�ػ���
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

// �ȼ���Ƿ�󶨹�
$pai_bind_account_obj = POCO::singleton('pai_bind_account_class');
$user_id    = $yue_login_id;
$type       = 'alipay_account';
$ret 		= $pai_bind_account_obj->get_bind_status($user_id,$type);

// ����
$redirect_url = trim($_INPUT['redirect_url']);

// �滻�ֻ���
if($ret['third_account'])
{
    $third_account = $ret['third_account'];
    $pattern = '/(\d{3})(\d{4})(\d{4})/i';
    $replacement = '$1****$3';
    $third_account = preg_replace($pattern, $replacement,$third_account);
}

//-1 δ�� 0 ����� 1����� 2��˲�ͨ��
switch ($ret['status']) {
    case '-1':

        $output_arr['code'] = -1;
        $output_arr['msg']  = 'δ��֧�����˺�';
        $output_arr['data'] = array();
        break;
    case '0':
        $output_arr['code'] = 0;
        $output_arr['msg']  = '�����';

        $output_arr['data'] = array('third_account'=>$third_account);
        break;
    case '1':
        // �Ѿ��󶨾�ȡ����Ϣ

        $output_arr['code'] = 1;
        $output_arr['msg']  = '�����';
        $output_arr['data'] = array(
            'third_account'=>$third_account
        );
        break;
    case '2':
        $output_arr['code'] = 2;
        $output_arr['msg']  = '��˲�ͨ��';
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
 * �жϿͻ���
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