<?php
ignore_user_abort(true);
/**
 * ����ͬ��״̬
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

/**
 * ҳ����ղ���
 */
$date_id = intval($_INPUT['date_id']) ;
$agree_status = trim($_INPUT['agree_status']);


if(!$yue_login_id)
{
	die('no login');
}
//$agree_status��agree��disagree
$ret = update_agree_status($date_id, $agree_status);

if($ret==1){
	$msg = "�ɹ�";
}elseif($ret==-1){
	$msg = "���ѻظ���Ӱʦȡ������";
}elseif($ret==-2){
	$msg = "ϵͳ״̬�쳣";
}elseif($ret==-3){
	$msg = "��ѽ���";
}

$output_arr['msg'] = $msg;

$output_arr['data'] = $ret;


mobile_output($output_arr,false);

?>