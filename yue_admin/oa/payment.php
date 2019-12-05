<?php

include_once 'common.inc.php';
include_once 'top.php';

$model_oa_order_obj = POCO::singleton ( 'pai_model_oa_order_class' );

if($_POST)
{
	
	$update_data['cameraman_realname'] = $_INPUT['cameraman_realname'];
	$update_data['pay_type'] = $_INPUT['pay_type'];
	$update_data['pay_account'] = $_INPUT['pay_account'];
	$update_data['running_number'] = $_INPUT['running_number'];
	$update_data['payment'] = $_INPUT['payment'];
	$update_data['pay_time'] = strtotime($_INPUT['pay_time']);
	$update_data['payment_remark'] = $_INPUT['payment_remark'];


	
	if(empty($update_data['cameraman_realname']))
	{
		echo "<script>alert('��ʵ��������Ϊ��');</script>";
		exit;
	}
	
	if(empty($update_data['pay_account']))
	{
		echo "<script>alert('֧���˺Ų���Ϊ��');</script>";
		exit;
	}

    if(empty($update_data['running_number']))
    {
        echo "<script>alert('��ˮ�Ų���Ϊ��');</script>";
        exit;
    }

    if(empty($update_data['pay_time']))
    {
        echo "<script>alert('֧��ʱ�䲻��Ϊ��');</script>";
        exit;
    }
	

	$ret = $model_oa_order_obj->order_pay_confirm($order_id);
	
	if($ret==1)
	{
		$model_oa_order_obj->update_order($update_data,$order_id);
	
		echo "<script>alert('ȷ�ϳɹ�');parent.location.href='list.php?list_status=doing';</script>";
		exit;
	}
	elseif($ret==-1)
	{
		echo "<script>alert('��ǰ����״̬���Ǵ��տ��ˢ�º�����');</script>";
	}
	else 
	{
		echo "<script>alert('ȷ��ʧ��');</script>";
	}
}

$tpl = new SmartTemplate ( "payment.tpl.htm" );


$order_info = $model_oa_order_obj->get_order_info($order_id);

$order_info['total_price'] = $order_info['hour']*$order_info['budget'];

$tpl->assign ( $order_info );



$tpl->assign ( 'YUE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER );
$tpl->output ();

?>