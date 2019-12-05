<?php
/**
 * 全站地区数据
 */
include_once './area.conf.php';

$ouput_arr['province'] = $area['province'];
$ouput_arr['city'] = $area['city'];
$ouput_arr['district'] = $area['district'];

/**
 * 输出内容
 */

// 文件类型
header('Content-Type: application/json');

// 构造JS格式的对象变量
if ($callback) 
{
    echo $callback."(".json_encode(array('code' => 1, 'msg' => 'success', 'data' => $ouput_arr)).");";
} else 
{
    echo json_encode(array('code' => 1, 'msg' => 'success', 'data' => $ouput_arr));
}

?>