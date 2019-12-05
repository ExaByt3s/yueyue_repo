<?php
include_once("protocol_common.inc.php");

switch($_GET['name'])
{
    case 'get_token':
        mobile_app_get_token();
        break;
        
    case 'get_fiend_total':
        mobile_app_get_fiend_total();
        break;
    
    case 'get_fiend_list':
        mobile_app_get_fiend_list();
        break;
    
    case 'get_find_index':
        mobile_app_get_find_index();
        break;
        
    case 'get_my':
        mobile_app_get_my();
        break;
        
    case 'get_hot_data':
        mobile_app_get_hot_data();
        break;
        
    case 'get_hot_list':
        mobile_app_get_hot_list();
        break;
        
    case 'check_chat_pass':
        mobile_app_check_chat_pass();
        break;
        
    case 'get_location':
        mobile_app_get_location();
        break;
        
    case 'get_search':
        mobile_app_get_search();
        break;
        
    case 'logout':
        mobile_app_logout();
        break;
        
    case 'search_tag':
        mobile_app_search_tag();
        break;
        
    case 'get_date_info_list':
        mobile_app_get_date_info_list();
        break;
        
    case 'get_search_push':
        mobile_app_get_search_push();
        break;
        
    case 'check_find_is_show':
        mobile_app_check_find_is_show();
        break;
        
    case 'get_hot_ad':
        mobile_app_get_hot_ad();
        break;

    case 'set_blacklist':
        mobile_app_set_blacklist();
        break;

    case 'get_blacklist':
        mobile_app_get_blacklist();
        break;

    case 'get_detail_info':
        mobile_app_get_detail_info();
        break;

	case 'get_comment_list':
        mobile_app_get_comment_list();
        break;

    case 'share_callback':
        mobile_app_share_callback();
        break;

    case 'get_user_follow':
        mobile_app_get_user_follow();
        break;

    case 'get_user_be_follow':
        mobile_app_get_user_be_follow();
        break;

    case 'set_user_follow':
        mobile_app_set_user_follow();
        break;

    case 'get_waipai_list':
        mobile_app_get_waipai_list();
        break;

	case 'app_get_user_info':
        mobile_app_get_user_info();
        break;

    case 'app_get_hot_top':
        mobile_app_get_hot_top();
        break;
        
    case 'sent_verify_code':
        mobile_app_sent_verify_code();
        break;
        
    case 'verify_phone_code':
        mobile_app_verify_phone_code();
        break;
        
    case 'login':
        mobile_app_login();
        break;
        
    case 'reg':
        mobile_app_reg();
        break;
        
    case 'reset':
        mobile_app_reset();
        break;

    case 'get_https_token':
        mobile_get_https_token();
        break;

    case 'get_mobile_ac':
        mobile_get_mobile_ac();
        break;

    case 'get_waipai_index':
        mobile_get_waipai_index();
        break;

    case 'code_to_yueyue':
        mobile_code_to_yueyue();
        break;

    case 'code_processing':
        mobile_code_processiong();
        break;


    case 'get_qrcode_list':
        mobile_get_qrcode_list();
        break;

    case 'get_app_index':
        mobile_get_app_index();
        break;

    case 'get_ware_list':
        mobile_get_ware_list();
        break;

    case 'get_seller_index':
        mobile_get_seller_index();
        break;

    case 'mmobile_get_token':
        mmobile_get_token();
        break;

    case 'mobile_check_code':
        mobile_check_code();
        break;

    case 'mobile_classify_index':
        mobile_get_classify_index();
        break;

    case 'mobile_screening_content':
        mobile_get_screening_content();
        break;

}

function mobile_get_screening_content()
{
    $url = 'http://yp.yueus.com/mobile_app/customer/screening_content.php';
    $param = array("type_id"=>'5');

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
    echo $post_data;
    $post_data = iconv('GBK', 'UTF-8', $post_data);

    //var_dump($post_data);
    print_r(mobile_app_curl($url, $post_data));
}


function mobile_get_classify_index()
{
    echo "111";
    $url    = 'http://yp.yueus.com/mobile_app/customer/classify_index.php';
    $param = array('user_id'=>100008,"location_id"=>101029001, "type_id"=>31);

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

    var_dump($post_data);
    print_r(mobile_app_curl($url, $post_data));
}



