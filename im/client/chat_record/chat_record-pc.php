<?php

//****************** pc版头部通用 start ******************
$pc_wap = 'pc/';
$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'chat_record/chat_record.tpl.htm');


$id = $_INPUT['seller_id'];
$page = intval($_INPUT['page']);
// ================== pc版头部通用 end ==================

/*
     * 获取一个管理员下面有效的商家列表
     *
     * @param string $admin_user_id    管理员ID
     * @param string $b_select_count   是否返回总数：TRUE返回总数 FALSE返回列表
     * @param string $limit     分页
     * @return array|int
*/

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
$ret = get_api_result('merchant/msg_list.php',array(
	'user_id' => $id
	));
$seller_name = get_seller_nickname_by_user_id($id);
//解码
foreach($ret['data']['list'] as $k=>$value){
	$ret['data']['list'][$k] = iconv('GBK','UTF-8',$value);
	$ret['data']['list'][$k] = json_decode($ret['data']['list'][$k],true);
}


//操作
foreach($ret['data']['list'] as $i=>$value){
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
	$ret['data']['list'][$i]['custom_icon'] = $custom_icon;
	$ret['data']['list'][$i]['custom_name'] = $custom_name;
	$ret['data']['list'][$i]['custom_id'] = $custom_id;
	$ret['data']['list'][$i]['manage_id'] = $manage_id;
	$ret['data']['list'][$i]['msg_diret'] = $msg_diret;
	$ret['data']['list'][$i]['last_connect_day'] = date('Y-m-d',$ret['data']['list'][$i]['send_time']);
	$ret['data']['list'][$i]['last_connect_time'] = date('H:i',$ret['data']['list'][$i]['send_time']);
	//$ret['data']['list'][$k]['good_list'] = $good_list;
}

//编码
$ret['data']['list'] = poco_iconv_arr($ret['data']['list'],'UTF-8','GBK');

$tpl->assign('seller_name',$seller_name);
$tpl->assign('this_seller_id',$id);
$tpl->assign('buyer_list',mall_output_format_data($ret['data']['list']));
$tpl->assign('yue_login_id',$yue_login_id);

?>
