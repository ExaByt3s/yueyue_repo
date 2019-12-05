<?php
    include_once 'common.inc.php';
    $task_goods_obj = POCO::singleton('pai_mall_goods_class');

    $req_goods = $_POST;

    $buyer_user_id = intval($req_goods['user_id']);

    //获取产品信息
    $goods_id = intval($req_goods['goods_id']);
	$goods_obj = POCO::singleton('pai_mall_goods_class');
	$goods_info = $goods_obj->get_goods_info($goods_id);
	//获取产品信息end

	$goods_data = $goods_info['goods_data'];

	$order_obj = POCO::singleton('pai_mall_order_class');

	// 订单主信息
	$more_info = array(
		'referer' => '测试案例',//订单来源
		'description' => '测试',
		);
	
	// 订单详细信息

	$detail_list[] = array(
	 	'type_id'=> $goods_data['type_id'],//品类id
	 	'goods_id'=> $goods_id,//商品id
	 	'goods_name'=> $goods_data['titles'],//商品名称
	 	'prices_type_id'=> $goods_info['goods_prices_list'][0]['type_id'][0],//商品价格策略属性id
	 	'prices_spec'=> $req_goods['prices_spec'],//商品规格
	 	'goods_version'=> $goods_data['version'],//商品版本
	 	'service_time'=> strtotime($req_goods['service_time']),//服务时间
	 	'service_location_id'=> $goods_data['location_id'],
	  	'service_address' => $req_goods['service_address'],
	  	'service_people' => $req_goods['service_people'],
	  	//'prices' => 0,
	  	'quantity'=> $req_goods['num'],
	  	);
	$order_sn = $order_obj->submit_order($buyer_user_id, $detail_list, $more_info);
	var_dump($order_sn);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gbk" />
<title>订单列表</title>
<style>
td{ font-size:12px;}
</style>
</head>
<body>
	
</body>
</html>
