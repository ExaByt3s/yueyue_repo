<?php
    include_once 'common.inc.php';
    $task_goods_obj = POCO::singleton('pai_mall_goods_class');

    $req_goods = $_POST;

    $buyer_user_id = intval($req_goods['user_id']);

    //��ȡ��Ʒ��Ϣ
    $goods_id = intval($req_goods['goods_id']);
	$goods_obj = POCO::singleton('pai_mall_goods_class');
	$goods_info = $goods_obj->get_goods_info($goods_id);
	//��ȡ��Ʒ��Ϣend

	$goods_data = $goods_info['goods_data'];

	$order_obj = POCO::singleton('pai_mall_order_class');

	// ��������Ϣ
	$more_info = array(
		'referer' => '���԰���',//������Դ
		'description' => '����',
		);
	
	// ������ϸ��Ϣ

	$detail_list[] = array(
	 	'type_id'=> $goods_data['type_id'],//Ʒ��id
	 	'goods_id'=> $goods_id,//��Ʒid
	 	'goods_name'=> $goods_data['titles'],//��Ʒ����
	 	'prices_type_id'=> $goods_info['goods_prices_list'][0]['type_id'][0],//��Ʒ�۸��������id
	 	'prices_spec'=> $req_goods['prices_spec'],//��Ʒ���
	 	'goods_version'=> $goods_data['version'],//��Ʒ�汾
	 	'service_time'=> strtotime($req_goods['service_time']),//����ʱ��
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
<title>�����б�</title>
<style>
td{ font-size:12px;}
</style>
</head>
<body>
	
</body>
</html>
