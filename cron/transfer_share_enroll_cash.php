<?php 
/**
 * 外拍分享，报名返现
 * @author Henry
 */

echo '停止：外拍分享，报名返现' . date("Y-m-d H:i:s");
exit();

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$op = trim($_INPUT['op']);
if( $op!='run')
{
	die('op error!');
}

$user_obj = POCO::singleton('pai_user_class');

$pai_payment_obj = POCO::singleton('pai_payment_class');
$trade_obj = POCO::singleton('ecpay_pai_trade_class');

$server_id = 101;

$sql = "SELECT * FROM `pai_db`.`pai_event_enroll_coupon_tbl` WHERE is_send=0 AND enroll_id>0 AND share_event_id>0";
$list = db_simple_getdata($sql, false, $server_id);
foreach($list as $info)
{
	$id = intval($info['id']);
	$cellphone = trim($info['cellphone']);
	
	$user_info = $user_obj->get_user_by_phone($cellphone);
	if( empty($user_info) )
	{
		continue;
	}
	
	//事务开始
	POCO_TRAN::begin($server_id);
	
	$sql = "UPDATE `pai_db`.`pai_event_enroll_coupon_tbl` SET is_send=1 WHERE id={$id} AND is_send=0";
	db_simple_getdata($sql, true, $server_id);
	$affected_rows = (int)db_simple_get_affected_rows();
	if( $affected_rows<1 )
	{
		//事务回滚
		POCO_TRAN::rollback($server_id);
		
		break;
	}
	
	$more_info = array('is_balance'=>1, 'subject'=>'外拍分享，报名返现');
	$ret = $trade_obj->transfer(20001, 2, $user_info['user_id'], $more_info);
	
	//日志
	$ret['id'] = $id;
	$ret['cellphone'] = $cellphone;
	var_dump($ret);
	
	if( $ret['error']!==0 )
	{
		//事务回滚
		POCO_TRAN::rollback($server_id);
		
		break;
	}
	
	//事务提交
	POCO_TRAN::commmit($server_id);
}

echo '外拍分享，报名返现' . date("Y-m-d H:i:s");