function mobile_check_code()
{
    $url    = 'http://yp.yueus.com/mobile_app/customer/code_processing.php';
    $check_url = urlencode('http://www.yueus.com/event/57352');

    $param = array('user_id'=> 100008, "code_data"=>$check_url);

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

function mobile_get_seller_index()
{
    $url    = 'http://yp.yueus.com/mobile_app/merchant/seller_index.php';
    $param = array('user_id'=> 100028,"location_id"=>"0,5", "query"=>"12268");

    $post_data = array(
        'version'   => '2.2.0',
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

function mobile_get_ware_list()
{
    $url    = 'http://yp.yueus.com/mobile_app/event/get_ware_list.php';
    $param = array('user_id'=> 100028,"location_id"=>"0,5", "query"=>"12268");

    $post_data = array(
        'version'   => '2.2.0',
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

function mobile_get_app_index()
{
    $url    = 'http://yp.yueus.com/mobile_app/event/get_app_index.php';
    $param = array('user_id'=> 100028,"location_id"=>"0,5");

    $post_data = array(
        'version'   => '2.0.0',
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

function mobile_get_qrcode_list()
{
    $url = 'http://yp.yueus.com/mobile_app/user/get_qrcode_list.php';
    $param = array('user_id'=> 100028,"limit"=>"0,5");
    $post_data = array(
        'version'   => '2.0.0',
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

function mobile_code_to_yueyue()
{
    $url = 'http://yp.yueus.com/mobile_app/event/code_to_yueyue.php';
    $param = array('user_id'=> 100008, 'code_data'=>'http://www.yueus.com/cameraman/100008');
    $post_data = array(
        'version'   => '1.0.1',
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

function mobile_code_processiong()
{
    $url = 'http://yp-wifi.yueus.com/mobile_app/merchant/code_processing.php';
    $param = array('user_id'=> 100026, 'code_data'=>'http%3a%2f%2fyp.yueus.com%2fmobile%2faction%2fcheck_qrcode.php%3fevent_id%3d23830%26enroll_id%3d23830%26code%3d976221%26hash%3d4b967fbbf4390b5b2f64');
    $post_data = array(
        'version'   => '2.0.0',
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

function mobile_get_waipai_index()
{
    $url = 'http://yp.yueus.com/mobile_app/event/get_waipai_index.php';
    $param = array('user_id'=> 100008);
    $post_data = array(
        'version'   => '1.8.0',
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

function mobile_get_mobile_ac()
{
    $url = 'http://yp.yueus.com/mobile_app/user/mobile_ac.php';
    $param = array('user_id'=> 100008, 'access_token'=>'962479246544630667', 'os'=>'ios', 'os_ver'=>'8.0.2', 'appver'=>'1.0.6', 'machinecode'=>'adf2a7a8fba82d23c64219b3e734b3e54ab44beb846befeb53323b08f4f50b3d' );
    $post_data = array(
        'version'   => '1.0.6',
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


function mobile_get_https_token()
{
    $url = 'https://ypays.yueus.com/app/token.php';

    $param = array('user_id'=> 100026, 'access_token'=>'2460563001294022660', 'refresh_token'=>'854315338165462611', 'app_name'=>'poco_yuepai_android' );
    $post_data = array(
        'version'   => '1.0.6',
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

function mobile_app_reset()
{
    $url = 'https://ypays.yueus.com/app/reset.php';

    $param = array('phone'=>"13960000000",'pwd'=>'6543211','verify_code'=>'999082');
    $post_data = array(
        'version'   => '1.0.6',
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

function mobile_app_reg()
{
    $url = 'https://ypays.yueus.com/app/reg.php';

    $param = array('phone'=>"13960000011",'pwd'=>'123456','verify_code'=>'078287','role'=>'model');
    $post_data = array(
        'version'   => '2.1.0',
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

function mobile_app_login()
{
    $url = 'https://ypays.yueus.com/app/login.php';

    $param = array('phone'=>"13560004676",'pwd'=>'123456');
    $post_data = array(
        'version'   => '1.0.6',
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

function mobile_app_verify_phone_code()
{
    $url = 'http://www.yueus.com/mobile_app/event/verify_phone_code.php';

    $param = array('phone'=>"13560004676",'verify_code'=>'862260');
    $post_data = array(
        'version'   => '1.0.6',
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


function mobile_app_sent_verify_code()
{
    $url = 'http://www.yueus.com/mobile_app/event/sent_verify_code.php';

    $param = array('phone'=>"13960000000",'reset'=>'1');
    $post_data = array(
        'version'   => '1.0.6',
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

function mobile_app_get_hot_top()
{
    $url = 'http://www.yueus.com/mobile_app/event/get_hot_top.php';

    $param = array('user_id'=>100008, 'location_id'=>101004001);
    $post_data = array(
        'version'   => '2.2.10',
        'os_type'   => 'iphone',
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

function mobile_app_get_user_info()
{
    $url = 'http://www.yueus.com/mobile_app/user/edit_user.php';

    $param = array('user_id'=>100000,'access_token'=>'713648480431504112');
    $post_data = array(
        'version'   => '1.0.6',
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

function mobile_app_get_waipai_list()
{
    $url = 'http://www.yueus.com/mobile_app/event/get_waipai_list.php';

    $param = array('user_id' =>100008, 'location_id' => '101029001', 'limit'=>'0,100');
    $post_data = array(
        'version'   => '2.1.10',
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

function mobile_app_set_user_follow()
{
    $url = 'http://www.yueus.com/mobile_app/user/set_user_follow.php';

    $param = array('user_id' =>100008, 'access_token' => '6369381645748666281', 'v_id'=>100000);
    $post_data = array(
        'version'   => '1.0.5',
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

    $url = 'http://www.yueus.com/mobile_app/user/set_user_follow.php';

    $param = array('user_id' =>100008, 'access_token' => '6369381645748666281', 'v_id'=>100000);
    $post_data = array(
        'version'   => '1.0.5',
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

    $url = 'http://www.yueus.com/mobile_app/user/set_user_follow.php';

    $param = array('user_id' =>100008, 'access_token' => '6369381645748666281');
    $post_data = array(
        'version'   => '1.0.5',
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

function mobile_app_get_user_be_follow()
{
    $url = 'http://www.yueus.com/mobile_app/user/get_user_by_follow.php';

    $param = array('user_id'=>100026,'v_id'=>103868, 'limit'=>'0,20');
    $post_data = array(
        'version'   => '1.0.6',
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

function mobile_app_get_user_follow()
{
    $url = 'http://www.yueus.com/mobile_app/user/get_user_follow.php';
    $param = array('user_id'=>100008,'v_id'=>105081, 'limit'=>'0,20');
    $post_data = array(
        'version'   => '2.2.0',
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


function mobile_app_share_callback()
{
    $url = 'http://www.yueus.com/mobile_app/share/share_callback.php';
    $param = array('user_id'=>100008,'soure_id'=>100000,'dmid'=>'1001','share_link'=>'XXXX','share_type'=>'weixin','share_callback_data'=>'XXXXX');
    $post_data = array(
        'version'   => '1.0.5',
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

function mobile_app_get_comment_list()
{
    $url = 'http://www.yueus.com/mobile_app/event/get_comment_list.php';
    $param = array('v_id' =>111947,'user_id'=>100000,'limit'=>'0,10');
    $post_data = array(
        'version'   => '1.0.5',
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


function mobile_app_get_detail_info()
{
    $url = 'http://www.yueus.com/mobile_app/event/get_detail_info.php';
    $param = array('v_id' =>100001,'user_id'=>100000);
    $post_data = array(
        'version'   => '1.0.6',
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

function mobile_app_get_blacklist()
{
    $url = 'http://www.yueus.com/mobile_app/user/get_blacklist.php';
    $param = array('user_id' =>100228, 'access_token' => '1038492394347874656');
    $post_data = array(
        'version'   => '1.0.4',
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

function mobile_app_set_blacklist()
{
    $url = 'http://www.yueus.com/mobile_app/user/set_blacklist.php';
    $param = array('user_id' =>100008, 'access_token' => '6862491745873438200', 'blacklist_id'=>'100001', 'set'=>'on');
    $post_data = array(
        'version'   => '1.0.4',
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

    $url = 'http://www.yueus.com/mobile_app/user/set_blacklist.php';
    $param = array('user_id' =>100008, 'access_token' => '6862491745873438200', 'blacklist_id'=>'100002', 'set'=>'on');
    $post_data = array(
        'version'   => '1.0.4',
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

    $url = 'http://www.yueus.com/mobile_app/user/set_blacklist.php';
    $param = array('user_id' =>100008, 'access_token' => '6862491745873438200', 'blacklist_id'=>'100003', 'set'=>'on');
    $post_data = array(
        'version'   => '1.0.4',
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

function mobile_app_get_hot_ad()
{
    $url = 'http://www.yueus.com/mobile_app/event/get_hot_ad.php';
    $param = array('location_id'=>101015001, 'user_id'=>100260);
    $post_data = array(
                'version'   => '2.2.0',
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
echo "<BR>--------------------------------------<BR>";     
    $url = 'http://www.yueus.com/mobile_app/event/get_hot_ad.php';
    $param = array('location_id'=>101029001, 'user_id'=>100008);
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

function mobile_app_check_find_is_show()
{
     $url = 'http://www.yueus.com/mobile_app/event/check_page_show.php';
    $param = array('location_id'=>101029001);
    $post_data = array(
                'version'   => '88.8.8',
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
    
     $url = 'http://www.yueus.com/mobile_app/event/check_page_show.php';
    $param = array('location_id'=>101029002);
    $post_data = array(
                'version'   => '88.8.8',
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


function mobile_app_get_search_push()
{
    $url = 'http://www.yueus.com/mobile_app/event/search_push.php';
    $param = array('user_id' =>100021, 'access_token' => '2658079747041762656');
    $post_data = array(
                'version'   => '88.8.8',
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

function mobile_app_get_date_info_list()
{
    $url = 'http://www.yueus.com/mobile_app/user/get_date_info_list.php';
    $param = array('user_id' =>100410, 'access_token' => '2734049247897915792');
    $post_data = array(
                'version'   => '88.8.8',
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

function mobile_app_search_tag()
{
    $url = 'http://www.yueus.com/mobile_app/event/search_tag.php';
//echo $url;
    $param = array('user_id'=>100008, 'page'=>1);
    $post_data = array(
                'version'   => '1.0.3',
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

function mobile_app_logout()
{
    $url = 'http://www.yueus.com/mobile_app/user/logout.php';
    $param = array('user_id' =>100008, 'access_token' => '6862491745873438200');
    $post_data = array(
                'version'   => '1.0.3',
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

function mobile_app_get_search()
{
    $query['tag'] = 'ŷ��';
    $url = 'http://www.yueus.com/mobile_app/event/search.php';
    $param = array('user_id' =>100008, 'query'=>$query, 'location_id'=>101001001);
    $post_data = array(
                'version'   => '2.1.0',
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

function mobile_app_get_location()
{
    $url = 'http://yp.yueus.com/mobile_app/event/get_location.php';
    $param = array(1);
    $post_data = array(
                'version'   => '1.0.3',
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

function mobile_app_get_find_index()
{
    echo '1.0.3';
    $url = 'http://yp.yueus.com/mobile_app/event/get_find_index.php';
    $param = array(1);
    $post_data = array(
                'version'   => '1.0.3',
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
    
    echo '1.0.4';
    $url = 'http://yp.yueus.com/mobile_app/event/get_find_index.php';
    $param = array(1);
    $post_data = array(
                'version'   => '1.0.4',
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
    
    echo '1.0.5';
    $url = 'http://yp.yueus.com/mobile_app/event/get_find_index.php';
    $param = array(1);
    $post_data = array(
                'version'   => '1.0.5',
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
    
    echo '1.0.6';
    $url = 'http://yp.yueus.com/mobile_app/event/get_find_index.php';
    $param = array(1);
    $post_data = array(
                'version'   => '88.8.8',
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

function mobile_app_check_chat_pass()
{
    $url = 'http://www.yueus.com/mobile_app/user/check_chat_pass.php';
    $param = array('user_id' =>100008, 'access_token' => '6862491745873438200', 'to_user_id'=>100000);
    $post_data = array(
                'version'   => '1.0.3',
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

function mobile_app_get_hot_list()
{
    $url = 'http://www.yueus.com/mobile_app/event/get_hot_list.php';
    $param = array('location_id' =>101029001, 'user_id' => 100009, 'query'=>'21');
    $post_data = array(
                'version'   => '88.8.8',
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

function mobile_app_get_hot_data()
{
    $url = 'http://www.yueus.com/mobile_app/event/get_hot_data.php';
    $param = array('location_id' =>101015001, 'user_id' => 100008);

    $post_data = array(
                'version'   => '1.8.0',
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

function mobile_app_get_my()
{
    $url = 'http://www.yueus.com/mobile_app/user/my.php';
    $param = array('user_id' =>100008, 'access_token' => '6410601150768019514');
    $post_data = array(
                'version'   => '2.2.0_beta4',
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

function mobile_app_get_fiend_list()
{
    $url = 'http://www.yueus.com/mobile_app/user/get_fiend_list.php';
    $param = array('user_id' =>105081, 'access_token' => '1493618404822765810', 'type' => 'follow');
    $post_data = array(
                'version'   => '2.1.0',
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
 
    /*$url = 'http://www.yueus.com/mobile_app/user/get_fiend_list.php';
    $param = array('user_id' =>100045, 'access_token' => '6862491745873438200', 'type' => 'follow');
    $post_data = array(
                'version'   => '1.0.4',
                'os_type'   => 'android',
                'ctime'     => time(),
                'app_name'  => 'poco_yuepai_android',
                'sign_code' => substr(md5('poco_'.json_encode($param).'_app'), 5, -8),
                'is_enc'    => 0,
                'param'     => $param,
              );
    $post_data = json_encode($post_data);
    $post_data = iconv('GBK', 'UTF-8', $post_data); 
    
    print_r(mobile_app_curl($url, $post_data));    */
}

function mobile_app_get_fiend_total()
{
    $url = 'http://www.yueus.com/mobile_app/user/get_fiend_total.php';
    $param = array('user_id' =>100008, 'access_token' => '5307476332215004416');
    $post_data = array(
                'version'   => '1.0.3',
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

function mobile_app_get_token()
{
    $url = 'http://yp.yueus.com/mobile_app/user/token.php';
    
    $param = array('user_id' =>100008,  'access_token' => '713648480431504112', 'app_name' => 'poco_yuepai_android');

    $post_data = array(
               'version'   => '1.0.3',
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
    //echo $url . "<BR>";
    //echo $post_data . "<BR>";
    $ch = curl_init();    
    // ����ѡ�����URL
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER,  false);
    curl_setopt($ch, CURLOPT_COOKIE, "visitor_flag=1386571300; visitor_r=; cmt_hash=2746320925; _topbar_introduction=1; lastphoto_show_mode=list; session_id=67cd1e92439b03d60254f6afd6ada9c7; session_ip=112.94.240.51; session_ip_location=101029001; session_auth_hash=05d30ac6bf7bb8d1902df17a936ce6a4; g_session_id=3808f8022c9c8c16b8f5b6b7ddeb57c7; member_id=65849144; fav_userid=65849144; remember_userid=65849144; nickname=Mr.Ceclian; fav_username=Mr.Ceclian; activity_level=fans; pass_hash=f5544bdf101337398cbb8b07a3b05fe6");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, array('req' => $post_data));
    
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
    
    // ִ�в���ȡHTML�ĵ�����
    $output = curl_exec($ch);
    
    // �ͷ�curl���?
    curl_close($ch);
    
    header("Content-type: text/html; charset=utf-8");
    
    // ��ӡ��õ����
    return $output; 
}

function mmobile_get_token()
{
    $url = 'http://yp.yueus.com/mobile_app/get_token.php';

    $param = array('user_id' =>100008, 'app_name' => 'poco_yuepai_android');


    $post_data = array(
        'version'   => '1.0.3',
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
?>