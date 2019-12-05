<?php

include_once 'common.inc.php';
include_once 'top.php';
include_once ('/disk/data/htdocs232/poco/pai/config/model_card_config.php');

$model_oa_order_obj = POCO::singleton ( 'pai_model_oa_order_class' );

$order_id = $_INPUT ['order_id'];
$list_status = $_INPUT ['list_status'] ? $_INPUT ['list_status'] : 'wait';

if ($_POST)
{
	$update_data ['cameraman_phone'] = $_INPUT ['cameraman_phone'];
	$update_data ['cameraman_nickname'] = $_INPUT ['cameraman_nickname'];
	$update_data ['style'] = $_INPUT ['style'];
	$update_data ['clothes_require'] = $_INPUT ['clothes_require'];
	$update_data ['clothes_provide'] = $_INPUT ['clothes_provide'];
	$update_data ['date_address'] = $_INPUT ['date_address'];
	$update_data ['date_time'] = $_INPUT ['date_time'];
	$update_data ['fact_date_time'] = $_INPUT ['fact_date_time'];
	$update_data ['hour'] = $_INPUT ['hour'];
	$update_data ['location_id'] = $_INPUT ['location_id'];
	$update_data ['model_num'] = $_INPUT ['model_num'];
	$update_data ['budget'] = $_INPUT ['budget'];
	$update_data['payable_amount'] = $_INPUT['payable_amount'];
	$update_data['receivable_amount'] = $_INPUT['receivable_amount'];
	$update_data ['bwh_require'] = $_INPUT ['bwh_require'];
	$update_data ['height_require'] = $_INPUT ['height_require'];
	$update_data ['weight_require'] = $_INPUT ['weight_require'];
	$update_data ['looks_require'] = $_INPUT ['looks_require'];
	$update_data ['date_remark'] = $_INPUT ['date_remark'];
	$update_data ['response_time'] = $_INPUT['response_time'];

	
	if (empty ( $update_data ['cameraman_nickname'] ))
	{
		echo "<script>alert('用户昵称不能为空');</script>";
		exit ();
	}
	
	if (empty ( $update_data ['cameraman_phone'] ))
	{
		echo "<script>alert('联系电话不能为空');</script>";
		exit ();
	}
	
	if (! preg_match ( '/^1\d{10}$/isU', $update_data ['cameraman_phone'] ))
	{
		echo "<script>alert('手机格式错误');</script>";
		exit ();
	}
	
	if ($update_data ['date_time'] =='0000-00-00 00:00:00')
	{
		echo "<script>alert('服务时间不能为空');</script>";
		exit ();
	}
	
	
	if (empty ( $update_data ['budget'] ))
	{
		echo "<script>alert('拍摄价格不能为空');</script>";
		exit ();
	}
	
	
	
	$ret = $model_oa_order_obj->update_order ( $update_data ,$order_id);
	

	echo "<script>alert('修改成功');parent.location.href='list.php?list_status={$list_status}';</script>";
	exit ();
	
}


$order_info = $model_oa_order_obj->get_order_info ( $order_id );

if($order_info['type_id_str'])
{
    $tpl = new SmartTemplate ( "mall_order_modify.tpl.htm" );
}
else
{
    $tpl = new SmartTemplate ( "order_modify.tpl.htm" );
}

$order_info ['city_name'] = get_poco_location_name_by_location_id ( $order_info ['location_id'] );

$order_info ['province_id'] =  substr($order_info['location_id'],0,6);

$order_info ['height_require'] = strtolower ( $order_info ['height_require'] );
$order_info ['weight_require'] = strtolower ( $order_info ['weight_require'] );

$order_info['date_remark'] = str_replace("<br rel=auto>","\n",htmlspecialchars_decode($order_info['date_remark']));

$style_arr = explode ( ",", $order_info ['style'] );

foreach ( $model_style as $style )
{
	if (in_array ( $style, $style_arr ))
	{
		$style_str .= '<label><input type="checkbox" name="style[]" value="' . $style . '" checked="checked" />' . $style . '</label>&nbsp;&nbsp;&nbsp;&nbsp;';
	}
	else
	{
		$style_str .= '<label><input type="checkbox" name="style[]" value="' . $style . '" />' . $style . '</label>&nbsp;&nbsp;&nbsp;&nbsp;';
	}
}

$order_info ['style_str'] = $style_str;

$tpl->assign ( $order_info );

$tpl->output ();

?>