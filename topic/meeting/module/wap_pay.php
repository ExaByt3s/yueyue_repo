<?php
/**
 * WAP��ȥ֧��
 * ��Ϊdemo�ο�ʹ��
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$channel_rid = 1; //ģ�����ID������ID��������
$subject = '4�·��'; //��Ʒ���ơ������
$amount = 0.01; //֧�����
$channel_return = 'http://yp.yueus.com/topic/meeting/wap_pay_return.php'; //֧���ɹ���ҳ����ת����
$channel_notify = 'http://yp.yueus.com/topic/meeting/pay_notify.php'; //�������첽֪֧ͨ��״̬

$channel_module = 'meeting'; //ģ�����ͣ����
$third_code = 'alipay_wap'; //֧����ʽ��֧����WAP
$payment_info = array(
    'third_code' => $third_code,
    'subject' => 'ԼԼ-' . $subject,
    'amount' => $amount,
    'channel_return' => $channel_return,
    'channel_notify' => $channel_notify,
);
$payment_obj = POCO::singleton('pai_payment_class');
$payment_ret = $payment_obj->submit_payment($channel_module, $channel_rid, $payment_info);
if( $payment_ret['error']===0 )
{
    $request_data = trim($payment_ret['request_data']);
    echo '<script>parent.location.href="'.$request_data.'"</script>';
}
else
{
    print_r($payment_ret);
}
