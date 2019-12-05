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

//获取商家列表
$ret_1 = get_api_result('merchant/msg_list.php',array(
	'user_id' => $id
	));

if(empty($receiver_id))
{

}
else
{
	$ret_2 = get_api_result('merchant/msg_list.php',array(
	'user_id' => $id,
	'receiver_id' => $receiver_id,
	'limit' => $limit
	));
	
	$ret_2['data']['limit'] = $limit;

	$ret_2['data']['page'] = $page;
	$buyer_into_seller = $ret_2['data'];
}

$ret['buyer_list'] = $ret_1['data'];
$ret['buyer_msg'] = $ret_2['data'];




mobile_output($ret,false);
?>