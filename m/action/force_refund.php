<?php
ignore_user_abort(true);
/**
 * ǿ���˿�
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

/**
 * ҳ����ղ���
 */
$date_id = intval($_INPUT['date_id']) ;


if(!$yue_login_id)
{
	die('no login');
}

$ret = update_force_refund_status($date_id);


if($ret==1){
	$msg = "�ɹ�";
}elseif($ret==-1){
	$msg = "ģ��ͬ��״̬�쳣";
}elseif($ret==-2){
	$msg = "�˿�ʧ�ܣ�����ϵ����Ա";
}elseif($ret==-3){
	$msg = "ϵͳ״̬�쳣";
}elseif($ret==-4){
	$msg = "��ѽ���";
}elseif($ret==-5){
	$msg = "�ȯ�ѱ�ɨ�裬�����˿���";
}elseif($ret==-6){
	$msg = "ģ�����˿���";
}

$output_arr['msg'] = $msg;

$output_arr['data'] = $ret;


mobile_output($output_arr,false);

?>