<?php

include_once 'common.inc.php';
include_once 'top.php';
include_once ('/disk/data/htdocs232/poco/pai/config/model_card_config.php');


if($_POST)
{
	$model_oa_order_obj = POCO::singleton ( 'pai_model_oa_order_class' );
	
	$insert_data['cameraman_phone'] = $_INPUT['cameraman_phone'];
	$insert_data['cameraman_nickname'] = $_INPUT['cameraman_nickname'];
	$insert_data['style'] = $_INPUT['style'];
	$insert_data['clothes_require'] = $_INPUT['clothes_require'];
	$insert_data['clothes_provide'] = $_INPUT['clothes_provide'];
	$insert_data['location_id'] = $_INPUT['location_id'];
	$insert_data['date_address'] = $_INPUT['date_address'];
	$insert_data['date_time'] = $_INPUT['date_time'];
	$insert_data['fact_date_time'] = $_INPUT ['fact_date_time'];
	$insert_data['hour'] = $_INPUT['hour'];
	$insert_data['model_num'] = $_INPUT['model_num'];
	$insert_data['budget'] = $_INPUT['budget'];
	$insert_data['payable_amount'] = $_INPUT['budget']*$_INPUT['hour'];
	$insert_data['receivable_amount'] = $_INPUT['budget']*$_INPUT['hour'];
	$insert_data['bwh_require'] = $_INPUT['bwh_require'];
	$insert_data['height_require'] = $_INPUT['height_require'];
	$insert_data['weight_require'] = $_INPUT['weight_require'];
	$insert_data['looks_require'] = $_INPUT['looks_require'];
	$insert_data['date_remark'] = $_INPUT['date_remark'];
	$insert_data['source'] = $_INPUT['source'];
	$insert_data['response_time'] = $_INPUT['response_time'];
	$insert_data['audit_status'] = 'pass';
	$insert_data['order_status'] = 'confirm_order';
	
	if(empty($insert_data['cameraman_nickname']))
	{
		echo "<script>alert('�û��ǳƲ���Ϊ��');</script>";
		exit;
	}
	
	if(empty($insert_data['cameraman_phone']))
	{
		echo "<script>alert('��ϵ�绰����Ϊ��');</script>";
		exit;
	}
	
	if ( !preg_match ( '/^1\d{10}$/isU',$insert_data['cameraman_phone'] ) )
	{
		echo "<script>alert('�ֻ���ʽ����');</script>";
		exit;
	}
	
	if(empty($insert_data['style']))
	{
		echo "<script>alert('��������Ϊ��');</script>";
		exit;
	}
	
	if(empty($insert_data['location_id']))
	{
		echo "<script>alert('����ص㲻��Ϊ��');</script>";
		exit;
	}
	
	if(empty($insert_data['date_address']))
	{
		echo "<script>alert('������ϸ��ַ����Ϊ��');</script>";
		exit;
	}
	
	if(empty($insert_data['date_time']))
	{
		echo "<script>alert('����ʱ�䲻��Ϊ��');</script>";
		exit;
	}
	
	if(empty($insert_data['hour']))
	{
		echo "<script>alert('����ʱ������Ϊ��');</script>";
		exit;
	}
	
	if(empty($insert_data['budget']))
	{
		echo "<script>alert('����۸���Ϊ��');</script>";
		exit;
	}
	
	if(empty($insert_data['response_time']))
	{
		echo "<script>alert('��ʱ�䲻��Ϊ��');</script>";
		exit;
	}
	
	if(empty($insert_data['source']))
	{
		echo "<script>alert('������Դ����Ϊ��');</script>";
		exit;
	}
	
	$insert_data['style'] = implode(",",$insert_data['style']);
	
	$ret = $model_oa_order_obj->add_order($insert_data);
	
	
	if($ret)
	{
		echo "<script>alert('�ύ�ɹ�');parent.location.href='list.php?list_status=doing';</script>";
		exit;
	}
	else 
	{
		echo "<script>alert('�ύʧ��');</script>";
	}
}

$tpl = new SmartTemplate ( "order_add.tpl.htm" );

$i=0;
foreach($model_style as $k=>$val)
{
	$model_style_arr[$i]['style'] = $val;
	$i++;
}

$tpl->assign ( 'model_style_arr', $model_style_arr );



$tpl->assign ( 'YUE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER );
$tpl->output ();

?>