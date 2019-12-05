<?php
/**
 * �����ļ�
 */
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

/**
 * ��ʼ��
 */
// �û���Ϣ
$user_icon = get_user_icon($yue_login_id);
$user_nickname = get_user_nickname_by_user_id($yue_login_id);

$callback = $_GET['callback'];
$yue_login_id = $_COOKIE['yue_member_id'];

// ��ȫ��֤
if ((int)$yue_login_id < 1) 
{
	header('HTTP/1.0 403 Forbidden'); // ���ؽ�ֹ����header
	exit;
}

$return_data = array();


/**
 * ���ݴ���
 */
// ��ȡ�û�������Ϣ
// --------------------------------------------------------
$nickname = $_COOKIE['nickname'];

// �̼���Ϣ  ��֤v
$task_profile_obj = POCO::singleton('pai_task_profile_class');
$profile_info = $task_profile_obj->get_profile_info($yue_login_id,2);
$is_vip = $profile_info['is_vip'];

$return_data['user_info'] = array(
    'user_id' => (int)$yue_login_id,
    'nickname' => iconv('GBK', 'UTF-8', $user_nickname),
    'avatar' => $user_icon,
	'is_vip' => $is_vip,
    'link' => ''
);


/**
 * �������
 */

// �ļ�����
header('Content-Type: application/json');

// ����JS��ʽ�Ķ������
if ($callback) 
{
    echo $callback."(".json_encode(array('code' => 1, 'msg' => 'success', 'data' => $return_data)).");";
} else 
{
    echo json_encode(array('code' => 1, 'msg' => 'success', 'data' => $return_data));
}

?>