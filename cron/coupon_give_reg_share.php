<?php 
/**
 * �����Ż�ȯ
 * @author Henry
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

//echo '��ͣ�����Ż�ȯ' . date("Y-m-d H:i:s");
//exit();

set_time_limit(600);

$op = trim($_INPUT['op']);
if( $op!='run')
{
	die('op error!');
}

$coupon_give_obj = POCO::singleton('pai_coupon_give_class');
$queue_list = $coupon_give_obj->get_queue_list(0, false, '', 'id DESC', '0,1000');
foreach($queue_list as $queue_info)
{
	$coupon_give_obj->give_coupon_by_queue_info($queue_info);
}

/*
//2015��7��10�� �� 2015��8��9�գ�����50ԪԼ��ר����Ʒȯ
//���������죻��ɫ����Ӱʦ��ע��ʱ�䣺2015��7��10�� �� 2015��8��9�գ�
$sql = "SELECT user_id FROM `pai_db`.`pai_user_tbl` WHERE location_id='101004001' AND role='cameraman' AND add_time>=1436457600 AND add_time<=1439135999 AND user_id NOT IN (SELECT user_id FROM `pai_coupon_db`.`coupon_give_queue_tbl` WHERE give_code='Y2015M07D10_CHONGQING_NEW_USER')";
$user_list = db_simple_getdata($sql, false, 101);
foreach($user_list as $user_info)
{
	$cellphone = '';
	$user_id = intval($user_info['user_id']);
	$ref_id = 0;
	$coupon_give_obj->submit_queue('Y2015M07D10_CHONGQING_NEW_USER', $cellphone, $user_id, $ref_id);
}
*/

echo '�����Ż�ȯ' . date("Y-m-d H:i:s");
