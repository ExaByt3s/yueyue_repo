<?php
define('MALL_USER_DIR_APP',"./");
include_once MALL_USER_DIR_APP.'common.inc.php';

foreach ($_POST as $key => $value) {
    if ($key == 'path') {
        continue;
    }
    $param[$key] = $value;   // ��װ����
}

$path = trim($_INPUT['path']);

$ret = get_api_result($path,$param,false,true);

//$ret = json_encode($ret);  // ����ת����

//die();

header('Content-type: application/json;');   // �����ļ���ʽ
header("Access-Control-Allow-Origin:*");

echo $ret;
?>