<?php

//****************** pc版头部通用 start ******************
$pc_wap = 'pc/';
$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'chat_room/chat_room.tpl.htm');



// ================== pc版头部通用 end ==================

/*
     * 获取一个管理员下面有效的商家列表
     *
     * @param string $admin_user_id    管理员ID
     * @param string $b_select_count   是否返回总数：TRUE返回总数 FALSE返回列表
     * @param string $limit     分页
     * @return array|int
     */

$ret = $obj->get_valid_seller_list($yue_login_id,$b_select_count = false,$limit = '0,1000');

$goods_obj = POCO::singleton ( 'pai_mall_operate_agent_class' );

foreach ($ret as $k=>$value) {
  $icon = get_seller_user_icon($value['seller_user_id']);
  $name = get_seller_nickname_by_user_id($value['seller_user_id']);
  $good_list = $goods_obj->user_goods_list($value['seller_user_id'],array("show"=>1), false,  'goods_id DESC', '0,200');
  $ret[$k]['icons'] = $icon;
  $ret[$k]['nickname'] = $name;
  $ret[$k]['good_list'] = $good_list;

  $id = $value['seller_user_id'];
  $ret_2 = get_api_result('merchant/msg_list.php',array(
	'user_id' => $id
	));
	
	//解码
foreach($ret_2['data']['list'] as $l=>$value){
	$ret_2['data']['list'][$l] = iconv('GBK','UTF-8',$value);
	$ret_2['data']['list'][$l] = json_decode($ret_2['data']['list'][$l],true);
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
	$ret_2['data']['list'][$i]['msg_diret'] = $msg_diret;
	$ret_2['data']['list'][$i]['last_connect_day'] = date('Y-m-d',$ret_2['data']['list'][$i]['send_time']);
	$ret_2['data']['list'][$i]['last_connect_time'] = date('H:i',$ret_2['data']['list'][$i]['send_time']);
	//$ret['data']['list'][$k]['good_list'] = $good_list;
}
	  
	//编码
	$ret[$k]['buyer_list']= poco_iconv_arr($ret_2['data']['list'],'UTF-8','GBK');
};

$tpl->assign('sellers',mall_output_format_data($ret));
$tpl->assign('yue_login_id',$yue_login_id);

?>
