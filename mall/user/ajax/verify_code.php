<?php
/**
 * �����֤
 */

include_once('../common.inc.php');

$output_arr['data'] = array();

if(empty($yue_login_id))
{
	$output_arr['code'] = 0;
	$output_arr['msg'] = "�Ƿ���¼";
	die();
}

$code = str_replace(' ','',$_INPUT['code']);

$code = (int)$code;

$code_obj = POCO::singleton('pai_activity_code_class');


$ret = $code_obj->verify_code($yue_login_id,$code);

$output_arr['code'] = $ret>0 ? 1 : 0;

if($ret==1)
{
	$code_info = $code_obj->get_available_code_info($code);
	$event_id = $code_info["event_id"];

	$return_url = "./sign.php?event_id=".$event_id;

	$output_arr['data']['return_url'] = $return_url;
	$output_arr['msg'] = "�����֤�ɹ�";
}elseif($ret==-1){
	$output_arr['msg'] = "�����Ч���ѱ�ʹ��";
}elseif($ret==-2){
	$output_arr['msg'] = "�㲻�ǻ�����ˣ�����ʹ�øû��";
}elseif($ret==-3){
	$output_arr['msg'] = "��ѽ���";
}elseif($ret==-4){
	$output_arr['msg'] = "������������࣬���Ժ�����";
}


mall_mobile_output($output_arr,false); 

?>