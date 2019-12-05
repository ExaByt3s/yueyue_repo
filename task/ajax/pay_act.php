<?php
/** 
 * 
 * ����
 * 
 * 2015-4-11
 * 
 */
 
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$quotes_id = (int)$_INPUT['quotes_id'];
$num = (int)$_INPUT['num'];
if( $quotes_id<1 || $num<1 )
{
	$output_arr['code'] = -1;
	$output_arr['message'] = '��������';
	mobile_output($output_arr,false);
	exit();
}
if( $num!=20 )
{
	$output_arr['code'] = -2;
	$output_arr['message'] = '��������';
	mobile_output($output_arr,false);
	exit();
}

$subject = '���⿨����';
$amount = 70;
$coins = 20;

$task_coin_obj = POCO::singleton('pai_task_coin_class');

/**
 * �ύ����
 * @param int $user_id
 * @param double $amount
 * @param double $coins
 * @param string $subject
 * @param int $quotes_id
 * @param array $more_info array('remark'=>'')
 * @return array array('result'=>0, 'message'=>'', 'buy_id'=>0)
 */
$submit_ret = $task_coin_obj->submit_buy($yue_login_id, $amount, $coins, $subject, $quotes_id);
if( $submit_ret['result']!=1 )
{
	$output_arr['code'] = -3;
	$output_arr['message'] = '�ύ����';
	mobile_output($output_arr,false);
	exit();
}

$buy_id = intval($submit_ret['buy_id']);
$available_balance = 0;
$is_available_balance = 0;
$third_code = 'alipay_wap';
$redirect_url = 'http://www.yueus.com/task/m/success.php';
$notify_url = 'http://www.yueus.com/task/pay_buy_notify.php';

/**
 * �ύ֧��
 * @param int $buy_id
 * @param double $available_balance ҳ�浱ǰ���
 * @param int $is_available_balance �Ƿ�ʹ����0�� 1��
 * @param string $third_code ֧����ʽ alipay�����û�ʹ�����ȫ��֧��ʱ��Ϊ��
 * @param string $redirect_url ֧���ɹ�����ת��url ���û�ʹ�����ȫ��֧��ʱ��Ϊ��
 * @param string $notify_url
 * @return array
 */
$pay_ret = $task_coin_obj->submit_pay_buy($buy_id, $available_balance, $is_available_balance, $third_code, $redirect_url, $notify_url);
if( $pay_ret['result']!=1 )
{
	$output_arr['code'] = -4;
	$output_arr['message'] = '֧������';
	mobile_output($output_arr,false);
	exit();
}

$output_arr['code'] = 1;
$output_arr['message'] = '�ɹ�';
$output_arr['request_data'] = $pay_ret['request_data'];

mobile_output($output_arr,false);
?>