<?php
/**
 * 根据用户ID获取头像
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$poco_id = $_INPUT['poco_id'];

if(empty($poco_id)){
	$output_arr['code'] = 1;
	$output_arr['msg'] = 'poco id is empty';
	$output_arr['result'] = '';
	echo json_encode($output_arr);
	exit;
}

$obj = POCO::singleton ( 'pai_user_class' );


$poco_id_arr = explode(",",$poco_id);

foreach($poco_id_arr as $k=>$user_id){
	$ret[$k]['poco_id']   = $user_id;
	$nickname             = get_user_nickname_by_user_id($user_id);
	$ret[$k]['nickname']  = iconv("GB2312", "UTF-8//IGNORE",$nickname);
	$ret[$k]['user_icon'] = get_user_icon($user_id, 165);
	if($user_id==10002){
		$ret[$k]['url']       = "";
		$ret[$k]['wifi_url']  = "";
	}else{
		$check_role = $obj->check_role($user_id);

		if($check_role=='model'){
			$ret[$k]['url']       = "http://yp.yueus.com/mobile/app?from_app=1#model_card/{$user_id}";
			$ret[$k]['wifi_url']  = "http://yp-wifi.yueus.com/mobile/app?from_app=1#model_card/{$user_id}";
		}else{
			$ret[$k]['url']       = "http://yp.yueus.com/mobile/app?from_app=1#zone/{$user_id}/cameraman";
			$ret[$k]['wifi_url']  = "http://yp-wifi.yueus.com/mobile/app?from_app=1#zone/{$user_id}/cameraman";
		}
	}
}


$output_arr['code'] = 0;
$output_arr['msg'] = 'success';
$output_arr['result'] = $ret;
echo json_encode($output_arr);
?>