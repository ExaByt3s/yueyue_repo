<?php

/**
 * ����
 * hdw 2015.2.28 ������֧���ͼ�������֧���ϲ�
 * �������Ļʹ��
 */

include_once('../common.inc.php');

$event_id			= intval($_INPUT['event_id']);

if(empty($yue_login_id))
{
	$output_arr['code'] = -20;
	$output_arr['message'] = '��δ��¼';

	mobile_output($output_arr,false);
	die();
}

$special_event_config = include('/disk/data/htdocs232/poco/pai/config/special_event_config.php');

if($special_event_config[$event_id])
{
	$special_event_arr = explode(',',$special_event_config[$event_id]['yue_user_id']);
	
	if(!in_array($yue_login_id,$special_event_arr))
	{
		$output_arr['code'] = -20;
		$output_arr['message'] = '�ûֻ��ָ���û�����';
	
		mobile_output($output_arr,false);
		die();
	}
}

// �޸�1.0.6��bug���ӿ��жϱ�������
$test_enroll_num = intval($_INPUT['table_arr'][0]['enroll_num']);
if($test_enroll_num>3)
{
	$output_arr['code'] = -20;
	$output_arr['message'] = '�����������ó���3��';

	mobile_output($output_arr,false);
	die();
}


$has_join = intval($_INPUT['has_join']);

