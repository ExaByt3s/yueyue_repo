<?php
/**
 * ���������ǩ
 */
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');


$coupon_obj = POCO::singleton('pai_coupon_class');

$sn = $_INPUT['sn'];


$ret = $coupon_obj -> get_coupon_detail_by_sn($sn,$yue_login_id);

$ret['scope_module_type_name'] = '��'.$ret['scope_module_type_name'].'��';
$ret['scope_order_total_amount'] = $ret['scope_order_total_amount']*1;
$ret['face_value'] = $ret['face_value']*1;
$ret['coin'] = '��';

if( $ret['scope_module_type']=='yuepai' )
{
	$ret['scope_module_txt'] = '����Լ�ģ� ʹ���Żݣ�';
	$ret['scope_module_btn'] = 'hot'; //��ҳ
}
elseif( $ret['scope_module_type']=='waipai' )
{
	$ret['scope_module_txt'] = '�������ģ� ʹ���Żݣ�';
	$ret['scope_module_btn'] = 'act'; //����
}
else
{
	$ret['scope_module_txt'] = '����ʹ���Ż�ȯ��';
	$ret['scope_module_btn'] = 'hot';
	
	//��ʱ������Ȼ����ȯ����תȥ������ҳ
	$ret['scope_module_type'] = 'yuepai';
}

$output_arr = $ret;

mobile_output($output_arr,false);

?>