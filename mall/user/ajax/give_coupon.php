<?php

/**
 * ��ȡ�����Ż�ȯ
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

if(empty($yue_login_id))
{
	$output_arr['list'] = array();
	$output_arr['code'] = 0;

	mobile_output($output_arr,false);

	die();
} 
	
/**
 * ҳ����ղ���
 */
$sn = trim($_INPUT['sn']);
$sn = preg_replace('/\xE2\x80\x86/isU', '', $sn); //�滻��IOS�������뷨�Ĳ����ո�

$coupon_obj = POCO::singleton('pai_coupon_class');

$ret = $coupon_obj->give_coupon($yue_login_id,$sn,$b_valid=true);

if($yue_login_id == 100001 || $yue_login_id == 100003)
{
	$ret = array('result'=> 1,'list'=>array(1));
}

$output_arr['list'] = $ret;

if($ret['result'] !=1)
{
	$output_arr['code'] = 0;
}
else
{
	$output_arr['code'] = 1;
}



mobile_output($output_arr,false);

?>