// ������enroll_id����δ֧��δ����״̬
if($has_join == 0)
{
	/*
	* �����
	* 
	* @param array    $data   
	* @param int    $event_id   �ID
	* @param int    $user_id   ������ID
	* @param int    $phone   �绰����
	* return bool
	*/


	$phone				= intval($_INPUT['phone']);
	$user_id			= $yue_login_id;
	$enroll_data = array(
	   'user_id'=>$yue_login_id, 
	   'event_id'=>$event_id,
	   'phone'=>$phone,
	   'source'=>'app',
	);
	$sequence_data		= $_INPUT['table_arr'];  //�����������顡 
	$user_balance       = $_INPUT['available_balance'];
	$is_available_balance = $_INPUT['is_available_balance'] == 1 ? 0 : 1;// �������֧�����ݲ��������ֶ� is_available_balance Ϊ0 �����֧����1�ǵ�����֧��
	$third_code         = trim($_INPUT['third_code']);
	$redirect_url 		= urldecode($_INPUT['redirect_url']);
	$share_event_id     = $_COOKIE['share_event_id'];
    $share_phone        = $_COOKIE['share_phone'];
	$notify_url         = G_MALL_PROJECT_USER_ROOT . "/ajax/pay_activity_notify.php?share_event_id={$share_event_id}&share_phone={$share_phone}";

	//�Ż�ȯ
	if( is_array($sequence_data[0]) && !empty($sequence_data[0]) && isset($_INPUT['coupon_sn']) )
	{
		$sequence_data[0]['coupon_sn'] = trim($_INPUT['coupon_sn']);
	}

	/**
	 * Լ���ύ����  modify hai 20140911
	 * @param array $enroll_data 
	 * array(
	 *  'user_id'=>'',  �û�ID  [�ǿ�]
	 *  'event_id'=>,   �ID  [�ǿ�]
	 *  'phone'=>'',    �ֻ�����
	 *  'email'=>'',    ����
	 *  'remark'=>      ��ע
	 * )
	 * @param array $enroll_data  �������ݵĶ�ά����  
	 * array(
	 *  0=>array(
	 *                         
	 *    'enroll_num'=>''  [�ǿ�]    ��������      
	 *    'table_id'=>''    [�ǿ�]    ��������ID 
	 *    'coupon_sn'=>''             �Ż���
	 *  
	 *  ),
	 *  1=>array(...
	 * )
	 * @param int    $user_balance �û����  �����ж��û��Ƿ�ͣ��ҳ��̫��ʱ��û�ύ  ���û����䶯�����ύ
	 * @param int    $is_available_balance   0Ϊ���֧�� 1Ϊ������֧��   �������֧������Ҫ������ת������������֧��
	 * @param string $third_code   ������֧���ı�ʶ ����ʱ֧��΢�ź�֧����Ǯ�� alipay_purse��tenpay_wxapp ���û�ʹ�����ȫ��֧��ʱ��Ϊ��
	 * @param string $redirect_url ֧���ɹ�����ת��url ���û�ʹ�����ȫ��֧��ʱ��Ϊ��
	 * @param string $notify_url ֧���ɹ����첽��url��Ϊ��ʱʹ�������ļ��еĴ���ҳ
	 * @return array array( 'status_code'=>0,'message'=>'','cur_balance'=>'','request_data'=>$request_data)
	 * ����ֵ status_code Ϊ״̬ 
	 * status_code����ֵ 
		 * -1  ��������
		 * -2  �û������ 
		 * -3  ��ѽ���
		 * -4  ������Ϊ��֯��  ������
		 * -5  ���������Ƿ�
		 * -6  ĳһ���Ѿ�������
		 * -7  ĳһ�������ѹر� �������ٱ���
		 * -8  ������������  ����ʧ��
		 * -9  ��������ɲ�֧��  ����ʧ��
		 * -10  �û�����б䶯
		 * -11  ���֧��ʧ��
		 * -12 ��ת��������֧����������
	 * status_code��ȷֵ
	 *   1Ϊ�ύ�ɹ� ����֯������
	 *   2Ϊ���֧���ɹ�   
	 *   3Ϊ������������ɹ�������ת���������� 
	 * message���ص���Ϣ cur_balance �����û���ǰ��ʵ���[��status_code==2�����֧���ɹ����д�key] 
	 * request_data ����������������ַ���[��Ҫ��������ʱ��ŷ���]
	 *
	 */
	$ret = add_enroll_op($enroll_data,$sequence_data,$user_balance,$is_available_balance,$third_code,$redirect_url,$notify_url);
	if($ret['status_code'] == 2)
	{
		$ret['status_code'] = 1;
	}
	else if($ret['status_code'] == 3)
	{
		$ret['status_code'] = 2;
	}
}
// ����enroll_id����δ֧���ѱ���״̬�� ִ���ٴ�֧���ķ���
else
{
	
	$user_id = $yue_login_id;
	$enroll_data = array(
	   'user_id'=>$yue_login_id, 
	   'event_id'=>$event_id,
	);
	$enroll_id_arr			= $_INPUT['enroll_id_arr'];
	$user_balance			= $_INPUT['available_balance'];
	$is_available_balance	= $_INPUT['is_available_balance'] ;
	$third_code				= trim($_INPUT['third_code']);
	$redirect_url 			= urldecode($_INPUT['redirect_url']);
	$share_event_id     = $_COOKIE['share_event_id'];
    $share_phone        = $_COOKIE['share_phone'];
	$notify_url				= G_MALL_PROJECT_USER_ROOT . "/ajax/pay_activity_notify.php?share_event_id={$share_event_id}&share_phone={$share_phone}";

	//�Ż�ȯ
	$coupon_sn = trim($_INPUT['coupon_sn']);
	
	/**
	 * Լ�ı���������֧��
	 * @param array $enroll_data
	 * array(
	 *  'user_id'=>'',  �û�ID  [�ǿ�]
	 *  'event_id'=>,   �ID  [�ǿ�]
	 * )
	 * @param array $enroll_id_arr  ����ID
	 * array(
	 *  1,2
	 * )
	 * @param int    $user_balance �û����  �����ж��û��Ƿ�ͣ��ҳ��̫��ʱ��û�ύ  ���û����䶯�����ύ
	 * @param int    $is_available_balance   0Ϊ���֧�� 1Ϊ������֧��   �������֧������Ҫ������ת������������֧��
	 * @param string $third_code   ������֧���ı�ʶ ����ʱ֧��΢�ź�֧����Ǯ�� alipay_purse��tenpay_wxapp ���û�ʹ�����ȫ��֧��ʱ��Ϊ��
	 * @param string $redirect_url ֧���ɹ�����ת��url ���û�ʹ�����ȫ��֧��ʱ��Ϊ��
	 * @return array array( 'status_code'=>0,'message'=>'','cur_balance'=>'','request_data'=>$request_data)
	 * ����ֵ status_code Ϊ״̬
	 * status_code����ֵ
	 * -1  ��������
	 * -2  �û������
	 * -3  ��ѽ���
	 * -10 �û�����б䶯
	 * -11 ���֧��ʧ��
	 * -12 ��ת��������֧����������
	 * status_code��ȷֵ
	 *   1Ϊ���֧���ɹ�
	 *   2Ϊ������������ɹ�������ת����������
	 * message���ص���Ϣ cur_balance �����û���ǰ��ʵ���[��status_code==2�����֧���ɹ����д�key]
	 * request_data ����������������ַ���[��Ҫ��������ʱ��ŷ���]
	 *
	 */
	$ret = again_enroll_op($enroll_data,$enroll_id_arr,$user_balance,$is_available_balance,$third_code,$redirect_url,$notify_url, $coupon_sn);

	
}


/***************ͳһ����***************/

$output_arr['code'] = $ret['status_code'];
$output_arr['data'] = $ret['request_data'];
$output_arr['payment_no'] = $ret['payment_no'];
$output_arr['channel_return'] = $redirect_url;
$output_arr['message'] = $ret['message'];
$output_arr['cur_balance'] = $ret['cur_balance'];
$output_arr['third_code'] = $third_code;


mall_mobile_output($output_arr,false);

?>