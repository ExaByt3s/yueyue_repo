<?php
/**
 * ֧���ɹ�����ҳ��תͬ��֪ͨ
 * ע�⣺һ�㲻��ͬ��֪ͨ�������֧��״̬
 * ��Ϊdemo�ο�ʹ��
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$payment_no = trim($_INPUT['payment_no']); //֧����

//��ȡ֧����Ϣ
$payment_obj = POCO::singleton('pai_payment_class');
$payment_info = $payment_obj->get_payment_info($payment_no);
if( empty($payment_info) )
{
    echo "֧��������";
    exit();
}

$payment_status = intval($payment_info['status']);
if( !in_array($payment_status, array(1, 8)) )
{
    echo "֧��δ�ɹ�";
    exit();
}

$channel_rid = intval($payment_info['channel_rid']); //����ID��������
$third_total_fee = $payment_info['third_total_fee']*1; //ʵ�ս��

echo "֧���ѳɹ� {$channel_rid} {$third_total_fee}";
exit();
