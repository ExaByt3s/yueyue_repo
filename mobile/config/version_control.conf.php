<?php
/**
 * POCOԼ��APP - �汾����
 */


$config = array(
    'archive' => array(
        'style' => '2.2.0', // ��ʽ
        'script' => '2.2.0', // �ű�
        'app' => '2.2.0' // app���߰汾
    ),
    'beta' => array( 
        'style' => '0.0.1-beta', // ��ʽ
        'script' => '0.0.1-beta', // �ű�
        'app' => '0.0.1-beta', // app���߰汾
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