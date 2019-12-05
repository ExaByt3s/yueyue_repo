<?php
/**
 * 峰会的微信支付页面
 * 
 * @author 星星
 * 
 * 
 * @copyright 2015-04-02
 */
 
include_once('/disk/data/htdocs232/poco/pai/topic/meeting/config/phone_meeting_config.php');//配置峰会对应场次的ID跟价钱
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
define("G_DB_GET_REALTIME_DATA",1);
$summit_meeting_obj   = POCO::singleton('pai_summit_meeting_class');
$pai_sms_obj = POCO::singleton('pai_sms_class');
//显示页面
// 注意 URL 一定要动态获取，不能 hardcode.
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$url = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

//微信授权
$openid = trim($_COOKIE['yueus_openid']);
$not_repeat = intval($_INPUT['not_repeat']);
if( strlen($openid)<1 && $not_repeat!=1 )
{
    if( strpos($url, '?')===false )
    {
        $url = $url . '?not_repeat=1';
    }
    else
    {
        $url = $url . '&not_repeat=1';
    }
    $weixin_pub_obj = POCO::singleton('pai_weixin_pub_class');
    $authorize_url = $weixin_pub_obj->auth_get_authorize_url(array('url'=>$url), 'snsapi_base');
    header("Location: {$authorize_url}");
    exit();
}
//显示页面

//处理页面
$op = trim($_INPUT['op']);

if( $op=='pay' )
{
    
    $meeting_id = (int)$_INPUT['meeting_id'];
    $phone = (int)$_INPUT['phone'];
    $email = trim($_INPUT['email']);
    $enroll_num = (int)$_INPUT['enroll_num'];
    $active_code = (int)$_INPUT['active_code'];
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
    if($active_code<1)
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
        $verify_code = $active_code;
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
}
//处理页面


//往下是显示
$app_id = 'wx25fbf6e62a52d11e'; //约约正式号
$weixin_helper_obj = POCO::singleton('pai_weixin_helper_class');
$wx_sign_package = $weixin_helper_obj->wx_get_js_api_sign_package_by_app_id($app_id, $url);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title></title>
<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script src="//code.jquery.com/jquery-1.11.2.min.js"></script>
<script>
  /*
   * 注意：
   * 1. 所有的JS接口只能在公众号绑定的域名下调用，公众号开发者需要先登录微信公众平台进入“公众号设置”的“功能设置”里填写“JS接口安全域名”。
   * 2. 如果发现在 Android 不能分享自定义内容，请到官网下载最新的包覆盖安装，Android 自定义分享接口需升级至 6.0.2.58 版本及以上。
   * 3. 常见问题及完整 JS-SDK 文档地址：http://mp.weixin.qq.com/wiki/7/aaa137b55fb2e0456bf8dd9148dd613f.html
   *
   * 开发中遇到问题详见文档“附录5-常见错误及解决办法”解决，如仍未能解决可通过以下渠道反馈：
   * 邮箱地址：weixin-open@qq.com
   * 邮件主题：【微信JS-SDK反馈】具体问题
   * 邮件内容说明：用简明的语言描述问题所在，并交代清楚遇到该问题的场景，可附上截屏图片，微信团队会尽快处理你的反馈。
   */
  wx.config({
    debug: true,
    appId: '<?php echo $wx_sign_package["appId"];?>',
    timestamp: <?php echo $wx_sign_package["timestamp"];?>,
    nonceStr: '<?php echo $wx_sign_package["nonceStr"];?>',
    signature: '<?php echo $wx_sign_package["signature"];?>',
    jsApiList: [
        'chooseWXPay'
    ]
  });
  wx.ready(function () {
    // 在这里调用 API
  });

  function callpay()
  {
  
    var phone = $("#phone").val();
    var email = $("#email").val();
    var enroll_num = $("#enroll_num").val();
    var meeting_id = $("#meeting_id").val();
    var ajax_url = "pay_photo_meeting_pay.php?op=pay"+"&phone="+phone+"&email="+email+"&enroll_num="+enroll_num+"&meeting_id="+meeting_id;//需要编码优化
    $.getJSON(ajax_url,function(output){
        if( output )
        {
            var result_data = output.result_data;
            var data = eval('('+result_data.data + ')');
            if( result_data && result_data.code==1 )
            {
                wx.chooseWXPay({
                    timestamp: data.timeStamp, // 支付签名时间戳，注意微信jssdk中的所有使用timestamp字段均为小写。但最新版的支付后台生成签名使用的timeStamp字段名需大写其中的S字符
                    nonceStr: data.nonceStr, // 支付签名随机串，不长于 32 位
                    package: data.package, // 统一支付接口返回的prepay_id参数值，提交格式如：prepay_id=***）
                    signType: data.signType, // 签名方式，默认为'SHA1'，使用新版支付需传入'MD5'
                    paySign: data.paySign, // 支付签名
                    success: function (res) {
                        //window.location.href=result_data.channel_return;
                        alert('chooseWXPay:' + res);//成功进行跳转或者其他操作
                    }
                });
            }
            else
            {
                alert("result_data error " + result_data.message);
            }
        }
        else
        {
            alert('output error');
        }
    });
  }
</script>
</head>
<body>
  <br /><br /><br /><br />
    <div align="center">
        <input type="text" value="" id="phone">手机号码</input>
        <input type="text" value="" id="email">邮箱</input>
        <input type="text" value="" id="meeting_id">场次ID</input>
        <input type="text" value="" id="enroll_num">人数</input>
        <button style="width:210px; height:30px; background-color:#FE6714; border:0px #FE6714 solid; cursor: pointer;  color:white;  font-size:16px;" type="button" onclick="callpay()" >贡献一下</button>
    </div>
</body>
</html>
