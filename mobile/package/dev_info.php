<?php
/**
 * 开发
 */
header('Content-Type: application/json');



echo json_encode(array(
    'packageUrl' => 'http://pai.poco.cn/mobile/package/resource/app.zip',

    // 软件需求，只能数字和小数点，网站这边方便区分，保留分支名
    'packageVer' => '0.0.1',
    'packageForceUpdate' => 0,
    'enterUrl' => 'http://pai.poco.cn/mobile/appbeta',
    'appVer' => '1.0',
    'appForceUpdate' => 0
));
?>