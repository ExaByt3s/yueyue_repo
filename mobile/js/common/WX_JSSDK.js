/**
 * Created by nolest on 2015/1/15.
 *
 * 微信6.0.2 JSSDK
 */

define(function(require, exports, module) {
    var $ = require('$');
    var Backbone = require('backbone');
    var utility = require('./utility');
    var global_config = require('./global_config');
    var m_alert = require('../ui/m_alert/view');
    var page_control = require('../frame/page_control');

    //微信debug模式开关 true | false
    var WeiXinDebugMode = false;

    var WeiXinSDK =
    {
        version : 'default'
    };

    // Config_data格式 ：
    // Config_data =
    // {
    //      appId:appId，
    //      timestamp:timestamp，
    //      nonceStr:nonceStr，
    //      signature:signature
    // }
    WeiXinSDK.setConfig = function(Config_data)
    {

        var data = Config_data;

        wx.config({
            debug: WeiXinDebugMode,   // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
            appId: data.appId,        // 必填，公众号的唯一标识
            timestamp: data.timestamp,// 必填，生成签名的时间戳
            nonceStr: data.nonceStr,  // 必填，生成签名的随机串
            signature: data.signature,// 必填，签名，见附录1
            jsApiList: [
                'checkJsApi',
                'onMenuShareTimeline',
                'onMenuShareAppMessage',
                'onMenuShareQQ',
                'onMenuShareWeibo',
                'hideMenuItems',
                'showMenuItems',
                'hideAllNonBaseMenuItem',
                'showAllNonBaseMenuItem',
                'getNetworkType',
                'openLocation',
                'getLocation',
                'hideOptionMenu',
                'showOptionMenu',
                'closeWindow',
                'scanQRCode',
                'chooseWXPay',
                'openProductSpecificView'
            ] // 必填，需要使用的JS接口列表

        });
    };

    //ready回调
    WeiXinSDK.ready = function(callback)
    {
        wx.ready(function()
        {
            var Api = this;

            if(typeof callback == 'function')
            {
                callback.call(this,Api);
            }
        });

    };

    WeiXinSDK.ShareToFriend = function(Share_data)
    {
        var data = Share_data;

        console.log(Share_data);

        wx.onMenuShareAppMessage
        ({
            title: Share_data.title, // 分享标题
            desc: Share_data.desc, // 分享描述
            link: Share_data.link, // 分享链接
            imgUrl: Share_data.imgUrl, // 分享图标
            type: Share_data.type, // 分享类型,music、video或link，不填默认为link
            dataUrl: Share_data.dataUrl, // 如果type是music或video，则要提供数据链接，默认为空
            success: function () {
                // 用户确认分享后执行的回调函数
                if(Share_data.success && typeof Share_data.success === 'function')Share_data.success();
            },
            cancel: function () {
                // 用户取消分享后执行的回调函数
                if(Share_data.cancel && typeof Share_data.cancel === 'function')Share_data.cancel();
            }
        })
    };

    WeiXinSDK.ShareTimeLine = function(Share_data)
    {
        var data = Share_data;

        wx.onMenuShareTimeline({
            title: Share_data.desc, // 分享标题
            link: Share_data.link, // 分享链接
            imgUrl: Share_data.imgUrl, // 分享图标
            success: function () {
                // 用户确认分享后执行的回调函数
                if(Share_data.success && typeof Share_data.success === 'function')Share_data.success();
            },
            cancel: function () {
                // 用户取消分享后执行的回调函数
                if(Share_data.cancel && typeof Share_data.cancel === 'function')Share_data.cancel();
            }
        });
    };

    WeiXinSDK.ShareQQ = function(Share_data)
    {
        var data = Share_data;

        wx.onMenuShareQQ({
            title: Share_data.title, // 分享标题
            desc: Share_data.desc, // 分享描述
            link: Share_data.link, // 分享链接
            imgUrl: Share_data.imgUrl, // 分享图标
            success: function () {
                // 用户确认分享后执行的回调函数
                if(Share_data.success && typeof Share_data.success === 'function')Share_data.success();
            },
            cancel: function () {
                // 用户取消分享后执行的回调函数
                if(Share_data.cancel && typeof Share_data.cancel === 'function')Share_data.cancel();
            }
        });
    };

    WeiXinSDK.isWeiXin = function ()
    {
        return /MicroMessenger/i.test(navigator.userAgent);
    };

    WeiXinSDK.hideOptionMenu = function()
    {
        wx.hideOptionMenu();
    };

    WeiXinSDK.showOptionMenu = function()
    {
        wx.showOptionMenu();

    };

    WeiXinSDK.getLocation = function(options)
    {
        wx.getLocation({
            success: function (res) {
                //alert("success" + JSON.stringify(res))
                if(options.success && typeof options.success === 'function')options.success(res);
            },
            cancel: function (res) {
                //alert("cancel" + JSON.stringify(res))
                if(options.cancel && typeof options.cancel === 'function')options.cancel(res);
            },
            fail : function(res){
                //alert("fail" + JSON.stringify(res))
                if(options.fail && typeof options.fail === 'function')options.fail(res);
            }
        });
    };

    WeiXinSDK.chooseWXPay = function(data,success,fail)
    {
        if(data.appId)
        {
            delete data.appId;
        }

        data.timestamp = data.timeStamp;

        var callback =
        {
            success: function(res)
            {
                debugger;
                console.log(res);

                var code = 0;

                var err_log_src = 'http://www.yueus.com/mobile_app/log/save_log.php?err_level=1&url='+encodeURIComponent(window.location.href);

                var img = new Image();

                if( res.errMsg == 'chooseWXPay:ok' )
                {
                    //支付成功
                    code = 1;
                }
                else if( res.errMsg == 'chooseWXPay:cancel' )
                {
                    //支付过程中
                    code = 10;
                }
                else if( res.errMsg == 'chooseWXPay:fail' )
                {
                    //支付失败
                    code = -3;

                    img.src = err_log_src+'&err_str='+encodeURIComponent(res.errMsg);

                    console.log('url='+window.location.href+'&err_str='+res.errMsg);

                    alert("支付失败:"+res.err_msg);

                }
                else
                {
                    img.src = err_log_src+'&err_str='+encodeURIComponent(res.errMsg);

                    console.log('url='+window.location.href+'&err_str='+res.errMsg);

                    alert("支付失败，由于网络问题提交失败，请点击左上角关闭并重新进入");
                }


                if (typeof success == "function")
                {
                    success.call(this, {code : code});
                }
            },
            fail: function(res)
            {
                console.log(res);



                if (typeof fail == "function")
                {
                    fail.call(this, res);
                }
            },complete : function(res)
            {

                debugger;
            },
            cancel : function(res)
            {

                debugger;
            }
        };

        data = $.extend({},data,callback);

        wx.chooseWXPay(data);

    };


    module.exports = WeiXinSDK;
});
