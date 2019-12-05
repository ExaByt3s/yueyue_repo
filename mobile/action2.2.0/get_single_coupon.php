<?php
/**
 * 获得搜索标签
 */
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');


$coupon_obj = POCO::singleton('pai_coupon_class');

$sn = $_INPUT['sn'];


$ret = $coupon_obj -> get_coupon_detail_by_sn($sn,$yue_login_id);

$ret['scope_module_type_name'] = '「'.$ret['scope_module_type_name'].'」';
$ret['scope_order_total_amount'] = $ret['scope_order_total_amount']*1;
$ret['face_value'] = $ret['face_value']*1;
$ret['coin'] = '￥';

if( $ret['scope_module_type']=='yuepai' )
{
	$ret['scope_module_txt'] = '马上约拍， 使用优惠！';
	$ret['scope_module_btn'] = 'hot'; //首页
}
elseif( $ret['scope_module_type']=='waipai' )
{
	$ret['scope_module_txt'] = '参与外拍， 使用优惠！';
	$ret['scope_module_btn'] = 'act'; //外拍
}
else
{
	$ret['scope_module_txt'] = '马上使用优惠券！';
	$ret['scope_module_btn'] = 'hot';
	
	//临时处理，不然其它券会跳转去外拍首页
	$ret['scope_module_type'] = 'yuepai';
}

$output_arr = $ret;

mobile_output($output_arr,false);

?>