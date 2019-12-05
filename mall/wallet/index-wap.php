<?php
$task_templates_root = TASK_TEMPLATES_ROOT;

// �°���ҳ�ı���
// hudw 2015.9.7
if(isset($index_template_root))
{
	$task_templates_root = $index_template_root.'/templates/default/';
}
//****************** wap�� ͷ��ͨ�� start  ******************
$pc_wap = 'wap/';
$tpl = $my_app_pai->getView($task_templates_root.$pc_wap.'/index.tpl.htm');

// ͷ��css���
include_once($task_templates_root.$pc_wap. '/webcontrol/head.php');
// �ײ������ļ�����
include_once($task_templates_root.$pc_wap. '/webcontrol/footer.php');



$pc_global_top = _get_wbc_head();
$wap_global_footer = _get_wbc_footer();


$tpl->assign('wap_global_top', $pc_global_top);
$tpl->assign('wap_global_footer', $wap_global_footer);


//****************** wap�� ͷ��ͨ�� end  ******************
//****************** wap�� �û���֧���� start  ******************
// �ȼ���Ƿ�󶨹�
$pai_bind_account_obj = POCO::singleton('pai_bind_account_class');
$user_id    = $yue_login_id;
$type       = 'alipay_account';
$ret_weixin 		= $pai_bind_account_obj->get_bind_status($user_id,$type);

// �滻�ֻ���
if($ret_weixin['third_account'])
{
    $third_account = $ret_weixin['third_account'];
    $pattern = '/(\d{3})(\d{4})(\d{4})/i';
    $replacement = '$1****$3';
    $third_account = preg_replace($pattern, $replacement,$third_account);
}

//-1 δ�� 0 ����� 1����� 2��˲�ͨ��
switch ($ret_weixin['status']) {
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
        $output_arr['msg']  = '�Ѿ����˺�';
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
//****************** wap�� �û�֧������ end  ******************


$ret = get_api_result("customer/my.php",array
    (
        'user_id' =>$yue_login_id
    ),TRUE,TRUE,TRUE);

// Ȩ�޼��
$check_arr = mall_check_user_permissions($yue_login_id);

// �˺��л�ʱ
if(intval($check_arr['switch']) == 1)
{
	$url='http://'.$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"];
	header("Location:{$url}");
	die();
}

// ��ȡ������Ϣ
$user_obj = POCO::singleton('pai_user_class');
$user_info = $user_obj->get_user_info_by_user_id($yue_login_id);

// Ǯ�����
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