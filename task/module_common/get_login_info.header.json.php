<?php
/**
 * 引入文件
 */
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

/**
 * 初始化
 */
// 用户信息
$user_icon = get_user_icon($yue_login_id);
$user_nickname = get_user_nickname_by_user_id($yue_login_id);

$callback = $_GET['callback'];
$yue_login_id = $_COOKIE['yue_member_id'];

// 安全验证
if ((int)$yue_login_id < 1) 
{
	header('HTTP/1.0 403 Forbidden'); // 返回禁止访问header
	exit;
}

$return_data = array();


/**
 * 数据处理
 */
// 获取用户基本信息
// --------------------------------------------------------
$nickname = $_COOKIE['nickname'];

// 商家信息  认证v
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
 * 输出内容
 */

// 文件类型
header('Content-Type: application/json');

// 构造JS格式的对象变量
if ($callback) 
{
    echo $callback."(".json_encode(array('code' => 1, 'msg' => 'success', 'data' => $return_data)).");";
} else 
{
    echo json_encode(array('code' => 1, 'msg' => 'success', 'data' => $return_data));
}

?>