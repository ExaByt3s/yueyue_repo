<?php
ignore_user_abort ( true );
/**
 * �����˿�
 */

include_once ('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

/**
 * ҳ����ղ���
 */
$date_id = intval ( $_INPUT ['date_id'] );
$reason = mb_convert_encoding ( trim ( $_INPUT ['reason'] ), 'gbk', 'utf-8' );
$remark = mb_convert_encoding ( trim ( $_INPUT ['remark'] ), 'gbk', 'utf-8' );

$type = $_INPUT ['type'];

if (! $yue_login_id)
{
	die ( 'no login' );
}


	
$ret_arr = submit_date_cancel_application ( $date_id, $reason, $remark );

$ret = $ret_arr['code'];

if($ret_arr['type']=='wait')
{
	if ($ret == 1)
	{
		$msg = "�˿�ɹ�";
	} elseif ($ret == - 1)
	{
		$msg = "��������";
	} elseif ($ret == - 2)
	{
		$msg = "�˿�ʧ��";
	}elseif ($ret == - 3)
	{
		$msg = "״̬�쳣";
	}
}else
{
	if ($ret == 1)
	{
		$msg = "�ύ�ɹ�";
	} elseif ($ret == - 1)
	{
		$msg = "ģ�ػ�δ��������";
	} elseif ($ret == - 2)
	{
		$msg = "����ȡ����¼";
	} elseif ($ret == - 3)
	{
		$msg = "�ȯ�ѱ�ɨ��";
	}
}

$output_arr ['msg'] = $msg;

$output_arr ['data'] = $ret;

mobile_output ( $output_arr, false );

?>