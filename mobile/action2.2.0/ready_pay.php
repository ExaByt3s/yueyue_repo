<?php

/**
 * ׼��֧��
 * 
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

if(!$yue_login_id)
{
	die('no login');
}


$output_arr['code'] = 0;
$output_arr['message'] = "��Ǹ���ף�ģ����Լ��������ͣʹ��";
mobile_output($output_arr,false);
exit();


// �޸�Android 1.0.6 ֧����͸����
sleep(1);

$data['from_date_id']   = $yue_login_id; //��ӰʦID
$data['to_date_id']     = (int)$_INPUT['model_id'];  //ģ��ID
$data['date_status']    = 'wait';  //״̬
$data['date_time']      = strtotime(trim($_INPUT['date']));  //Լ��ʱ��
$data['date_style']     = mb_convert_encoding(trim($_INPUT['style']),'gbk','utf-8'); //������
$data['date_hour']      = 1;  //����ʱ��
$data['hour']           = $_INPUT['hour'];  //����ʱ��
$data['date_price']     = $_INPUT['price'];  //����
$data['limit_num']     = (int)$_INPUT['limit_num'];  //��������
$data['date_address']   = iconv("UTF-8", "gbk//TRANSLIT", $_INPUT['address']); 
$data['source']    = 'app';
$data['direct_confirm_id']   =  (int)$_INPUT['direct_confirm_id'];

$ret = add_date_op($data);

if($ret['status_code']==1)
{
	$output_arr['code'] 	= $ret['status_code'];
	$output_arr['message'] 	= $ret['message'];
	$output_arr['date_id']  = $ret['date_id'];
}
else
{
	$output_arr['code'] 	= $ret['status_code'];
	$output_arr['message'] 	= $ret['message'];
	
}

mobile_output($output_arr,false);

?>