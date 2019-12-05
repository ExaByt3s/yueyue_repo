<?php
/**
 * POCO约拍APP - 版本控制
 */


$config = array(
    'archive' => array(
        'style' => '2.2.0', // 样式
        'script' => '2.2.0', // 脚本
        'app' => '2.2.0' // app离线版本
    ),
    'beta' => array( 
        'style' => '0.0.1-beta', // 样式
        'script' => '0.0.1-beta', // 脚本
        'app' => '0.0.1-beta', // app离线版本
        'cache_ver' => '1.0.006-beta'
    )
);

//$config['archive'] = $config['beta'];

if (isset($_GET['json']))
 {
    header('Content-Type: application/json');
    echo json_encode($config);
} else 
{
    return $config;
}

?>