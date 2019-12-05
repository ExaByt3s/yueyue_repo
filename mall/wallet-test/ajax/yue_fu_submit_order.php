<?php


include_once('../common.inc.php');

/**
 * ҳ����ղ���
 */

$prime_prices = trim($_INPUT['price']) ;
$seller_user_id = intval($_INPUT['seller_user_id']) ;
$mark = iconv("UTF-8","GBK",trim($_INPUT['mark']));


if (!$mark) 
{
    $mark = '���δ��������' ;
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



//Լ��
$order_payment_obj = POCO::singleton('pai_mall_order_payment_class');

$more_info = array(
	'description' => $mark, //��������ע
	'referer' =>  $referer   //������Դ��app weixin pc wap oa
);

/**
 * �ύ����
 * @param int $buyer_user_id �������û�ID
 * @param double $prime_prices ������
 * @param int $seller_user_id �̼��û�ID
 * @param array $more_info ������Ϣ
 * @return array array('result'=>0, 'message'=>'', 'order_id'=>0, 'order_sn'=>'')
 * @tutorial
 * 
 * $more_info = array(
 * 	'description' => '', //��������ע
 *  'referer' => '', //������Դ��app weixin pc wap oa
 * );
 * 
 */
$submit_rst = $order_payment_obj->submit_order($yue_login_id, $prime_prices, $seller_user_id, $more_info);


mall_mobile_output ( $submit_rst, false );

?>