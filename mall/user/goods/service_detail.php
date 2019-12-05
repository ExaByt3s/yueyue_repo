<?php
/**
 * 服务详情
 *
 * @author hudingwen
 * @version $Id$
 * @copyright , 18 August, 2015
 * @package default
 */

/**
 * 初始化配置文件
 */
include_once 'config.php';


// ========================= 初始化接口 start =======================

//================= 接收参数 =================
$goods_id = $_INPUT['goods_id'];
//================= 接收参数 END =================

// 参数非法判断处理
$index_url_link = G_MALL_PROJECT_USER_ROOT . '/index.php';

if (empty($goods_id)) 
{
	header("location: ".$index_url_link);
}
// 是否包含字母
if(preg_match('/[A-Za-z]+/',$goods_id) && !isset($_INPUT['preview']))
{

	header("location: ".$index_url_link);
}




//================= 获取数据 =================

if($_INPUT['preview'] == 1)
{
	$ret = get_api_result('customer/sell_services_preview.php',array(
	'user_id' => $yue_login_id,
	'goods_id' => $goods_id
	));
}
else
{
	$ret = get_api_result('customer/sell_services.php',array(
	'user_id' => $yue_login_id,
	'goods_id' => $goods_id
	));
}

if($_INPUT['print'] == 1)
{
	print_r($ret);
	die();
}

//  如果不存在记录，跳到首页
if ( empty($ret['data']) )  
{
	header("location: ".$index_url_link);
}

// print_r($ret);


//================= 获取数据 END =================
	
//================= 数据整合输出 =================
$promise_num = 10; //更多 按钮
$ret['data']['promise_show'] = array_slice($ret['data']['promise'],0,$promise_num);
if(count($ret['data']['promise']) > $promise_num)
{
	$ret['data']['promise_hide'] = array_slice($ret['data']['promise'],$promise_num,count($ret['data']['promise']));
	$ret['data']['promise_has_hide'] = true;
}




// ========================= 初始化接口 end =======================



// ========================= 区分pc，wap模板与数据格式整理 start  =======================
if(MALL_UA_IS_PC == 1)
{

	//****************** pc版 ******************
	include_once './service_detail-pc.php';
}
else
{
	//****************** wap版 ******************
	include_once './service_detail-wap.php';

}



// 输出分享内容
$share = mall_output_format_data($ret['data']['share']);
$ret['data']['detail']['value'] = htmlspecialchars_decode($ret['data']['detail']['value']);

//================= 微信签名数据输出 =================
if(MALL_UA_IS_WEIXIN == 1)
{
	$wx = mall_wx_get_js_api_sign_package();
	$wx_json = mall_output_format_data($wx);
}
$tpl->assign('wx_json', $wx_json);
//================= 微信签名数据输出 =================
//if($yue_login_id == 123241)
//{
//    print_r($ret);
//}
$promotion = $ret['data']['promotion'];

$tpl->assign('promotion',$promotion);
$tpl->assign('share',$share);
$tpl->assign('resa', $ret);
$tpl->assign('order_link',G_MALL_PROJECT_USER_ROOT.'/order/confirm.php?goods_id='.$ret['data']['goods_id']);

// PC版的数据输出，用于整合商家信息
if(MALL_UA_IS_PC == 1)
{
	$seller_user_id = $ret['data']['user']['user_id'];
	$seller_info = get_api_result('customer/trade_seller_property.php',array(
	    'user_id' => $yue_login_id,
	    'seller_user_id'=> $seller_user_id,
		'type_id'=>$ret['data']['profile_type']
	    ), true); 
	$tpl->assign('seller_info', $seller_info);

	$goods_id = $ret['data']['goods_id'];
	$service_commit = get_api_result('customer/sell_services_appraise.php',array(
	    'user_id' => $yue_login_id,
	    'seller_user_id'=> $seller_user_id,
		'goods_id'=>$goods_id,
		'limit' => '0,1000000'
	    ), true); 
	$tpl->assign('service_commit', $service_commit);
	$tpl->assign('commit_json',mall_output_format_data($service_commit['data']['list']));
}

if($yue_login_id == 100001){
	//dump($tpl);
}



// ========================= 最终模板输出  =======================
//dump($tpl);
$tpl->output();
//================= 数据整合输出 END =================

 
?>
