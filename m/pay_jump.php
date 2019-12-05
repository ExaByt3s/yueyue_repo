<?php
/**
 * ΢��֧��ҳ��
 *
 * @author Henry
 * @copyright 2015-04-02
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

// ע�� URL һ��Ҫ��̬��ȡ������ hardcode.
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$url = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

//΢����Ȩ
$openid = trim($_COOKIE['yueus_openid']);

if (!$openid) {
    $weixin_pub_obj = POCO::singleton('pai_weixin_pub_class');
    $authorize_url = $weixin_pub_obj->auth_get_authorize_url(array('url' => $url), 'snsapi_base');
    header("Location: {$authorize_url}");
    exit();
}

$app_id = 'wx25fbf6e62a52d11e'; //ԼԼ��ʽ��
$weixin_helper_obj = POCO::singleton('pai_weixin_helper_class');
$wx_sign_package = $weixin_helper_obj->wx_get_js_api_sign_package_by_app_id($app_id, $url);

// ����֧����
$payment_obj = POCO::singleton('pai_payment_class');
$amount = $_INPUT['amount']*1;
$third_code = trim($_INPUT['third_code']);
$redirect_url = trim(urldecode($_INPUT['redirect_url']));
$notify_url = G_PAI_APP_DOMAIN . '/mobile/action2.1.0/pay_recharge_notify.php';

$more_info = array(
    'channel_return' => $redirect_url,
    'channel_notify' => $notify_url,
    'openid' => $openid
);
$recharge_ret = $payment_obj->submit_recharge('consume', $yue_login_id, $amount, $third_code, 0, '', 0, $more_info);

$channel_return = $redirect_url;
if (!empty($recharge_ret['payment_no']) && strpos($channel_return, '#') !== false) {
    //����Լ�ĵ�JS�ṹ����
    $channel_return .= '/';
    $channel_return .= "payment_no_{$recharge_ret['payment_no']}";
}

if ($recharge_ret['error'] !== 0) {
    $output_arr['code'] = 0;
    $output_arr['message'] = $recharge_ret['message'];
    $output_arr['data'] = $recharge_ret['request_data'];
    $output_arr['payment_no'] = $recharge_ret['payment_no'];
}
else{
    $output_arr['code'] = 1;
    $output_arr['message'] = $recharge_ret['message'];
    $output_arr['data'] = $recharge_ret['request_data'];
    $output_arr['payment_no'] = $recharge_ret['payment_no'];
    $output_arr['channel_return'] = $channel_return;
    $output_arr['third_code'] = $third_code;
}




?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="gbk">
    <meta name="HandheldFriendly" content="true"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.3, user-scalable=0"/>
    <meta name="apple-mobile-web-app-capable" content="yes"/>
    <meta name="format-detection" content="telephone=no"/>
    <title>ԼԼ΢��֧��ҳ</title>
    <script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
    <script src="http://static.yueus.com/js/jquery-1.8.3.min.js"></script>
    <script>

        $(function () {
            /*
             * ע�⣺
             * 1. ���е�JS�ӿ�ֻ���ڹ��ںŰ󶨵������µ��ã����ںſ�������Ҫ�ȵ�¼΢�Ź���ƽ̨���롰���ں����á��ġ��������á�����д��JS�ӿڰ�ȫ��������
             * 2. ��������� Android ���ܷ����Զ������ݣ��뵽�����������µİ����ǰ�װ��Android �Զ������ӿ��������� 6.0.2.58 �汾�����ϡ�
             * 3. �������⼰���� JS-SDK �ĵ���ַ��http://mp.weixin.qq.com/wiki/7/aaa137b55fb2e0456bf8dd9148dd613f.html
             *
             * ������������������ĵ�����¼5-�������󼰽���취�����������δ�ܽ����ͨ����������������
             * �����ַ��weixin-open@qq.com
             * �ʼ����⣺��΢��JS-SDK��������������
             * �ʼ�����˵�����ü��������������������ڣ��������������������ĳ������ɸ��Ͻ���ͼƬ��΢���Ŷӻᾡ�촦����ķ�����
             */
            wx.config({
                debug    : false,
                appId    : '<?php echo $wx_sign_package["appId"];?>',
                timestamp: <?php echo $wx_sign_package["timestamp"];?>,
                nonceStr : '<?php echo $wx_sign_package["nonceStr"];?>',
                signature: '<?php echo $wx_sign_package["signature"];?>',
                jsApiList: [
                    'chooseWXPay'
                ]
            });
            wx.ready(function () {
                // ��������� API

                pay_action();

                $('#retry-btn').on('click', function () {
                    pay_action();
                });

            });


            var chooseWXPay = function (data, success, fail) {
                if (data.appId) {
                    delete data.appId;
                }

                data.timestamp = data.timeStamp;

                var callback =
                {
                    success    : function (res) {


                        var code = 0;

                        var err_log_src = 'http://www.yueus.com/mobile_app/log/save_log.php?err_level=1&url=' + encodeURIComponent(window.location.href);

                        var img = new Image();

                        if (res.errMsg == 'chooseWXPay:ok') {
                            //֧���ɹ�
                            code = 1;

                            $('#head-title').html('֧���ɹ�!����Ϊ����ת����...');

                            setTimeout(function () {
                                window.location.href = '<?php echo $output_arr["channel_return"];?>'
                            }, 1500);
                        }
                        else if (res.errMsg == 'chooseWXPay:cancel') {
                            //֧��������
                            code = 10;
                            $('#head-title').html('ȡ��֧��');

                            $('[data-role="retry"]').show();


                        }
                        else if (res.errMsg == 'chooseWXPay:fail') {
                            //֧��ʧ��
                            code = -3;

                            img.src = err_log_src + '&err_str=' + encodeURIComponent(res.errMsg);

                            console.log('url=' + window.location.href + '&err_str=' + res.errMsg);


                            $('#head-title').html("֧��ʧ��:" + res.err_msg);

                            $('[data-role="retry"]').show();

                        }
                        else {
                            img.src = err_log_src + '&err_str=' + encodeURIComponent(res.errMsg);

                            console.log('url=' + window.location.href + '&err_str=' + res.errMsg);

                            $('#head-title').html("֧��ʧ�ܣ��������������ύʧ�ܣ��������Ͻǹرղ����½���");

                            $('[data-role="retry"]').show();
                        }


                        if (typeof success == "function") {
                            success.call(this, {code: code});
                        }
                    },
                    fail       : function (res) {
                        console.log(res);

                        if (typeof fail == "function") {
                            fail.call(this, res);
                        }

                        $('#head-title').html("֧��ʧ��:" + res.err_msg);

                        $('[data-role="retry"]').show();

                    }, complete: function (res) {

                    debugger;
                },
                    cancel     : function (res) {

                        $('#head-title').html('ȡ��֧��');

                        $('[data-role="retry"]').show();
                    }
                };

                data = $.extend({}, data, callback);

                wx.chooseWXPay(data);

            };

            var pay_action = function () {
                var pay_json_data = <?php echo json_encode($output_arr['data']);?>;
                var code = '<?php echo $output_arr['code'];?>';
                var message = '<?php echo $output_arr['message'];?>';


                if (code > 0) {
                    pay_json_data = JSON.parse(pay_json_data);

                    chooseWXPay(pay_json_data);
                }
                else {
                    alert(message);
                }
            }
        });


    </script>
