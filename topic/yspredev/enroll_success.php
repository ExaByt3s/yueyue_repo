<?php
/**
 *
 * Լ��ר���Ƿ�ɹ���תҳ����
 *
 * author ����
 *
 *
 * 2015-6-3
 *
 *
 */


/**
 * ֧���ɹ�����ҳ��תͬ��֪ͨ
 * ע�⣺һ�㲻��ͬ��֪ͨ�������֧��״̬
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$type = trim($_INPUT['type']);
$enroll_res = trim($_INPUT['enroll_res']);

if($type=="normal")
{
    if(empty($enroll_res))
    {
        $res = "error";

    }
    else
    {
        $res = $enroll_res;
    }

    if($res == "error")
    {
        $reason = "�������ִ���������";
    }

    $tpl = $my_app_pai->getView('welfare_enroll_success.tpl.htm');
}
else
{
    $payment_no = trim($_INPUT['payment_no']); //֧����
    $tpl = $my_app_pai->getView('fatherday_enroll_success.tpl.htm');
    //��ȡ֧����Ϣ
    $payment_obj = POCO::singleton('pai_payment_class');
    $payment_info = $payment_obj->get_payment_info($payment_no);
    $reason = "";
    if( empty($payment_info) )
    {

        $res = "error";
        $reason = "ԭ��֧��������";
    }
    else
    {

        $payment_status = intval($payment_info['status']);
        if( !in_array($payment_status, array(1, 8)) )
        {
            $res = "error";
            //echo "֧��δ�ɹ�";
            ///exit();
        }
        else
        {
            $channel_rid = intval($payment_info['channel_rid']); //����ID��������
            $third_total_fee = $payment_info['third_total_fee']*1; //ʵ�ս��

            //echo "֧���ѳɹ� {$channel_rid} {$third_total_fee}";
            //exit();
            $res = "success";

        }
    }
    $tpl->assign("res",$res);

}




$tpl->assign("reason",$reason);
$tpl->output();
?>