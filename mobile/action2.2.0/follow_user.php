<?php

/**
 * ��ע�û�
 * hdw 2014.9.15
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

/**
 * ���ղ���
 */
$type = trim($_INPUT['type']);
$be_follow_user_id = intval($_INPUT['be_follow_user_id']);

$pai_obj = POCO::singleton('pai_user_follow_class');

switch($type)
{
	case 'follow':
		/*
		 * ��ӹ�ע
		 * 
		 * @param int    $follow_user_id    ��ע���û�ID
		 * @param int    $be_follow_user_id ����ע���û�ID
		 * 
		 * return bool 
		 */
		
		if($yue_login_id==$be_follow_user_id)
		{
			$msg = '�������Լ���ע�Լ�Ŷ��';
			$ret = 0;
		}else
		{
		    $ret = $pai_obj->add_user_follow($yue_login_id, $be_follow_user_id);
	        $msg = '�ɹ���ע';
		}

		break;
	case 'no_follow':
		/*
		 * ȡ����ע
		 * 
		 * @param int    $follow_user_id    ��ע���û�ID
		 * @param int    $be_follow_user_id ����ע���û�ID
		 * 
		 * return bool
		 */
		$ret = $pai_obj->cancel_follow($yue_login_id, $be_follow_user_id);
		$msg = 'ȡ����ע';
		break;


}

$is_follow = $pai_obj->check_user_follow($yue_login_id, $be_follow_user_id);
$is_be_follow = $pai_obj->check_user_follow($be_follow_user_id, $yue_login_id);

if($is_follow && $is_be_follow)
{
	$follow_status=2;
}
elseif($is_follow)
{
	$follow_status = 1;
}
else
{
	$follow_status = 0;
}


$output_arr['is_follow'] = $follow_status;
$output_arr['code'] = $ret;
$output_arr['msg']  = $msg;

mobile_output($output_arr,false);

?>