<?php
/** /* 
 * 峰会参加页,for 微信
 * 
 * author 星星
 * 
 * 2015-4-2
 */
 
 
include_once('/disk/data/htdocs232/poco/pai/topic/meeting/config/phone_meeting_config.php');//配置峰会对应场次的ID跟价钱
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
define("G_DB_GET_REALTIME_DATA",1);
$summit_meeting_obj   = POCO::singleton('pai_summit_meeting_class');
$pai_sms_obj = POCO::singleton('pai_sms_class');

//处理页面

$meeting_id = (int)$_INPUT['meeting_id'];
$name = trim($_INPUT['name']);
$phone = (int)$_INPUT['phone'];
$email = trim($_INPUT['email']);
$enroll_num = (int)$_INPUT['num'];
$active_word = (int)$_INPUT['active_word'];
//校验场次ID
if(!in_array($meeting_id,$meeting_array))
{
    $output_arr = array();
    $output_arr['code'] = 0;
    $output_arr['message'] = "data error";
    mobile_output($output_arr, false);
    exit();
}

//校验手机
if(empty($name))
{
    
    $output_arr = array();
    $output_arr['code'] = 0;
    $output_arr['message'] = "name error";
    mobile_output($output_arr, false);
    exit();

}


//校验手机
if(empty($phone))
{
    
    $output_arr = array();
    $output_arr['code'] = 0;
    $output_arr['message'] = "phone error";
    mobile_output($output_arr, false);
    exit();

}

//校验邮箱
$pattern = "/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i";
if ( !preg_match( $pattern, $email ) )
{
    
    $output_arr = array();
    $output_arr['code'] = 0;
    $output_arr['message'] = "email error";
    mobile_output($output_arr, false);
    exit();
}

//校验人数
if($enroll_num<1)
{
    $output_arr = array();
    $output_arr['code'] = 0;
    $output_arr['message'] = "enroll number error";
    mobile_output($output_arr, false);
    exit();
}

//校验验证码
if($active_word<1)
{
    $output_arr = array();
    $output_arr['code'] = 0;
    $output_arr['message'] = "vertify number not exist error";
    mobile_output($output_arr, false);
    exit();
}
else
{
    //校验验证码
    $verify_code = $active_word;
    $group_key = 'G_PAI_TOPIC_MEETING_VERIFY';
    $active_code_ret = $pai_sms_obj->check_verify_code($phone, $group_key, $verify_code, $user_id=0, $b_del_verify_code=true);
    if(!$active_code_ret)
    {
        $output_arr = array();
        $output_arr['code'] = 0;
        $output_arr['message'] = "vertify number error";
        mobile_output($output_arr, false);
        exit();
    }
    
}


//插入数据
$insert_data['meeting_id'] = $meeting_id;
$insert_data['name'] = $name;
$insert_data['email'] = $email;
$insert_data['phone'] = $phone;
$insert_data['enroll_num'] = $enroll_num;
$sum_price = ($meeting_price_array[$meeting_id])*$enroll_num;//总价格
$insert_data['sum_price'] = $sum_price;
$summit_meeting_id = $summit_meeting_obj->add_summit_meeting($insert_data);
if(empty($summit_meeting_id) || $summit_meeting_id<=0)
{
    $output_arr = array();
    $output_arr['code'] = 0;
    $output_arr['message'] = "enroll_id error";
    mobile_output($output_arr, false);
    exit();
}
//对应的subject名字，
$subject = $meeting_name_array[$meeting_id];//匹配配置的名
$amount = $sum_price; //支付金额


$channel_rid = $summit_meeting_id; //模块关联ID：报名ID、订单号
$subject = $subject; //商品名称、活动名称
$amount = $amount; //支付金额
$channel_return = 'http://www.yueus.com/topic/meeting/photo_meeting_middle_jump.php'; //支付成功，页面跳转返回
$channel_notify = 'http://www.yueus.com/topic/meeting/pay_notify.php'; //服务器异步通知支付状态
$openid = trim($_COOKIE['yueus_openid']);
if( strlen($openid)<1 )
{
    $output_arr = array();
    $output_arr['code'] = 0;
    $output_arr['message'] = 'need weixin authorize!';
    mobile_output($output_arr, false);
    exit();
}

$channel_module = 'meeting'; //模块类型：峰会
$third_code = 'tenpay_wxpub'; //支付方式：微信支付PUB
$payment_info = array(
    'third_code' => $third_code,
    'subject' => '约约-' . $subject,
    'amount' => $amount,
    'channel_return' => $channel_return,
    'channel_notify' => $channel_notify,
    'openid' => $openid,
);
$payment_obj = POCO::singleton('pai_payment_class');
$payment_ret = $payment_obj->submit_payment($channel_module, $channel_rid, $payment_info);
if( $payment_ret['error']===0 )
{
    $output_arr = array();
    $output_arr['code'] = 1;
    $output_arr['message'] = '';
    $output_arr['payment_no'] = $payment_ret['payment_no'];
    $output_arr['data'] = $payment_ret['request_data'];
    $output_arr['channel_return'] = $channel_return;
}
else
{
    $output_arr = array();
    $output_arr['code'] = 0;
    $output_arr['message'] = $payment_ret['message'];
}
mobile_output($output_arr, false);
exit();

//处理页面








?>