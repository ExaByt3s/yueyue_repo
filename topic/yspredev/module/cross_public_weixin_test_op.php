<?php
/** /*
 * 峰会参加页,for 微信
 *
 * author 星星
 *
 * 2015-4-2
 */


include_once('../config/topic_config.php');//配置峰会对应场次的ID跟价钱
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
define("G_DB_GET_REALTIME_DATA",1);
//约摄对象
$pai_yueshe_topic_obj   = POCO::singleton('pai_yueshe_topic_class');
$pai_sms_obj = POCO::singleton('pai_sms_class');

//处理页面

$topic_id = (int)$_INPUT['topic_id'];
$name = trim($_INPUT['name']);
$phone = (int)$_INPUT['phone'];
$address = trim($_INPUT['address']);
$enroll_num = (int)$_INPUT['enroll_num'];
//校验场次ID
if(!in_array($topic_id,$topic_array))
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

//校验地址
if ( empty($address) )
{

    $output_arr = array();
    $output_arr['code'] = 0;
    $output_arr['message'] = "address error";
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




//插入数据
$insert_data['topic_id'] = $topic_id;
$insert_data['name'] = $name;
$insert_data['address'] = $address;
$insert_data['phone'] = $phone;
$insert_data['enroll_num'] = $enroll_num;
$sum_price = ($topic_price_array[$topic_id])*$enroll_num;//总价格
$insert_data['sum_price'] = $sum_price;
$insert_data['user_id'] = $yue_login_id;
$enroll_tmp_id = $pai_yueshe_topic_obj->add_order($insert_data);
if(empty($enroll_tmp_id) || $enroll_tmp_id<=0)
{
    $output_arr = array();
    $output_arr['code'] = 0;
    $output_arr['message'] = "enroll_id error";
    mobile_output($output_arr, false);
    exit();
}
//对应的subject名字，
$subject = $topic_name_array[$topic_id];//匹配配置的名
$amount = $sum_price; //支付金额


$channel_rid = $enroll_tmp_id; //模块关联ID：报名ID、订单号
$subject = $subject; //商品名称、活动名称
$amount = $amount; //支付金额
$channel_return = '../enroll_success.php'; //支付成功，页面跳转返回
$channel_notify = 'http://www.yueus.com/topic/yspredev/pay_notify.php'; //服务器异步通知支付状态
$openid = trim($_COOKIE['yueus_openid']);
if( strlen($openid)<1 )
{
    $output_arr = array();
    $output_arr['code'] = 0;
    $output_arr['message'] = 'need weixin authorize!';
    mobile_output($output_arr, false);
    exit();
}

$channel_module = 'yueshe_topic_ys'; //模块类型：峰会
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
$output_arr['order_id'] = $enroll_tmp_id;//返回订单ID，用于避免跨号支付，多插入记录
mobile_output($output_arr, false);
exit();

//处理页面








?>