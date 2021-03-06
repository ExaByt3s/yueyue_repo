/**
 * Created by hdw on 2014/12/21.
 * 微信接口
 */

define(function(require, exports, module) {
    var $ = require('$');
    var Backbone = require('backbone');
    var utility = require('./utility');
    var global_config = require('./global_config');
    var m_alert = require('../ui/m_alert/view');
    var page_control = require('../frame/page_control');

    /**
     * 定义WeixinApi
     */
    var WeixinApi = {
        version:3.2
    };

    // 将WeixinApi暴露到window下：全局可使用，对旧版本向下兼容
    window.WeixinApi = WeixinApi;

    /////////////////////////// CommonJS /////////////////////////////////
    if (typeof define === 'function' && (define.amd || define.cmd)) {
        if (define.amd) {
            // AMD 规范，for：requirejs
            define(function () {
                return WeixinApi;
            });
        } else if (define.cmd) {
            // CMD 规范，for：seajs
            define(function (require, exports, module) {
                module.exports = WeixinApi;
            });
        }
    }

    /**
     * 对象简单继承，后面的覆盖前面的，继承深度：deep=1
     * @private
     */
    var _extend = function () {
        var result = {}, obj, k;
        for (var i = 0, len = arguments.length; i < len; i++) {
            obj = arguments[i];
            if (typeof obj === 'object') {
                for (k in obj) {
                    result[k] = obj[k];
                }
            }
        }
        return result;
    };

    /**
     * 内部私有方法，分享用
     * @private
     */
    var _share = function (cmd, data, callbacks) {
        callbacks = callbacks || {};

        // 分享过程中的一些回调
        var progress = function (resp) {
            switch (true) {
                // 用户取消
                case /\:cancel$/i.test(resp.err_msg) :
                    callbacks.cancel && callbacks.cancel(resp);
                    break;
                // 发送成功
                case /\:(confirm|ok)$/i.test(resp.err_msg):
                    callbacks.confirm && callbacks.confirm(resp);
                    break;
                // fail　发送失败
                case /\:fail$/i.test(resp.err_msg) :
                default:
                    callbacks.fail && callbacks.fail(resp);
                    break;
            }
            // 无论成功失败都会执行的回调
            callbacks.all && callbacks.all(resp);
        };

        // 执行分享，并处理结果
        var handler = function (theData, argv) {
            // 新的分享接口，单独处理
            if (cmd.menu === 'menu:general:share') {
                // 如果是收藏操作，并且在wxCallbacks中配置了favorite为false，则不执行回调
                if (argv.shareTo == 'favorite' || argv.scene == 'favorite') {
                    if (callbacks.favorite === false) {
                        return argv.generalShare(theData, function () {
                        });
                    }
                }

                argv.generalShare(theData, progress);
            } else {
                WeixinJSBridge.invoke(cmd.action, theData, progress);
            }
        };

        // 监听分享操作
        WeixinJSBridge.on(cmd.menu, function (argv)
        {
            if (callbacks.async && callbacks.ready)
            {
                WeixinApi["_wx_loadedCb_"] = callbacks.dataLoaded || new Function();
                if (WeixinApi["_wx_loadedCb_"].toString().indexOf("_wx_loadedCb_") > 0)
                {
                    WeixinApi["_wx_loadedCb_"] = new Function();
                }
                callbacks.dataLoaded = function (newData)
                {
                    // 这种情况下，数据仍需加工
                    var theData = _extend(data, newData);
                    if (cmd.menu == 'menu:share:timeline' ||
                        (cmd.menu == 'menu:general:share' && argv.shareTo == 'timeline')) {
                        theData = {
                            "appid":theData.appId ? theData.appId : '',
                            "img_url":theData.imgUrl,
                            "link":theData.link,
                            "desc":theData.title,
                            "title":theData.desc,
                            "img_width":"640",
                            "img_height":"640"
                        };
                    }
                    WeixinApi["_wx_loadedCb_"](theData);
                    handler(theData, argv);
                };
                // 然后就绪
                if (!(argv && (argv.shareTo == 'favorite' || argv.scene == 'favorite') && callbacks.favorite === false)) {
                    callbacks.ready && callbacks.ready(argv, data);
                }
            } else {
                // 就绪状态
                if (!(argv && (argv.shareTo == 'favorite' || argv.scene == 'favorite') && callbacks.favorite === false)) {
                    callbacks.ready && callbacks.ready(argv, data);
                }
                handler(data, argv);
            }
        });
    };

    /**
     * 分享到微信朋友圈
     * @param       {Object}    data       待分享的信息
     * @p-config    {String}    appId      公众平台的appId（服务号可用）
     * @p-config    {String}    imgUrl     图片地址
     * @p-config    {String}    link       链接地址
     * @p-config    {String}    desc       描述
     * @p-config    {String}    title      分享的标题
     *
     * @param       {Object}    callbacks  相关回调方法
     * @p-config    {Boolean}   async                   ready方法是否需要异步执行，默认false
     * @p-config    {Function}  ready(argv, data)       就绪状态
     * @p-config    {Function}  dataLoaded(data)        数据加载完成后调用，async为true时有用，也可以为空
     * @p-config    {Function}  cancel(resp)    取消
     * @p-config    {Function}  fail(resp)      失败
     * @p-config    {Function}  confirm(resp)   成功
     * @p-config    {Function}  all(resp)       无论成功失败都会执行的回调
     */
    WeixinApi.shareToTimeline = function (data, callbacks)
    {
        _share({
            menu:'menu:share:timeline',
            action:'shareTimeline'
        }, {
            "appid":data.appId ? data.appId : '',
            "img_url":data.imgUrl,
            "link":data.link,
            "desc":data.title,
            "title":data.desc,
            "img_width":"640",
            "img_height":"640"
        }, callbacks);
    };

    /**
     * 发送给微信上的好友
     * @param       {Object}    data       待分享的信息
     * @p-config    {String}    appId      公众平台的appId（服务号可用）
     * @p-config    {String}    imgUrl     图片地址
     * @p-config    {String}    link       链接地址
     * @p-config    {String}    desc       描述
     * @p-config    {String}    title      分享的标题
     *
     * @param       {Object}    callbacks  相关回调方法
     * @p-config    {Boolean}   async                   ready方法是否需要异步执行，默认false
     * @p-config    {Function}  ready(argv, data)       就绪状态
     * @p-config    {Function}  dataLoaded(data)        数据加载完成后调用，async为true时有用，也可以为空
     * @p-config    {Function}  cancel(resp)    取消
     * @p-config    {Function}  fail(resp)      失败
     * @p-config    {Function}  confirm(resp)   成功
     * @p-config    {Function}  all(resp)       无论成功失败都会执行的回调
     */
    WeixinApi.shareToFriend = function (data, callbacks) {
        _share({
            menu:'menu:share:appmessage',
            action:'sendAppMessage'
        }, {
            "appid":data.appId ? data.appId : '',
            "img_url":data.imgUrl,
            "link":data.link,
            "desc":data.desc,
            "title":data.title,
            "img_width":"640",
            "img_height":"640"
        }, callbacks);
    };


    /**
     * 分享到腾讯微博
     * @param       {Object}    data       待分享的信息
     * @p-config    {String}    link       链接地址
     * @p-config    {String}    desc       描述
     *
     * @param       {Object}    callbacks  相关回调方法
     * @p-config    {Boolean}   async                   ready方法是否需要异步执行，默认false
     * @p-config    {Function}  ready(argv, data)       就绪状态
     * @p-config    {Function}  dataLoaded(data)        数据加载完成后调用，async为true时有用，也可以为空
     * @p-config    {Function}  cancel(resp)    取消
     * @p-config    {Function}  fail(resp)      失败
     * @p-config    {Function}  confirm(resp)   成功
     * @p-config    {Function}  all(resp)       无论成功失败都会执行的回调
     */
    WeixinApi.shareToWeibo = function (data, callbacks) {
        _share({
            menu:'menu:share:weibo',
            action:'shareWeibo'
        }, {
            "content":data.desc,
            "url":data.link
        }, callbacks);
    };

    /**
     * 新的分享接口
     * @param       {Object}    data       待分享的信息
     * @p-config    {String}    appId      公众平台的appId（服务号可用）
     * @p-config    {String}    imgUrl     图片地址
     * @p-config    {String}    link       链接地址
     * @p-config    {String}    desc       描述
     * @p-config    {String}    title      分享的标题
     *
     * @param       {Object}    callbacks  相关回调方法
     * @p-config    {Boolean}   async                   ready方法是否需要异步执行，默认false
     * @p-config    {Function}  ready(argv, data)       就绪状态
     * @p-config    {Function}  dataLoaded(data)        数据加载完成后调用，async为true时有用，也可以为空
     * @p-config    {Function}  cancel(resp)    取消
     * @p-config    {Function}  fail(resp)      失败
     * @p-config    {Function}  confirm(resp)   成功
     * @p-config    {Function}  all(resp)       无论成功失败都会执行的回调
     */
    WeixinApi.generalShare = function (data, callbacks) {
        _share({
            menu:'menu:general:share'
        }, {
            "appid":data.appId ? data.appId : '',
            "img_url":data.imgUrl,
            "link":data.link,
            "desc":data.desc,
            "title":data.title,
            "img_width":"640",
            "img_height":"640"
        }, callbacks);
    };

    /**
     * 调起微信Native的图片播放组件。
     * 这里必须对参数进行强检测，如果参数不合法，直接会导致微信客户端crash
     *
     * @param {String} curSrc 当前播放的图片地址
     * @param {Array} srcList 图片地址列表
     */
    WeixinApi.imagePreview = function (curSrc, srcList) {		

        if (!curSrc || !srcList || srcList.length == 0) {
            return;
        }
        WeixinJSBridge.invoke('imagePreview', {
            'current':curSrc,
            'urls':srcList
        },function()
		{
				alert('error')
		});
    };

    /**
     * 显示网页右上角的按钮
     */
    WeixinApi.showOptionMenu = function () {
        WeixinJSBridge.call('showOptionMenu');
    };


    /**
     * 隐藏网页右上角的按钮
     */
    WeixinApi.hideOptionMenu = function () {
        WeixinJSBridge.call('hideOptionMenu');
    };

    /**
     * 显示底部工具栏
     */
    WeixinApi.showToolbar = function () {
        WeixinJSBridge.call('showToolbar');
    };

    /**
     * 隐藏底部工具栏
     */
    WeixinApi.hideToolbar = function () {
        WeixinJSBridge.call('hideToolbar');
    };

    /**
     * 返回如下几种类型：
     *
     * network_type:wifi     wifi网络
     * network_type:edge     非wifi,包含3G/2G
     * network_type:fail     网络断开连接
     * network_type:wwan     2g或者3g
     *
     * 使用方法：
     * WeixinApi.getNetworkType(function(networkType){
     *
     * });
     *
     * @param callback
     */
    WeixinApi.getNetworkType = function (callback) {
        if (callback && typeof callback == 'function') {
            WeixinJSBridge.invoke('getNetworkType', {}, function (e) {
                // 在这里拿到e.err_msg，这里面就包含了所有的网络类型
                callback(e.err_msg);
            });
        }
    };

    /**
     * 关闭当前微信公众平台页面
     * @param       {Object}    callbacks       回调方法
     * @p-config    {Function}  fail(resp)      失败
     * @p-config    {Function}  success(resp)   成功
     */
    WeixinApi.closeWindow = function (callbacks) {
        callbacks = callbacks || {};
        WeixinJSBridge.invoke("closeWindow", {}, function (resp) {
            switch (resp.err_msg) {
                // 关闭成功
                case 'close_window:ok':
                    callbacks.success && callbacks.success(resp);
                    break;

                // 关闭失败
                default :
                    callbacks.fail && callbacks.fail(resp);
                    break;
            }
        });
    };

    /**
     * 当页面加载完毕后执行，使用方法：
     * WeixinApi.ready(function(Api){
     *     // 从这里只用Api即是WeixinApi
     * });
     * @param readyCallback
     */
    WeixinApi.ready = function (readyCallback) {
        if (readyCallback && typeof readyCallback == 'function') {
            var Api = this;
            var wxReadyFunc = function () {
                readyCallback(Api);
            };
            if (typeof window.WeixinJSBridge == "undefined")
            {
                if (document.addEventListener)
                {
                    document.addEventListener('WeixinJSBridgeReady', wxReadyFunc, false);
                } else if (document.attachEvent)
                {
                    document.attachEvent('WeixinJSBridgeReady', wxReadyFunc);
                    document.attachEvent('onWeixinJSBridgeReady', wxReadyFunc);
                }
            } else
            {
                wxReadyFunc();
            }
        }
    };

    /**
     * 判断当前网页是否在微信内置浏览器中打开
     */
    WeixinApi.isWexXin = function () {
        return /MicroMessenger/i.test(navigator.userAgent);
    };

    /*
     * 打开扫描二维码
     * @param       {Object}    callbacks       回调方法
     * @p-config    {Function}  fail(resp)      失败
     * @p-config    {Function}  success(resp)   成功
     */
    WeixinApi.scanQRCode = function (callbacks) {
        callbacks = callbacks || {};
        WeixinJSBridge.invoke("scanQRCode", {}, function (resp) {
            switch (resp.err_msg) {
                // 打开扫描器成功
                case 'scan_qrcode:ok':
                    callbacks.success && callbacks.success(resp);
                    break;

                // 打开扫描器失败
                default :
                    callbacks.fail && callbacks.fail(resp);
                    break;
            }
        });
    };

    /**
     * 检测应用程序是否已安装
     *         by mingcheng 2014-10-17
     *
     * @param       {Object}    data               应用程序的信息
     * @p-config    {String}    packageUrl      应用注册的自定义前缀，如 xxx:// 就取 xxx
     * @p-config    {String}    packageName        应用的包名
     *
     * @param       {Object}    callbacks       相关回调方法
     * @p-config    {Function}  fail(resp)      失败
     * @p-config    {Function}  success(resp)   成功，如果有对应的版本信息，则写入到 resp.version 中
     * @p-config    {Function}  all(resp)       无论成功失败都会执行的回调
     */
    WeixinApi.getInstallState = function (data, callbacks) {
        callbacks = callbacks || {};

        WeixinJSBridge.invoke("getInstallState", {
            "packageUrl":data.packageUrl || "",
            "packageName":data.packageName || ""
        }, function (resp) {
            var msg = resp.err_msg, match = msg.match(/state:yes_?(.*)$/);
            if (match) {
                resp.version = match[1] || "";
                callbacks.success && callbacks.success(resp);
            } else {
                callbacks.fail && callbacks.fail(resp);
            }

            callbacks.all && callbacks.all(resp);
        });
    };

    /**
     * 从网页里直接调起微信地图
     *
     * @param       {Object}    data             打开地图所需要的数据
     * @p-config    {String}    latitude         纬度
     * @p-config    {String}    longitude        经度
     * @p-config    {String}    name             POI名称
     * @p-config    {String}    adress           地址
     * @p-config    {String}    scale            缩放
     * @p-config    {String}    infoUrl          查看位置界面底部的超链接
     *
     * @param       {Object}    callbacks       相关回调方法
     * @p-config    {Function}  fail(resp)      失败
     * @p-config    {Function}  success(resp)   成功
     * @p-config    {Function}  all(resp)       无论成功失败都会执行的回调
     */
    WeixinApi.openLocation = function (data, callbacks) {
        callbacks = callbacks || {};
        WeixinJSBridge.invoke('openLocation', {
            "latitude":data.latitude,
            "longitude":data.longitude,
            "name":data.name,
            "address":data.address,
            "scale":data.scale || 14,
            "infoUrl":data.infoUrl || ''
        }, function (resp) {
            if (resp.err_msg === "open_location:ok") {
                callbacks.success && callbacks.success(resp);
            } else {
                callbacks.fail && callbacks.fail(resp);
            }
            callbacks.all && callbacks.all(resp);
        });
    };

    /**
     * 发送邮件
     * @param       {Object}  data      邮件初始内容
     * @p-config    {String}  subject   邮件标题
     * @p-config    {String}  body      邮件正文
     *
     * @param       {Object}    callbacks       相关回调方法
     * @p-config    {Function}  fail(resp)      失败
     * @p-config    {Function}  success(resp)   成功
     * @p-config    {Function}  all(resp)       无论成功失败都会执行的回调
     */
    WeixinApi.sendEmail = function (data, callbacks) {
        callbacks = callbacks || {};
        WeixinJSBridge.invoke("sendEmail", {
            "title":data.subject,
            "content":data.body
        }, function (resp) {
            if (resp.err_msg === 'send_email:sent') {
                callbacks.success && callbacks.success(resp);
            } else {
                callbacks.fail && callbacks.fail(resp);
            }
            callbacks.all && callbacks.all(resp);
        })
    };

    /**
     * 开启Api的debug模式，比如出了个什么错误，能alert告诉你，而不是一直很苦逼的在想哪儿出问题了
     * @param    {Function}  callback(error) 出错后的回调，默认是alert
     */
    WeixinApi.enableDebugMode = function (callback) {
        /**
         * @param {String}  errorMessage   错误信息
         * @param {String}  scriptURI      出错的文件
         * @param {Long}    lineNumber     出错代码的行号
         * @param {Long}    columnNumber   出错代码的列号
         */
        window.onerror = function (errorMessage, scriptURI, lineNumber, columnNumber) {

            // 有callback的情况下，将错误信息传递到options.callback中
            if (typeof callback === 'function') {
                callback({
                    message: errorMessage,
                    script: scriptURI,
                    line: lineNumber,
                    column: columnNumber
                });
            } else {
                // 其他情况，都以alert方式直接提示错误信息
                var err_log_src = 'http://www.yueus.com/mobile_app/log/save_log.php?err_level=2&url='+encodeURIComponent(window.location.href);

                var img = new Image();

                var msgs = [];
                msgs.push("代码有错。。。");
                msgs.push("\n错误信息：", errorMessage);
                msgs.push("\n出错文件：", scriptURI);
                msgs.push("\n出错位置：", lineNumber + '行，' + columnNumber + '列');

                img.src = err_log_src+'&err_str='+encodeURIComponent(msgs.join(''));
                //alert(msgs.join(''));
            }
        }
    };

    WeixinApi.wxPay = function(data,callback)
    {
        callback = callback || function(){};
        WeixinJSBridge.invoke("getBrandWCPayRequest", data,
        function (res)
        {
            var code = 0;

            var err_log_src = 'http://www.yueus.com/mobile_app/log/save_log.php?err_level=1&url='+encodeURIComponent(window.location.href);

            var img = new Image();

            if( res.err_msg == 'get_brand_wcpay_request:ok' )
            {
                //支付成功
                code = 1;
            }
            else if( res.err_msg == 'get_brand_wcpay_request:cancel' )
            {
                //支付过程中
                code = 10;
            }
            else if( res.err_msg == 'get_brand_wcpay_request:fail' )
            {
                //支付失败
                code = -3;

                img.src = err_log_src+'&err_str='+encodeURIComponent(res.err_msg);

                console.log('url='+window.location.href+'&err_str='+res.err_msg);

                alert("支付失败:"+res.err_msg);

            }
            else
            {
                img.src = err_log_src+'&err_str='+encodeURIComponent(res.err_msg);

                console.log('url='+window.location.href+'&err_str='+res.err_msg);

                alert("支付失败，由于网络问题提交失败，请点击左上角关闭并重新进入");
            }

            callback.call(this,{code : code});
        })

    };



    WeixinApi.ready();

    WeixinApi.enableDebugMode();

    module.exports = WeixinApi;

    function isFunction(o) {
        return typeof o === 'function';
    }
});
