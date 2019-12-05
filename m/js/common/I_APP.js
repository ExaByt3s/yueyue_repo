/**
 * App 接口
 */
define(function(require, exports, module) {
    var $ = require('$');
    var Backbone = require('backbone');
    var utility = require('./utility');
    var global_config = require('./global_config');
    var m_alert = require('../ui/m_alert/view');
    var page_control = require('../frame/page_control');

    var appBridge = window.PocoWebViewJavascriptBridge;

    var isPaiApp = typeof appBridge !== 'undefined';

    var prefix = 'poco.yuepai.';

    if (isPaiApp) {
        appBridge.init();

        // 留接口比app调用
        var pocoAppEventTrigger = $.extend({}, Backbone.Events);
        window.pocoAppEventTrigger = function() {
            return pocoAppEventTrigger.trigger.apply(pocoAppEventTrigger, arguments);
        };
    }

    var App = {
        isPaiApp: isPaiApp,

        on: function() {
            if (!App.isPaiApp) {
                return;
            }
            return pocoAppEventTrigger.on.apply(pocoAppEventTrigger, arguments);
        },
        off: function() {
            if (!App.isPaiApp) {
                return;
            }
            return pocoAppEventTrigger.off.apply(pocoAppEventTrigger, arguments);
        },
        once: function() {
            if (!App.isPaiApp) {
                return;
            }
            return pocoAppEventTrigger.once.apply(pocoAppEventTrigger, arguments);
        },
        /**
         * 私信
         * @param options
         */
        chat : function(options)
        {
            var self = this;

            appBridge.callHandler(prefix+'function.chat', options, function()
            {
                typeof options.callback == 'function' && options.callback.call(this);
            });
        },
        /**
         * 扫二维码
         */
        qrcodescan : function(options)
        {
            var self = this;

            var options = options || {};

            var is_nav_page = (options.is_nav_page == null)?true : false;

            var success = options.success || function(){};

            appBridge.callHandler(prefix+'function.qrcodescan', options, function(data)
            {
                if(data.code == "0000")
                {
                    // 创建location对象
                    var a = document.createElement('a');

                    a.href = data.scanresult;

                    if (!window.location.origin)
                    {
                        window.location.origin = window.location.protocol + '//' + window.location.hostname + (window.location.port ? (':' + window.location.port) : '');
                    }

                    // 替换正确的路由
                    var href = a.href.replace(a.origin,window.location.origin);

                    console.log(href);

                    utility.ajax_request
                    ({
                        url : href,
                        beforeSend : function()
                        {
                            m_alert.show('发送中','right');
                        },
                        success : function(response)
                        {
                            var data = response.result_data;

                            m_alert.show(data.msg,'right');

                            if(data.code >0)
                            {
                                // 设置扫描后进行跳页
                                if(is_nav_page)
                                {
                                    page_control.navigate_to_page(data.type);
                                }
                                else
                                {
                                    success.call(this,data);
                                }

                            }
                        },
                        error : function()
                        {
                            m_alert.show('网络异常','error');
                        }
                    });


                }

                //typeof options.callback == 'function' && options.callback.call(this,data);
            });
        },
        /**
         * 检查是否登录
         * @param callback
         */
        check_login : function(callback)
        {


            appBridge.callHandler(prefix+'info.login',
            {


            }, function(poco_id)
            {
                callback.call(this,poco_id);
            });
        },
        /**
         * 登录
         */
        login : function(options)
        {
            var self = this;

            var options = options || {};

            appBridge.callHandler(prefix+'function.login',
            {
                pocoid : options.pocoid,
                username : options.username,
                icon : options.icon

            }, function()
            {


            });
        },
        /**
         * 退出登录
         * @param options
         */
        logout : function()
        {
            var self = this;

            console.log('APP logout');

            appBridge.callHandler(prefix+'function.logout',
            {

            }, function()
            {


            });
        },
        /**
         * 保存设备
         */
        save_device : function(tag,callback)
        {
            var self = this;

            var tag = tag || false;

            appBridge.callHandler(prefix+'info.device',{},
            function(data)
            {
                console.log(data);

                data = $.extend(data,{user_id : utility.login_id});

                if(!tag)
                {
                    utility.ajax_request
                    ({
                        url : global_config.ajax_url.a_img,
                        data : data
                    });
                }
                else
                {
                    if(typeof callback == 'function')
                    {
                        callback.call(this,data);
                    }



                }

            });
        },
        /**
         * 获取聊天信息
         * @param callback
         */
        get_chat_info : function(callback)
        {
            var self = this;

            appBridge.callHandler(prefix+'info.chat',{},
            function(data)
            {
                if(typeof callback == 'function')
                {
                    callback.call(this,data);
                }

            });
        },
        /**
         * 获取登录信息
         * @param success
         */
        get_login_info : function(success)
        {
            var self = this;

            appBridge.callHandler(prefix+'info.login',{},
            function(data)
            {
                if(typeof success == 'function')
                {
                    success.call(this,data);
                }


            });
        },
        /**
         * 支付宝支付
         * @param params
         */
        alipay : function(params,callback)
        {
            var self = this;

            if(!params)
            {
                alert('no params');

                return;
            }

            console.log(params);

            appBridge.callHandler(prefix+'function.alipay',params,
            function(data)
            {
                console.log('======支付宝支付回调参数======');
                console.log(data);

                if(typeof callback == 'function')
                {
                    callback.call(this,data);
                }
            });
        },
        /**
         * 微信支付
         * @param params
         */
        wxpay : function(params,callback)
        {
            var self = this;

            if(!params)
            {
                alert('no params');

                return;
            }

            console.log(params);

            appBridge.callHandler(prefix+'function.wxpay',params,
                function(data)
                {
                    console.log('======微信支付回调参数======');
                    console.log(data);

                    if(typeof callback == 'function')
                    {
                        callback.call(this,data);
                    }
                });
        },
        /**
         * 上传图片
         * @param params
         * @param callback
         */
        upload_img : function(type,params,callback)
        {
            var self = this;

            console.log('upload img');

            var url = '';
            var photosize = params.photosize || 640;

            //var domain = 'http://imgup-yue.yueus.com';

            var is_wifi = window.APP_NET_STATUS == 'wifi'?'-wifi':'';

            console.log('net status:'+window.APP_NET_STATUS);

            var icon_domain = 'http://sendmedia'+is_wifi+'.yueus.com:8078/';
            var pics_domain = 'http://sendmedia'+is_wifi+'.yueus.com:8079/';

            if(!params)
            {
                alert('no params');

                return;
            }

            if(!type)
            {
                alert('no type');

                return;
            }

            var operation = '';
            var src_opts = '';

            switch (type)
            {
                case 'header_icon':
                    //url = domain + '/ultra_upload_service/yueyue_upload_user_icon_act.php';
                    url = icon_domain + 'icon.cgi';
                    operation = 'modify_headicon';
                    src_opts = 'camera_album';
                    break;
                case 'single_img':
                    //url = domain + '/ultra_upload_service/yueyue_upload_act.php';
                    url = pics_domain + 'upload.cgi';
                    src_opts = 'camera_album';
                    break;
                case 'multi_img':
                     /*if(params.is_zip == 1)
                     {
                         url = domain + '/ultra_upload_service/yueyue_multi_upload_act.php';
                     }
                     else
                     {
                        url = domain +  '/ultra_upload_service/yueyue_upload_act.php';
                     }*/

                    url = pics_domain + 'upload.cgi';
                    src_opts = 'camera_album';

                    break;
            }


            params = $.extend(params,{url:url,photosize : photosize,operation:operation,src_opts : src_opts});

            console.log('-----upload img params-----');
            console.log(params);

            appBridge.callHandler(prefix+'function.uploadpic',params,
                function(data)
                {
                    callback.call(this,data);
                });




        },
        /**
         * 打开聊天列表
         */
        show_chat_list : function()
        {

            appBridge.callHandler(prefix+'function.openchatlist',{},
            function()
            {

            });
        },
        /**
         * 调试模式
         * url：待调试的首页链接
           cache_onoff：是否开启缓存，0关闭，1开启
           debug：0/1 ,是否启用调试模式，0关闭，1开启
         */
        debug : function(options)
        {
            var data = data || {};

            if(options.cache)
            {
                data.url = global_config.romain;
            }
            else
            {
                data.url = global_config.debug_romain;
            }

            data.debug = options.debug ? 1 : 0;

            data.cache_onoff = options.cache ? 1 : 0;

            appBridge.callHandler(prefix+'function.debug',data,
            function()
            {

            });
        },
        /**
         * 放大图
         * @param data
         */
        show_alumn_imgs : function(data)
        {
            var data = data || {};

            appBridge.callHandler(prefix+'function.show_album_imgs',data,
            function()
            {

            });
        },
        /**
         * 获取网络状态
         * @param callback
         */
        get_netstatus : function(callback)
        {
            var data = data || {};

            appBridge.callHandler(prefix+'info.netstatus',{},
            function(data)
            {

                //type off、wifi、mobile

                if(typeof callback == 'function')
                {
                    callback.call(this,data);
                }
            });
        },
        /**
         * 第三方登录
         * @param params
         * @param callback
         * 传入参数：
           platform：qzone/sina
           返回参数：
           code：0000-成功，1000-失败
           message：错误信息
           uid：用户id
           token：令牌
           tokensecret：
         */
        sso_login : function(params,callback)
        {

            appBridge.callHandler(prefix+'function.bind_account',params,
            function(data)
            {
                if(typeof callback == 'function')
                {
                    callback.call(this,data);
                }
            });
        },
        /**
         * 获取地理信息
         * @param params
         * @param callback
         * return
         *
            code：0000-成功，1000-失败，1002-超时
         　　long：经度
         　　lat：纬度
         */
        get_gps : function(params,callback)
        {
            appBridge.callHandler(prefix+'function.getgps',params,
                function(data)
                {
                    if(typeof callback == 'function')
                    {
                        callback.call(this,data);
                    }
                });
        },
        /**
         * 软件设置
           传入参数：
           msgvibrate：收到消息时手机震动 0/1
           msgsound：收到消息时声音提示 0/1
           返回参数：无
         * @param params
         * @param callback
         */
        set_setting : function(params,callback)
        {
            appBridge.callHandler(prefix+'function.setting',params,
                function(data)
                {

                    if(typeof callback == 'function')
                    {
                        callback.call(this,data);
                    }
                });
        },
        /**
         * 获取软件设置

           传入参数：无
           返回参数：
           msgvibrate：收到消息时手机震动 0/1
           msgsound：收到消息时声音提示 0/1
           remote_notify_setting：是否接收消息 0/1 (iphone)
         * @param params
         * @param callback
         */
        get_setting : function(params,callback)
        {
            appBridge.callHandler(prefix+'info.setting',params,
                function(data)
                {
                    console.log(data)

                    if(typeof callback == 'function')
                    {
                        callback.call(this,data);
                    }
                });
        },
        /**
         * 点击过去清除缓存
         */
        clear_cache : function()
        {
            appBridge.callHandler(prefix+'function.clearcache',{},
                function(data)
                {


                });
        },
        /**
         * 移走封面图
         */
        remove_front : function(page)
        {
            var hash = utility.getHash() || '';

            var highlight = '';

            // 特殊处理 从支付宝跳转过来的页面
            if(/mine\/payment_no_/.test(hash))
            {
                hash = 'mine';
            }

            // 显示底部的路由
            if(global_config._show_footer_hash[hash])
            {
                highlight = hash;
            }

            console.log('function.notifypageready hash:'+hash);

            appBridge.callHandler(prefix+'function.notifypageready',{page : hash,highlight:highlight},
                function(data)
                {


                });

        },
        /**
         * 移走底部bar
         */
        show_bottom_bar : function(show)
        {
            console.log('function.showbottombar')

            appBridge.callHandler(prefix+'function.showbottombar',{show:show},
                function(data)
                {


                });
        },
        /**
         *  检查app更新
            poco.yuepai.function.checkupdate
            传入参数：无
            返回参数：无
         */
        check_update : function()
        {
            appBridge.callHandler(prefix+'function.checkupdate',{},
                function(data)
                {


                });
        },
        /**
         *   模拟app返回键
             poco.yuepai.function.back
             传入参数：无
             返回参数：无
         */
        app_back : function()
        {
            console.log('App Back');

            appBridge.callHandler(prefix+'function.back',{},
                function(data)
                {


                });
        },
        /**
         * 获取app信息 主要用于显示红点
         */
        app_info : function(callback)
        {
            console.log('App app-info');

            appBridge.callHandler(prefix+'info.app',{},
            function(data)
            {
                console.log(data)

                if(typeof callback == 'function')
                {
                    callback.call(this,data);
                }
            });
        }



    };

    module.exports = App;

    function isFunction(o) {
        return typeof o === 'function';
    }
});