</head>
<body>
<style>
    .wrapper {
        padding: 10px;
        margin-top: 50px;
    }

    .ui-btn-lg {
        font-size: 18px;
        height: 44px;
        line-height: 42px;
        display: block;
        width: 100%;
        border-radius: 5px;
    }

    .ui-btn-confirm {
        background-color: #ff4978;
        color: #fff;
        background-clip: padding-box;
        padding: 15px 0;
    }

    .ui-btn, .ui-btn-lg, .ui-btn-s {
        height: 30px;
        line-height: 30px;
        padding: 0 13px;
        min-width: 55px;
        display: inline-block;
        position: relative;
        text-align: center;
        font-size: 15px;
        vertical-align: top;
        -webkit-box-sizing: border-box;
        background-clip: padding-box;
    }

    @media screen and (-webkit-min-device-pixel-ratio: 2)
        .ui-btn, .ui-btn-lg, .ui-btn-s, .ui-btn.disabled, .disabled.ui-btn-lg, .disabled.ui-btn-s, .ui-btn:disabled, .ui-btn-lg:disabled, .ui-btn-s:disabled {
            border: 0;
        }

        .ui-btn-lg {
            font-size: 18px;
            height: 44px;
            line-height: 42px;
            display: block;
            width: 100%;
            border-radius: 5px;
        }

        .ui-btn-confirm {
            background-color: #ff4978;
            color: #fff;
            background-clip: padding-box;
            padding: 15px 0;
        }

        .ui-btn, .ui-btn-lg, .ui-btn-s {
            height: 30px;
            line-height: 30px;
            padding: 0 13px;
            min-width: 55px;
            display: inline-block;
            position: relative;
            text-align: center;
            font-size: 15px;
            vertical-align: top;
            -webkit-box-sizing: border-box;
            background-clip: padding-box;
        }
</style>
<div class="wrapper">
    <h1 id="head-title">���ڵ���΢��֧��...</h1>

    <div class="ui-btn-wrap" data-role="retry" style="display: none;">
        <a class="ui-btn-lg ui-btn-confirm" id="retry-btn" style="height: 45px;line-height: 45px;">
            ����һ��
        </a>
    </div>
    <br/>

    <div class="ui-btn-wrap">
        <a class="ui-btn-lg ui-btn-confirm" style="height: 45px;line-height: 45px;" data-role="goback"
           onclick="javascript:history.back()">
            ������һҳ
        </a>
    </div>
</div>
</body>
</html>