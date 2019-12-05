<?php
/** 
 * ������ֵ���������첽�ص�
 * 
 * @author Henry
 * @copyright 2014-06-23
 */

include_once('../common.inc.php');

$payment_obj = POCO::singleton('pai_payment_class');

//��ȡ֧����Ϣ
$payment_no = trim($_INPUT['payment_no']);
$payment_info = $payment_obj->get_payment_info($payment_no);
if( empty($payment_info) )
{
	echo 'payment_no error';
	exit();
}

//���֧��״̬������ֵ״̬��ִ�г�ֵ
$result = $payment_obj->notify_recharge($payment_info);
if( $result['error']!==0 && $result['error']!==1 )
{
	print_r($result);
	exit();
}

$pay_ret = $payment_obj->pay_enroll($payment_no);
if( $pay_ret['error']!==0 )
{
	print_r($pay_ret);
	exit();
}

echo 'success';
