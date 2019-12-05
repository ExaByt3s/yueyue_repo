<?php
/**
 * 
 *
 * @author hudingwen
 * @version $Id$
 * @copyright , 8 July, 2015
 * @package default
 */

/**
 * Define ֧��ҳ
 */

include_once('../common.inc.php');

// Ȩ�޼��
mall_check_user_permissions($yue_login_id);

// ������
$mall_order_obj = POCO::singleton('pai_mall_order_class');

$order_sn = trim($_INPUT['order_sn']);
$third_code = trim($_INPUT['third_code']);
$coupon_sn = trim($_INPUT['coupon_sn']);
$available_balance = trim($_INPUT['available_balance']);
$is_available_balance = trim($_INPUT['is_available_balance']);
$redirect_url = trim($_INPUT['redirect_url']);
$notify_url =  G_MALL_PROJECT_USER_ROOT . '/ajax/pay_order_notify.php';
$user_id = $yue_login_id;
$more_info = array('page_total_amount'=>trim($_INPUT['total_amount']));


//��ȡ������Ϣ

/**
* ��ȡ������Ϣ
* @param string $order_sn
* @return array
*/	 
$order_full_info = $mall_order_obj->get_order_full_info($order_sn);

$buyer_user_id = intval($order_full_info['buyer_user_id']);

if( $yue_login_id>0 && $yue_login_id==$buyer_user_id )
{
/**
   * �ύ֧��
   * @param string $order_sn
   * @param int $user_id ����û�ID
   * @param double $available_balance ҳ�浱ǰ���
   * @param int $is_available_balance �Ƿ�ʹ����0�� 1��
   * @param string $third_code ֧����ʽ alipay_purse tenpay_wxapp�����û�ʹ�����ȫ��֧��ʱ��Ϊ��
   * @param string $redirect_url ֧���ɹ�����ת��url ���û�ʹ�����ȫ��֧��ʱ��Ϊ��
   * @param string $notify_url
   * @return array array('result'=>0, 'message'=>'', 'payment_no'=>'', 'request_data'=>'')
   * result 1��ȡ������֧����2���֧���ɹ�
   */
   $ret = $mall_order_obj->submit_pay_order($order_sn, $user_id, $available_balance, $is_available_balance, $third_code, $redirect_url, $notify_url, $coupon_sn,$more_info);
}
else
{
	$ret = array('result'=>-1, 'message'=>'��������', 'payment_no'=>'', 'request_data'=>'');
}

$ret['third_code'] = $third_code;
$ret['order_sn'] = $order_sn;

//��־
pai_log_class::add_log($ret, 'pay end', 'pay_tt');

mall_mobile_output($ret,false);

?>
