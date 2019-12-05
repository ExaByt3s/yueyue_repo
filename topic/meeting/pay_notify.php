<?php
/**
 * 服务器异步通知
 * 注意：一般在异步通知这里更新支付状态
 */

include_once('./config/phone_meeting_config.php');//配置峰会对应场次的ID跟价钱
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');


$payment_no = trim($_INPUT['payment_no']); //支付号

$summit_meeting_obj   = POCO::singleton('pai_summit_meeting_class');
$pai_sms_obj = POCO::singleton('pai_sms_class');
//获取支付信息
$payment_obj = POCO::singleton('pai_payment_class');
$payment_info = $payment_obj->get_payment_info($payment_no);

//文本日志 http://yp.yueus.com/logs/201504/01_meeting.txt
pai_log_class::add_log($payment_info, 'pay_notify', 'meeting');

if( empty($payment_info) )
{
    echo "payment_no error";
    exit();
}

$channel_module = trim($payment_info['channel_module']);
if( $channel_module!='meeting' )
{
    echo 'channel_module error';
    exit();
}

$payment_status = intval($payment_info['status']);
if( $payment_status!=8 )
{
    echo "status error";
    exit();
}

//TODO 这里更新报名的支付状态、支付号，需要先检查金额是否一致，同时也要查看状态是否未支付才更新
$channel_rid = intval($payment_info['channel_rid']); //报名ID、订单号
$third_total_fee = $payment_info['third_total_fee']*1; //实收金额

//检查报名ID对应的订单的状态是否已支付
$enroll_info = $summit_meeting_obj->get_summit_meeting_info($channel_rid);
if(empty($enroll_info))
{
    echo "enroll_info empty";
    exit();
}

//已支付
if( $enroll_info['pay_status']==1 )
{
    if( $payment_no==$enroll_info['payment_no'] )
    {
        echo "success";
        exit();
    }
    else
    {
        echo "repeat pay";
        exit();
    }
}

//检查金额
if($third_total_fee<=0 || $enroll_info['sum_price']!=$third_total_fee)
{
    echo "price error";
    exit();
}


$event_title = $meeting_name_array[$enroll_info['meeting_id']];//匹配活动的名

//更新记录
$update_data['pay_status'] = 1;
$update_data['payment_no'] = $payment_no;
$update_data['pay_time'] = time();
$ret = $summit_meeting_obj->update_summit_meeting($update_data,$channel_rid);
if($ret)
{
    //更新成功，报名成功发送短信
    $phone = $enroll_info['phone'];
    $group_key = 'G_PAI_TOPIC_MEETING_SUCCESS';
    $data = array(
        'event_title' => $event_title,
        'enroll_num'  => $enroll_info['enroll_num'],
    );
    $msg_ret = $pai_sms_obj->send_sms($phone, $group_key, $data);
    
    echo "success";
    exit();
}
else
{
    echo "update error";
    exit();
}
    

