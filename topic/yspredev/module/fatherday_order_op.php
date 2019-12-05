<?php
/**
 * 父亲节621专题插入订单
 * 星星
 *
 * 2015-6-3
 */

include_once('../config/topic_config.php');//配置专题ID，价钱跟名字
include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');

//取得应用操作对象实例
//使用form进行添加记录
define("G_DB_GET_REALTIME_DATA",1);
//约摄对象
$pai_yueshe_topic_obj   = POCO::singleton('pai_yueshe_topic_class');
//用户数据对象
$pai_user_obj = POCO::singleton('pai_user_class');

if(empty($yue_login_id))
{
    header("location:../father_day_login.php");
    exit("未登录");
}

$topic_id = (int)$_INPUT['topic_id'];
$weixin = trim($_INPUT['weixin']);
$enroll_num = (int)$_INPUT['enroll_num'];
$name = trim($_INPUT['name']);
$address = trim($_INPUT['address']);
$take_time_one = trim($_INPUT['take_time_one']);
$take_time_two = trim($_INPUT['take_time_two']);

//获取相关信息
$enroll_user_phone = $pai_user_obj->get_phone_by_user_id($yue_login_id);
$phone = $enroll_user_phone;


//校验场次ID
if(!in_array($topic_id,$topic_array))
{
    echo "<script>parent.alert('专题ID有误');window.location.href='../fatherday_enroll.php';</script>";//错误场次ID处理
    exit();
}

//校验微信
if(empty($weixin))
{
    echo "<script>parent.alert('微信有误');window.location.href='../fatherday_enroll.php';</script>";
    exit();
}

//校验报名数量
if(empty($enroll_num))
{
    echo "<script>parent.alert('报名数量有误');window.location.href='../fatherday_enroll.php';</script>";
    exit();
}

//校验报名数量
if(empty($name))
{
    echo "<script>parent.alert('名字有误');window.location.href='../fatherday_enroll.php';</script>";
    exit();
}

//校验报名地址
if(empty($address))
{
    echo "<script>parent.alert('地址有误');window.location.href='../fatherday_enroll.php';</script>";
    exit();
}

//校验手机号
if(empty($phone))
{
    echo "<script>parent.alert('手机号有误');window.location.href='../fatherday_enroll.php';</script>";
    exit();
}

//校验拍摄时间段1
if(empty($take_time_one) || empty($take_time_two))
{
    echo "<script>parent.alert('拍摄时间段有误');window.location.href='../fatherday_enroll.php';</script>";
    exit();
}



//插入数据

$insert_data['topic_id'] = $topic_id;
$insert_data['phone'] = $phone;
$insert_data['weixin'] = $weixin;
$insert_data['enroll_num'] = $enroll_num;
$insert_data['name'] = $name;
$insert_data['user_id'] = $yue_login_id;
//总价格
$sum_price = ($topic_price_array[$topic_id])*$enroll_num;
$insert_data['sum_price'] = $sum_price;

$insert_data['take_time'] = $take_time_one." ".$take_time_two;


$enroll_tmp_id = $pai_yueshe_topic_obj->add_order($insert_data);
if(empty($enroll_tmp_id) || $enroll_tmp_id<=0)
{
    echo "<script>parent.alert('报名出现错误');parent.reload();</script>";//暂时处理
    exit();
}



//进行支付操作
$third_code = 'alipay_wap';
$subject = $topic_name_array[$topic_id];//商品名称、活动名称

if(!empty($topic_price_array[$topic_id]))//对应专题匹配的单价钱
{
    $channel_rid = $enroll_tmp_id;
    //对应的subject名字，
    $subject = $subject;
    $amount = $sum_price; //支付金额
    $channel_notify = './pay_notify.php'; //服务器异步通知支付状态
    $channel_return = './enroll_success.php'; //服务器异步通知支付状态


    $channel_module = 'yueshe_topic_ys'; //模块类型：约摄专题
    $third_code = $third_code; //支付方式：支付宝
    $payment_info = array(
        'third_code' => $third_code,
        'subject' => '约约摄影-' . $subject,
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