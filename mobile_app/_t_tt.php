<?php
include_once("protocol_common.inc.php");

switch($_GET['name'])
{
    case 'get_question_list':
        mobile_app_get_question_list();
        break;
        
    case 'get_service_list':
        mobile_app_get_service_list();
        break;
        
    case 'get_profile_faq_list':
        mobile_app_get_profile_faq_list();
        break;
        
    case 'hire_act':
        mobile_app_hire_act();
        break;
        
    case 'get_review_list':
        mobile_app_get_review_list();
        break;
        
    case 'add_review_page':
        mobile_app_add_review_page();
        break;
        
    case 'get_quote_info':
        mobile_app_get_quote_info();
        break;
        
    case 'submit_message':
        mobile_app_submit_message();
        break;
        
    case 'submit_review':
        mobile_app_submit_review();
        break;
        
    case 'get_qrcode_list':
        mobile_app_get_qrcode_list();
        break;
        
    case 'sent_verify_code':
        mobile_sent_verify_code();
        break;

    case 'get_chat_user_info':
        mobile_get_chat_user_info();
        break;

    case 'processing':
        processing();
        break;
}


function processing()
{
    $url = 'http://yp.yueus.com/mobile_app/customer/code_processing.php';

    $param = array('code_data'=>'http://yp.yueus.com/mobile/action/check_qrcode.php?event_id=59418&enroll_id=809493&code=409693&hash=4b883054bc7e4d0e1f3d',"user_id"=>"100021");

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

function mobile_get_chat_user_info()
{
    $url = 'http://yp.yueus.com/mobile_app/user/get_chat_user_info_test.php';

    $param = array('type'=>'seller',"user_id"=>"100000");

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

function mobile_sent_verify_code()
{
    $url = 'http://yp.yueus.com/mobile_app/merchant/sent_verify_code.php';
    
    $param = array('phone'=>13560004676,"reset"=>"1");

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

function mobile_app_get_qrcode_list()
{
    $url = 'http://yp.yueus.com/mobile_app/customer/get_qrcode_list.php';
    
    $param = array('user_id'=>100045,"limit"=>"0,10");

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


function mobile_app_submit_review()
{
    $url = 'http://yp.yueus.com/mobile_app/task/add_review_act.php';
    
    $param = array('user_id'=>100027,"quote_id"=>169,"content"=>"aaaa","rank"=>5);

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

function mobile_app_submit_message()
{
    $url = 'http://yp.yueus.com/mobile_app/task/submit_message.php';
    
    $param = array('user_id'=>100000,"quote_id"=>1,"content"=>"aaaa");

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

function mobile_app_get_quote_info()
{
    $url = 'http://yp.yueus.com/mobile_app/task/get_quote_info.php';
    
    $param = array('user_id'=>109650,"quote_id"=>184);

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

function mobile_app_add_review_page()
{
    $url = 'http://yp.yueus.com/mobile_app/task/add_review_page.php';
    
    $param = array('user_id'=>100000,"quote_id"=>1);

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

function mobile_app_get_review_list()
{
    $url = 'http://yp.yueus.com/mobile_app/task/get_review_list.php';
    
    $param = array('user_id'=>101529,"seller_user_id"=>100000,"limit"=>"33,10");

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


function mobile_app_hire_act()
{
    $url = 'http://yp.yueus.com/mobile_app/task/hire_act.php';
    
    $param = array('user_id'=>100000,"quote_id"=>1);

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

function mobile_app_get_profile_faq_list()
{
    $url = 'http://yp.yueus.com/mobile_app/task/get_profile_faq_list.php';
    
    $param = array('user_id'=>100000,"profile_id"=>1);

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

function mobile_app_get_service_list()
{
    $url = 'http://yp.yueus.com/mobile_app/task/get_service_list.php';
    
    $param = array('user_id'=>100000);

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


function mobile_app_get_question_list()
{
    $url = 'http://yp.yueus.com/mobile_app/task/get_question_list.php';
    
    $param = array('user_id'=>100000);

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

function mobile_app_curl($url, $post_data)
{
    //echo $url . "<BR>";
    //echo $post_data . "<BR>";
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