<?php

/**
 * tt֧��
 * hdw 2015.4.15
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$quotes_id = intval($_INPUT['quotes_id']);
$third_code = trim($_INPUT['third_code']);
$coupon_sn = trim($_INPUT['coupon_sn']);
$available_balance = trim($_INPUT['available_balance']);
$is_available_balance = trim($_INPUT['is_available_balance']);
$redirect_url = trim($_INPUT['redirect_url']);
$notify_url = G_PAI_APP_DOMAIN . '/mobile/' . basename(dirname(__FILE__)) . '/pay_quotes_notify.php';

//��ȡ������Ϣ
$task_quotes_obj = POCO::singleton('pai_task_quotes_class');
$quotes_info = $task_quotes_obj->get_quotes_info($quotes_id);
$request_id = intval($quotes_info['request_id']);

//��ȡ������Ϣ
$task_request_obj = POCO::singleton('pai_task_request_class');
$request_info = $task_request_obj->get_request_info($request_id);
$buyer_user_id = intval($request_info['user_id']);

if( $yue_login_id>0 && $yue_login_id==$buyer_user_id )
{
	/**
	 * �ύ֧��
	 * @param int $quotes_id
	 * @param double $available_balance ҳ�浱ǰ���
	 * @param int $is_available_balance �Ƿ�ʹ����0�� 1��
	 * @param string $third_code ֧����ʽ alipay_purse tenpay_wxapp�����û�ʹ�����ȫ��֧��ʱ��Ϊ��
	 * @param string $redirect_url ֧���ɹ�����ת��url ���û�ʹ�����ȫ��֧��ʱ��Ϊ��
	 * @param string $notify_url
	 * @param string $coupon_sn
	 * @return array result 1��ȡ������֧����2���֧���ɹ�
	 */
	$ret = $task_quotes_obj->submit_pay_quotes_v2($quotes_id, $available_balance, $is_available_balance, $third_code, $redirect_url, $notify_url, $coupon_sn);
}
else
{
	$ret = array('result'=>-1, 'message'=>'��������', 'payment_no'=>'', 'request_data'=>'');
}

$ret['third_code'] = $third_code;

//��־
pai_log_class::add_log($ret, 'pay end', 'pay_tt');

mobile_output($ret,false);

?>