<?php
/**
 * 测试
 */
header('Content-Type: application/json');

$version = require_once('/disk/data/htdocs232/poco/pai/mobile/config/version_control.conf.php');


echo json_encode(array(
    'packageUrl' => 'http://yp-wifi.yueus.com/mobile/package/resource/'.$version['beta']['app'].'/app.zip',
	'no_wifi_packageUrl' => 'http://yp.yueus.com/mobile/package/resource/'.$version['beta']['app'].'/app.zip',

    // 软件需求，只能数字和小数点，网站这边方便区分，保留分支名
    //'packageVer' => str_replace('-beta', '', $version['beta']['app']),
    'packageVer' => '1.0.023-beta',
    'packageForceUpdate' => 0,
    'enterUrl' => 'http://yp-wifi.yueus.com/mobile/app',
	'no_wifi_enterUrl' => 'http://yp.yueus.com/mobile/app',
    'appVer' => '1.0',
    'appForceUpdate' => 0,
    "isShowAppTips" => 1,
    "appUpdateTips" => "亲，有新内容噢，请刷新！by 肥龙在天测试" 


));
?>