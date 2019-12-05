<?php
/*
	获取userinfo的v2版本，是支持跨域获取信息
*/
/**
 * 引入文件
 */
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

// 安全验证
if ((int)$yue_login_id < 1) 
{
	header('HTTP/1.0 403 Forbidden'); // 返回禁止访问header
	exit;
}

/**
 * 输出内容
 */

// 文件类型
header('Content-Type: application/json');

$__yue_user_info['nickname'] = iconv('GBK', 'UTF-8', $__yue_user_info['nickname']);

// 构造JS格式的对象变量
if ($callback) 
{
    echo $callback."(".json_encode(array('code' => 1, 'msg' => 'success', 'data' => $__yue_user_info)).");";
} else 
{
    echo json_encode(array('code' => 1, 'msg' => 'success', 'data' => $__yue_user_info));
}

?>