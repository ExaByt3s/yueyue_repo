<?php
/**
 * ��������
 *
 * @author hudingwen
 * @version $Id$
 * @copyright , 18 August, 2015
 * @package default
 */

/**
 * ��ʼ�������ļ�
 */
include_once 'config.php';


// ========================= ��ʼ���ӿ� start =======================

//================= ���ղ��� =================
$goods_id = $_INPUT['goods_id'];
//================= ���ղ��� END =================

// �����Ƿ��жϴ���
$index_url_link = G_MALL_PROJECT_USER_ROOT . '/index.php';

if (empty($goods_id)) 
{
	header("location: ".$index_url_link);
}
// �Ƿ������ĸ
if(preg_match('/[A-Za-z]+/',$goods_id) && !isset($_INPUT['preview']))
{

	header("location: ".$index_url_link);
}




//================= ��ȡ���� =================

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

//  ��������ڼ�¼��������ҳ
if ( empty($ret['data']) )  
{
	header("location: ".$index_url_link);
}

// print_r($ret);


//================= ��ȡ���� END =================
	
//================= ����������� =================
$promise_num = 10; //���� ��ť
$ret['data']['promise_show'] = array_slice($ret['data']['promise'],0,$promise_num);
if(count($ret['data']['promise']) > $promise_num)
{
	$ret['data']['promise_hide'] = array_slice($ret['data']['promise'],$promise_num,count($ret['data']['promise']));
	$ret['data']['promise_has_hide'] = true;
}




// ========================= ��ʼ���ӿ� end =======================



// ========================= ����pc��wapģ�������ݸ�ʽ���� start  =======================
if(MALL_UA_IS_PC == 1)
{

	//****************** pc�� ******************
	include_once './service_detail-pc.php';
}
else
{
	//****************** wap�� ******************
	include_once './service_detail-wap.php';

}



// �����������
$share = mall_output_format_data($ret['data']['share']);
$ret['data']['detail']['value'] = htmlspecialchars_decode($ret['data']['detail']['value']);

//================= ΢��ǩ��������� =================
if(MALL_UA_IS_WEIXIN == 1)
{
	$wx = mall_wx_get_js_api_sign_package();
	$wx_json = mall_output_format_data($wx);
}
$tpl->assign('wx_json', $wx_json);
//================= ΢��ǩ��������� =================
//if($yue_login_id == 123241)
//{
//    print_r($ret);
//}
$promotion = $ret['data']['promotion'];

$tpl->assign('promotion',$promotion);
$tpl->assign('share',$share);
$tpl->assign('resa', $ret);
$tpl->assign('order_link',G_MALL_PROJECT_USER_ROOT.'/order/confirm.php?goods_id='.$ret['data']['goods_id']);

// PC���������������������̼���Ϣ
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



// ========================= ����ģ�����  =======================
//dump($tpl);
$tpl->output();
//================= ����������� END =================

 
?>
