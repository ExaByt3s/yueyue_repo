<?php
/**
 * 
 *
 * @author hudingwen
 * @version 1.0
 * @copyright , 30 June, 2015
 * @package default
 */

/**
 * 订单列表
 */
define('G_SIMPLE_INPUT_CLEAN_VALUE',1);

include_once('../common.inc.php');

$log_arr['result'] = $yue_login_id;

pai_log_class::add_log($log_arr, 'yue_login_id', 'test_ding');

// 没有登录的处理
if(empty($yue_login_id))
{
	$output_arr['code'] = -1;
	$output_arr['msg']  = '尚未登录,非法操作';
	$output_arr['data'] = array();
	mobile_output($output_arr,false);
	exit();
}

// 传递的参数，如果是需要是数字类型，就要进行intval转换，如果是字符串类型，必须要进行trim转换
$goods_id = intval($_INPUT['goods_id']);
$prices_type_id = intval($_INPUT['prices_type_id']);
$service_time = strtotime($_INPUT['service_time']);
$service_location_id = trim($_INPUT['service_location_id']);
$service_address = trim($_INPUT['service_address']);
$service_people = intval($_INPUT['service_people']);
$description = trim($_INPUT['description']);
$quantity = intval($_INPUT['quantity']);
$redirect_url = trim($_INPUT['redirect_url']);
$is_auto_accept = intval($_INPUT['is_auto_accept']);
$is_auto_sign = intval($_INPUT['is_auto_sign']);
$promotion_id = intval($_INPUT['promotion_id']);

$service_address = iconv("UTF-8", "gbk//TRANSLIT", $service_address);
$description = iconv("UTF-8", "gbk//TRANSLIT", $description);

//获取商品信息
$mall_goods_obj = POCO::singleton('pai_mall_goods_class');
$goods_info = $mall_goods_obj->get_goods_info($goods_id);

//处理自动接受
if( $is_auto_accept<1 )
{
	$is_auto_accept = intval($goods_info['goods_data']['is_auto_accept']);
}
if( $is_auto_accept<1 && $goods_info['goods_data']['type_id']==41 ) //美食品类的商家，全部自动接受 2015-09-23
{
	$is_auto_accept = 1;
}
$is_auto_accept = $is_auto_accept>0 ? 1 : 0;

//处理自动签到
if( $is_auto_sign<1 )
{
	$is_auto_sign = intval($goods_info['goods_data']['is_auto_sign']);
}
$is_auto_sign = $is_auto_sign>0 ? 1 : 0;

// 提交页面
 //@param int $buyer_user_id 买家用户ID
 //@param array $detail_list 商品详细列表
 //@param $more_info array( 'referer'=>'', 'description'=>'', )
 //@referer 订单来源
 //@description 订单备注
 //@return array array('result'=>0, 'message'=>'', 'order_sn'=>'')
 //@tutorial
 $detail_list[] = array(
   'goods_id'=>$goods_id,
   'prices_type_id'=>$prices_type_id,
   'service_time'=>$service_time,
   'service_location_id'=>$service_location_id,
   'service_address' => $service_address,
   'quantity'=>$quantity,
   'goods_promotion_id'=>$promotion_id,
   'service_people'=>$service_people
);	

// 订单类：pai_mall_order_class
$mall_order_obj = POCO::singleton('pai_mall_order_class');

// 如果没有登录的，要终止程序继续跑下去
if(empty($yue_login_id))
{   	
   $output_arr['code'] = 0;
   $output_arr['msg']  = 'Error';
   $output_arr['data'] = array();
   exit;
}

$__is_weixin = stripos($_SERVER['HTTP_USER_AGENT'], 'micromessenger') ? true : false;
$__is_yueyue_app = (preg_match('/yue_pai/',$_SERVER['HTTP_USER_AGENT'])) ? true : false; 

if($__is_yueyue_app)
{
	$referer = 'app';
}
else if($__is_weixin)
{
	$referer = 'weixin';
}
else
{
	$referer = 'wap';
}

$more_info = array(
	'referer'=>$referer,
	'is_auto_accept'=>$is_auto_accept,
	'is_auto_sign'=>$is_auto_sign,
 	'description'=>$description
);

$ret = $mall_order_obj->submit_order($yue_login_id,$detail_list, $more_info);

// 提交成功
if($ret['result'] == 1)
{
      $output_arr['msg']  = $ret['message'];
	  
	  $ret['url'] = '../pay/?order_sn='.$ret['order_sn'].'&redirect_url='.urlencode($redirect_url);
}
else
{
	  $output_arr['msg']  = $ret['message'];
}
$output_arr['code'] = $ret['result'];
$output_arr['data'] = $ret;

mall_mobile_output($output_arr,false);
exit;

?>