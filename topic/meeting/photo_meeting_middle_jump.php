<?php
/** 
 * 
 * �����ת��תҳ
 * 
 * author ����
 * 
 * 
 * 2015-4-2
 * 
 * 
 */
 
 
 
 
 
 /**
 * ֧���ɹ�����ҳ��תͬ��֪ͨ
 * ע�⣺һ�㲻��ͬ��֪ͨ�������֧��״̬
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$payment_no = trim($_INPUT['payment_no']); //֧����


/**
 * �жϿͻ���
 */
$__is_weixin = stripos($_SERVER['HTTP_USER_AGENT'], 'micromessenger') ? true : false;
$__is_android = stripos($_SERVER['HTTP_USER_AGENT'], 'android') ? true : false;
$__is_iphone = stripos($_SERVER['HTTP_USER_AGENT'], 'iphone') ? true : false;

//�����豸ʹ�ò�ͬģ�����ʾ
if($__is_weixin || $__is_android || $__is_iphone)
{
    $tpl = $my_app_pai->getView('photo_meeting_middle_jump_wap.tpl.htm');

}
else
{

    $tpl = $my_app_pai->getView('photo_meeting_middle_jump_pc.tpl.htm');
}

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

$Party_global_header = $my_app_pai->webControl('Party_global_header', array(), true);
$tpl->assign('Party_global_header', $Party_global_header);
$header_html = $my_app_pai->webControl('PartyHeader', array(), true);
$footer_html = $my_app_pai->webControl('PartyFooter', array(), true);
$tpl->assign("res",$res);
$tpl->assign("reason",$reason);
$tpl->assign('Party_global_header', $Party_global_header);
$tpl->assign('header_html', $header_html);
$tpl->assign('footer_html', $footer_html);
$tpl->output();
?>