<?php
/** 
 * 
 * �Ż݄���⴦��
 * 
 * author ����
 * 
 * 
 * 2015-3-4
 */

include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');

$obj = POCO::singleton ( 'pai_event_coupon_class' );
$weixin_pub_obj = POCO::singleton('pai_weixin_pub_class'); 
$user_obj = POCO::singleton('pai_user_class'); 
$pai_sms_obj = POCO::singleton ( 'pai_sms_class' );

$cellphone = $_INPUT['cellphone'];

$data['cellphone'] = $cellphone;
$data['event_id'] = $_INPUT['event_id'];
$data['url'] = urldecode($_INPUT['url']);


$check_send = $obj->check_share_coupon($cellphone);

if($check_send)
{
	exit;
}

$user_id = $user_obj->get_user_id_by_phone($cellphone);

$role = $user_obj->check_role($user_id);

if($user_id && $role=='cameraman')
{
	$price = "200Ԫ�Ż����";
	//΢��
	$template_code = 'G_PAI_WEIXIN_WAIPAI_SHARE_SUCCESS';
	$weixin_data = array('amount'=>$price, 'coupon_sn'=>'---', 'end_date'=>'4��30��');
	$weixin_pub_obj->message_template_send_by_user_id($user_id, $template_code, $weixin_data, $to_url);
	
	//sms
	$sms_data = array ('amount' => $price); 
	$pai_sms_obj->send_sms ( $cellphone, 'G_PAI_WAIPAI_SHARE_SUCCESS', $sms_data, $user_id );
	
	//С����
	$content = "��ϲ�����ɹ���{$price}�Ѿ������ԼԼ�˺� ���汾����֮���¼app����ʹ�á�";
	send_message_for_10002 ( $user_id, $content );
	
}

if($cellphone)
{
	$ret=$obj->add_share_coupon($data);
}
?>