<?php
include_once 'config.php';
$pc_wap = 'wap/';
$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'home/index.tpl.html');

// ͷ��css���
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/head.php');
// ͷ��������ʽ��js����
$wap_global_top = _get_wbc_head();
$tpl->assign('wap_global_top', $wap_global_top);

// �ײ������ļ�����
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/footer.php');
$wap_global_footer = _get_wbc_footer();
$tpl->assign('wap_global_footer', $wap_global_footer);


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
$coupon_num = $data['coupon_num'];
$yuepai_num = $data['yuepai_num'];
$waipai_num = $data['waipai_num'];
$code_num = $data['code_num'];
$nickname = get_user_nickname_by_user_id($yue_login_id);
$user_icon = get_user_icon($yue_login_id,165);
$city = $user_info['city_name'];

//�����ʾ�İ�
if(MALL_UA_IS_WEIXIN == 1)
{
	$logout_tips = "�˳���¼��΢�Ż��ղ�����������Ϣ�����ͣ�ȷ��Ҫ�˳���";
}
else
{
	$logout_tips = "ȷ��Ҫ�˳���";
}

if($_INPUT['print'] == 1)
{
	print_r($ret);
	die();
}

$tpl->assign('buyer_id',$yue_login_id);
$tpl->assign('available_balance',$available_balance);
$tpl->assign('nickname',$nickname);
$tpl->assign('icon',$user_icon);
$tpl->assign('city',$city);
$tpl->assign('yuepai_num',$yuepai_num);
$tpl->assign('waipai_num',$waipai_num);
$tpl->assign('code_num',$code_num);
$tpl->assign('logout_tips',$logout_tips);
$tpl->assign('coupon_num',$coupon_num);



// �ײ�����
// ��ҳ 
$tpl->assign('index_link', G_MALL_PROJECT_USER_ROOT);
// �ҵ�
$tpl->assign('my_link', G_MALL_PROJECT_USER_ROOT. '/home/');



$tpl->output();
?>