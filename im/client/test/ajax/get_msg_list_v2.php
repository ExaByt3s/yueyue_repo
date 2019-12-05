<?php
include_once 'config.php';
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
include_once '../api_rest.php';

$id = $_INPUT['seller_id'];
$page = intval($_INPUT['page']);
$receiver_id = $_INPUT['buyer_id'];


if(empty($page))
{
	$page = 1;
}

// 分页使用的page_count
$page_count = 30;

$limit_start = ($page - 1)*($page_count);

$limit = "{$limit_start},{$page_count}";

$user_obj = POCO::singleton('pai_user_class');

$ret_2 = get_api_result('merchant/msg_list.php',array(
	'user_id' => $id,
	'receiver_id' => $receiver_id,
	'limit' => $limit
	));

//解码
foreach($ret_2['data']['list'] as $k=>$value){
	$ret_2['data']['list'][$k] = iconv('GBK','UTF-8',$value);
	$ret_2['data']['list'][$k] = json_decode($ret_2['data']['list'][$k],true);
}

//操作
foreach($ret_2['data']['list'] as $i=>$value){
	if($value['send_user_id'] == $id){
		$custom_icon = get_seller_user_icon($value['to_user_id']);
		$custom_name = iconv('GBK','UTF-8',get_seller_nickname_by_user_id($value['to_user_id'])); //返回的东西也要转码，后面再转一次
		$custom_id = $value['to_user_id'];
		$manage_id = $value['send_user_id'];
		$msg_diret = 'send';
	}
	else{
		$custom_icon = get_seller_user_icon($value['send_user_id']);
		$custom_name = iconv('GBK','UTF-8',get_seller_nickname_by_user_id($value['send_user_id']));
		$custom_id = $value['send_user_id'];
		$manage_id = $value['to_user_id'];
		$msg_diret = 'receive';
	}

	//$good_list = $goods_obj->user_goods_list($value['seller_user_id'],array("show"=>1), false,  'goods_id DESC', '0,200');
	$ret_2['data']['list'][$i]['custom_icon'] = $custom_icon;
	$ret_2['data']['list'][$i]['custom_name'] = $custom_name;
	$ret_2['data']['list'][$i]['custom_id'] = $custom_id;
	$ret_2['data']['list'][$i]['manage_id'] = $manage_id;
	$ret_2['data']['list'][$i]['manage_name'] = iconv('GBK','UTF-8',get_seller_nickname_by_user_id($manage_id));
	$ret_2['data']['list'][$i]['msg_diret'] = $msg_diret;
	$ret_2['data']['list'][$i]['last_connect_day'] = date('Y-m-d',$ret_2['data']['list'][$i]['send_time']);
	$ret_2['data']['list'][$i]['last_connect_time'] = date('H:i',$ret_2['data']['list'][$i]['send_time']);
	//$ret['data']['list'][$k]['good_list'] = $good_list;
}

$ret_2['data']['limit'] = $limit;

$ret_2['data']['page'] = $page;




//编码
$ret_2['data']['list'] = poco_iconv_arr($ret_2['data']['list'],'UTF-8','GBK');

$ret['buyer_msg'] = $ret_2['data'];
mobile_output($ret,false);
?>