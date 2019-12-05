<?php
/** /* 
 * 峰会参加页,for pc跟普通WAP
 * 
 * author 星星
 * 
 * 2015-4-2
 */
 
include_once('../config/phone_meeting_config.php');//配置峰会对应场次的ID跟价钱
include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');
 //取得应用操作对象实例
define("G_DB_GET_REALTIME_DATA",1);
$summit_meeting_obj   = POCO::singleton('pai_summit_meeting_class');
$pai_sms_obj = POCO::singleton('pai_sms_class');


//获取相关数据
$meeting_id = (int)$_INPUT['meeting_id'];
$name = trim($_INPUT['name']);
$phone = (int)$_INPUT['phone'];
$email = trim($_INPUT['email']);
$enroll_num = (int)$_INPUT['num'];
$active_word = (int)$_INPUT['active_word'];//验证码



//校验场次ID
if(!in_array($meeting_id,$meeting_array))
{
    echo "<script>parent.alert('场次ID有误');window.location.href='http://www.yueus.com/topic/meeting/photo_meeting.php#nav4';</script>";//错误场次ID处理
    exit();
}


//校验手机
if(empty($name))
{
    
    echo "<script>parent.alert('名字有误');window.location.href='http://www.yueus.com/topic/meeting/photo_meeting.php#nav4';</script>";
    exit();

}

//校验手机
if(empty($phone))
{
    
    echo "<script>parent.alert('手机号码有误');window.location.href='http://www.yueus.com/topic/meeting/photo_meeting.php#nav4';</script>";
    exit();

}

//校验邮箱
$pattern = "/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i";
if ( !preg_match( $pattern, $email ) )
{
    
    echo "<script>parent.alert('邮箱不合法');window.location.href='http://www.yueus.com/topic/meeting/photo_meeting.php#nav4';</script>";
    exit();
}

//校验人数
if($enroll_num<1)
{
    echo "<script>parent.alert('报名人数不能为0');window.location.href='http://www.yueus.com/topic/meeting/photo_meeting.php#nav4';</script>";
    exit();
}


//校验验证码
if(empty($active_word))
{
    echo "<script>parent.alert('请填入验证码');window.location.href='http://www.yueus.com/topic/meeting/photo_meeting.php#nav4';</script>";
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
        echo "<script>parent.alert('验证码不正确');window.location.href='http://www.yueus.com/topic/meeting/photo_meeting.php#nav4';</script>";
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
    echo "<script>parent.alert('报名出现错误');</script>";
    exit();
}

//对应的subject名字，
$subject = $meeting_name_array[$meeting_id];//匹配配置的名

//跟距设备处理支付方式
/**
 * 判断客户端
 */
$__is_android = stripos($_SERVER['HTTP_USER_AGENT'], 'android') ? true : false;
$__is_iphone = stripos($_SERVER['HTTP_USER_AGENT'], 'iphone') ? true : false;

$__is_mobile = ($__is_android || $__is_iphone) ? true : false;

if($__is_mobile)
{
    $third_code = 'alipay_wap';
}
else
{
    $third_code = 'alipay';
}


//支付模块
if(!empty($meeting_price_array[$meeting_id]))//对应场次匹配的单价钱
{
    
    
    
    $channel_rid = $summit_meeting_id; //模块关联ID：报名ID、订单号
    $subject = $subject; //商品名称、活动名称
    $amount = $sum_price; //支付金额
    //$channel_return = 'http://www.yueus.com/topic/meeting/pay_return.php'; //支付成功，页面跳转返回
    $channel_notify = 'http://www.yueus.com/topic/meeting/pay_notify.php'; //服务器异步通知支付状态
    $channel_return = 'http://www.yueus.com/topic/meeting/photo_meeting_middle_jump.php'; //服务器异步通知支付状态
    

    $channel_module = 'meeting'; //模块类型：峰会
    $third_code = $third_code; //支付方式：支付宝
    $payment_info = array(
        'third_code' => $third_code,
        'subject' => '约约-' . $subject,
        'amount' => $amount,
        'channel_return' => $channel_return,
        'channel_notify' => $channel_notify,
    );
    $payment_obj = POCO::singleton('pai_payment_class');
    $payment_ret = $payment_obj->submit_payment($channel_module, $channel_rid, $payment_info);
    if( $payment_ret['error']===0 )
    {
        $request_data = trim($payment_ret['request_data']);
        echo '<script>parent.location.href="'.$request_data.'"</script>';
    }
    else
    {
        print_r($payment_ret);
    }
    
  
    
}

 ?>