<?php
/**
 * Created by PhpStorm.
 * User: heyaohua
 * Date: 2015/6/23
 * Time: 14:01
 */
include_once("protocol_common.inc.php");

switch($_GET['name']) {
    case 'get_buyer_index':
        mobile_get_buyer_index();
        break;

    case 'get_classify_list':
        mobile_get_classify_list();
        break;

    case 'get_goods_list':
        mobile_get_goods_list();
        break;
}

function mobile_get_buyer_index()
{
    $url    = 'http://yp.yueus.com/mobile_app/customer/buyer_index.php';
    $param = array('user_id'=> 100028,"location_id"=>"101029001");

    $post_data = array(
        'version'   => '3.0.0',
        'os_type'   => 'android',
        'ctime'     => time(),
        'app_name'  => 'poco_yuepai_android',
        'sign_code' => substr(md5('poco_'.json_encode($param).'_app'), 5, -8),
        'is_enc'    => 0,
        'param'     => $param,
    );
    $post_data = json_encode($post_data);
    $post_data = iconv('GBK', 'UTF-8', $post_data);

    print_r(mobile_app_curl($url, $post_data));
}


function mobile_get_classify_list()
{
    $url    = 'http://yp.yueus.com/mobile_app/customer/classify_list.php';
    $param = array('user_id'=> 100028,'location_id'=>'101029001', 'query'=>'9421');

    $post_data = array(
        'version'   => '3.0.0',
        'os_type'   => 'android',
        'ctime'     => time(),
        'app_name'  => 'poco_yuepai_android',
        'sign_code' => substr(md5('poco_'.json_encode($param).'_app'), 5, -8),
        'is_enc'    => 0,
        'param'     => $param,
    );
    $post_data = json_encode($post_data);
    $post_data = iconv('GBK', 'UTF-8', $post_data);

    print_r(mobile_app_curl($url, $post_data));
}

function mobile_get_goods_list()
{
/*   $url    = 'http://yp.yueus.com/mobile_app/customer/goods_list.php';
    $param = array('user_id'=> 100008,'location_id'=>'101029001', 'return_query'=>'type_id=31&detail[46]=47&status=1&is_show=1&city=101029&location_id=101029001&user_id=0&prices_list=0', 'classify_id'=>'test');

    $post_data = array(
        'version'   => '3.0.0',
        'os_type'   => 'android',
        'ctime'     => time(),
        'app_name'  => 'poco_yuepai_android',
        'sign_code' => substr(md5('poco_'.json_encode($param).'_app'), 5, -8),
        'is_enc'    => 0,
        'param'     => $param,
    );
    $post_data = json_encode($post_data);
    $post_data = iconv('GBK', 'UTF-8', $post_data);

    print_r(mobile_app_curl($url, $post_data));*/

    $url    = 'http://yp.yueus.com/mobile_app/customer/goods_list.php';
    $param = array('user_id'=> 100008,'location_id'=>'101029001', 'return_query'=>'yueyue_static_cms_id%3D351', 'classify_id'=>'test');

    $post_data = array(
        'version'   => '3.0.0',
        'os_type'   => 'android',
        'ctime'     => time(),
        'app_name'  => 'poco_yuepai_android',
        'sign_code' => substr(md5('poco_'.json_encode($param).'_app'), 5, -8),
        'is_enc'    => 0,
        'param'     => $param,
    );
    $post_data = json_encode($post_data);
    $post_data = iconv('GBK', 'UTF-8', $post_data);

    print_r(mobile_app_curl($url, $post_data));
}

function mobile_app_curl($url, $post_data)
{

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER,  false);
    curl_setopt($ch, CURLOPT_COOKIE, "visitor_flag=1386571300; visitor_r=; cmt_hash=2746320925; _topbar_introduction=1; lastphoto_show_mode=list; session_id=67cd1e92439b03d60254f6afd6ada9c7; session_ip=112.94.240.51; session_ip_location=101029001; session_auth_hash=05d30ac6bf7bb8d1902df17a936ce6a4; g_session_id=3808f8022c9c8c16b8f5b6b7ddeb57c7; member_id=65849144; fav_userid=65849144; remember_userid=65849144; nickname=Mr.Ceclian; fav_username=Mr.Ceclian; activity_level=fans; pass_hash=f5544bdf101337398cbb8b07a3b05fe6");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, array('req' => $post_data));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $output = curl_exec($ch);

    curl_close($ch);

    header("Content-type: text/html; charset=utf-8");

    return $output;
}

?>