<?php
define('MALL_USER_DIR_APP',"./");
include_once MALL_USER_DIR_APP.'common.inc.php';

foreach ($_POST as $key => $value) {
    if ($key == 'path') {
        continue;
    }
    $param[$key] = $value;   // 组装参数
}

$path = trim($_INPUT['path']);

$ret = get_api_result($path,$param,false,true);

//$ret = json_encode($ret);  // 数据转编码

//die();

header('Content-type: application/json;');   // 定义文件格式
header("Access-Control-Allow-Origin:*");

echo $ret;
?>