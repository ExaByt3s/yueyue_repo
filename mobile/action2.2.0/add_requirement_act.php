<?php 

/**
 * 添加摄影师需求
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

if(empty($yue_login_id))
{
	die('no login');
}

$user_obj = POCO::singleton ( 'pai_user_class' );
$model_oa_order_obj = POCO::singleton ( 'pai_model_oa_order_class' );
$config_obj = POCO::singleton ( 'pai_config_class' );
$pai_trigger_obj = POCO::singleton('pai_trigger_class');

$check_role = $user_obj->check_role ( $yue_login_id );

if($check_role=='model')
{
	$output_arr['code'] = 0;
	$output_arr['msg'] = '模特不能提需求' ;
	
	mobile_output($output_arr,false); 
	exit;
}


$data_arr = poco_iconv_arr($_REQUEST['data_str'],'UTF-8','GBK');

$user_info = $user_obj->get_user_info($yue_login_id);

foreach($data_arr as $k=>$val)
{
	$answer .= $val['title']."\n".$val['value']."\n\n";
}

$big_style = $config_obj->question_big_style($data_arr['p1']['value']);

$date_arr = explode(" ",$data_arr['p0']['value']);

if($date_arr[1]=='morning')
{
	$hour_str = '10:00';
}
elseif($date_arr[1]=='afternoon')
{
	$hour_str = '15:00';
}
else
{
	$hour_str = '18:00';
}
$date_time = $date_arr[0]." ".$hour_str;

$location_id = $data_arr['location_id'] ? $data_arr['location_id'] :101029001;
 
$insert_data['location_id'] = $location_id;
$insert_data['cameraman_phone'] = $user_info['cellphone'];
$insert_data['cameraman_nickname'] = $user_info['nickname'];
$insert_data['style'] = $big_style;
$insert_data['clothes_require'] = $_INPUT['clothes_require'];
$insert_data['clothes_provide'] = $_INPUT['clothes_provide'];
$insert_data['date_address'] = $data_arr['p4']['value'];
$insert_data['date_time'] = $date_time;
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
$insert_data['date_remark'] = $answer;
$insert_data['require_remark'] = $data_arr['p7']['value'];
$insert_data['source'] = 5;
$insert_data['response_time'] = $_INPUT['response_time'];
$insert_data['audit_status'] = 'wait';
$insert_data['order_status'] = 'wait';
$insert_data['question_budget'] = $data_arr['p2']['value'];
$insert_data['question_style'] = $data_arr['p1']['value'];



$ret = $model_oa_order_obj->add_order($insert_data);

if($ret)
{
	$trigger_params = array('order_id'=>$ret);
	$pai_trigger_obj->requirement_submit_after($trigger_params);
}

$output_arr['code'] = $ret ? 1 :0;
$output_arr['msg'] = $ret ? '成功' : '失败';



mobile_output($output_arr,false); 

?>