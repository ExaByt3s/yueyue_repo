<?php
/**
 * app publish
 */

$cache_ver = 'wx-1.2.002';
$cache_beta_ver = 'beta-0.0.211';

$config = array(
	'dev' => array(
        'style' => '',
        'script' => '', 
        'app' => '',
        'cache_ver' => '',
        'cache_time' => 201412241049,
        'style_time' => 201412241049,
        'page_url' => 'http://yp.yueus.com/m/webdev'
    ),
    'archive' => array(
        'style' => 'wx-1.0.0',
        'script' => 'wx-1.0.0', 
        'app' => 'wx-1.0.0',
        'cache_ver' => 'wx-1.0.0',
        'cache_time' => 201412241049,
        'style_time' => 201412241049,
        'page_url' => 'http://yp.yueus.com/m/index.html?vwx-1.0.0'
    ),
    'test' => array( 
        'style' => '0.0.1-beta', 
        'script' => '0.0.1-beta',
        'app' => '0.0.1-beta', 
        'cache_ver' => '1.0.003-test',
        'cache_time' => 201412221758,
        'style_time' => 201412241049,
        'page_url' => 'http://yp.yueus.com/m/webtest?vwx0.0.1-beta'
    ),
    'beta' => array( 
        'style' => '0.0.1-beta', 
        'script' => '0.0.1-beta', 
        'cache_ver' => $cache_beta_ver,
        'cache_time' => 201504071215,
        'style_time' => 201504071215, 
        'page_url' => 'http://yp.yueus.com/m/webbeta?v'.$cache_beta_ver
    ),
    'wx' => array(
        'style' => 'wx-1.2.0', 
        'script' => 'wx-1.2.0', 
        'app' => 'wx-1.2.0', 
        'cache_ver' => $cache_ver,
        'cache_time' => 201508061259,
        'style_time' => 201508061259,
        'page_url' => 'http://yp.yueus.com/m/wx?v'.$cache_ver
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