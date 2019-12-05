define(function(require, exports, module)
{
    console.log('App 启动');

    /**
     * @param {String}  errorMessage   错误信息
     * @param {String}  scriptURI      出错的文件
     * @param {Long}    lineNumber     出错代码的行号
     * @param {Long}    columnNumber   出错代码的列号
     */
    window.onerror = function (errorMessage, scriptURI, lineNumber, columnNumber)
    {

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
            var err_log_src = 'http://www.yueus.com/mobile_app/log/save_log.php?from_str=weixin&err_level=2&url='+encodeURIComponent(window.location.href);

            var img = new Image();

            var msgs = [];

            msgs.push("代码有错。。。");
            msgs.push("\n错误信息：", errorMessage);
            msgs.push("\n出错文件：", scriptURI);
            msgs.push("\n出错位置：", lineNumber + '行，' + columnNumber + '列');


            if(window.location.href.indexOf('webdev')!=-1)
            {

                console.log(msgs.join(''));
            }
            else
            {
                img.src = err_log_src+'&err_str='+encodeURIComponent(msgs.join(''));
            }


        }
    };

    // 引用基本模块
    var $ = require('$');
    var page_control = require('./frame/page_control');
    var ua = require('./frame/ua');
    var utility = require('./common/utility');
    var cookie = require('cookie');
    var mine = require('./mine/model');
    var global_config = require('./common/global_config');
    var system = require('./common/system');
    var m_alert = require('./ui/m_alert/view');
    var WeixinApi = require('./common/I_WX');
    var WeiXinSDK = require('./common/WX_JSSDK');

    // 设置不可后退的路由
    window._rootHash =
    {
        'hot': 1,
        'find': 1,
        'mine': 1,
        'act/list': 1,
        'location' : 1,
        'model_date/submit_success' : 1,
        'account/register/reg' : 1
    };


    /**
     * 1、重写ajax方法，目的是增加一些统计点和统一交互
     * 2、backone的请求方法也应该生效
     */

    // 超时时间
    var ajaxSettings =
    {
        timeout: 10000
    };

    $.extend($.ajaxSettings, ajaxSettings);

    (function($){
        //备份ajax方法
        var _ajax=$.ajax;

        //重写ajax方法
        $.ajax=function(opt)
        {
            //备份opt中error和success方法
            var fn =
            {
                beforeSend : function(XMLHttpRequest, settings){},
                error:function(XMLHttpRequest, textStatus, errorThrown){},
                success:function(data, textStatus){},
                complete : function(XMLHttpRequest, settings){}
            };

            if(opt.beforeSend)
            {
                fn.beforeSend=opt.beforeSend;
            }
            if(opt.error)
            {
                fn.error=opt.error;
            }
            if(opt.success)
            {
                fn.success=opt.success;
            }
            if(opt.complete)
            {
                fn.complete=opt.complete;
            }

            var url  = opt.url;

            // 预留扩展参数
            opt.data = $.extend({},opt.data,{ '_page_mode' : window._PAGE_MODE });


            //扩展增强处理
            var _opt = $.extend
            (opt,{
                data : fn.data ,
                beforeSend : function(XMLHttpRequest, settings)
                {
                    console.log('%c请求前，请求路径是:'+url,'color:#66ccff');
                    fn.beforeSend(XMLHttpRequest, settings);
                },
                success:function(data, textStatus)
                {
                    //成功回调方法扩展处理
                    console.log('%c请求成功，请求路径是:'+url,'color:#00dd00');

                    fn.success(data, textStatus);
                },
                error:function(XMLHttpRequest, textStatus, errorThrown)
                {
                    //错误方法扩展处理
                    console.log('%c请求失败，请求路径是:'+url,'color:#ff5544');

                    utility.err_log(1,url,XMLHttpRequest.response);

                    console.log('http://www.poco.cn&err_str='+XMLHttpRequest.response);

                    fn.error(XMLHttpRequest, textStatus, errorThrown);
                },
                complete : function(XMLHttpRequest, settings)
                {
                    fn.complete(XMLHttpRequest, settings);
                }
            });

            _ajax(_opt);
        };
    })($);

    // app页面最顶级容器
    var $app_page_conatainer = $('[data-app-page-container]');

    var loc = window.location;

    /*//默认
    var data_share =
    {
        title: "444模特邀约第一移动平台", // 分享标题
        desc: "44模特邀约第一移动平台", // 分享描述
        link: loc.origin + loc.pathname + loc.search + "&for_share=timeline" + loc.hash, // 分享链接
        imgUrl:'http://yp.yueus.com/m/images/weixi_yueyue_icon.png?' + new Date().getTime(), // 分享图标
        type: 'link', // 分享类型,music、video或link，不填默认为link
        dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
        success: function () {
            // 用户确认分享后执行的回调函数
            alert("分分分成功")
        },
        cancel: function () {
            // 用户取消分享后执行的回调函数
            alert("分分分失败")
        }
    };
    */
    //微信配置初始化
    (function()
    {
        var ua_str = navigator.userAgent.toLowerCase();

        if(WeiXinSDK.isWeiXin() || WeixinApi.isWexXin())
        {
            var version_str = (ua_str.match(/micromessenger\/(\d+\.\d+\.\d+)/) || ua_str.match(/micromessenger\/(\d+\.\d+)/))[1];

            if(version_str)
            {
                global_config.WeiXin_Version = version_str;
            }
        }



        if(WeiXinSDK.isWeiXin() && global_config.WeiXin_Version >= "6.0.2")//(global_config.WeiXin_Version.indexOf('6.0.2') != -1|| global_config.WeiXin_Version.indexOf('6.1') != -1)
        {

            utility.ajax_request
            ({
                url: global_config.ajax_url.wx_get_js_api_sign_package,
                data : {url:loc.origin + loc.pathname + loc.search},
                cache : false,
                beforeSend : function(xhr,options)
                {
                    //self.trigger('before:wx_sign',xhr,options);
                },
                success : function(response, xhr, options)
                {


                    WeiXinSDK.setConfig(response.result_data);

                },
                error : function(collection, response, options)
                {
                    //self.trigger('error:wx_sign',response,options)
                },
                complete : function(xhr,status)
                {
                    //self.trigger('complete:wx_sign',xhr,status);
                }
            });

            /*
            WeiXinSDK.ready(function()
            {
                WeiXinSDK.ShareToFriend(data_share);

                WeiXinSDK.ShareTimeLine(data_share);

                WeiXinSDK.ShareQQ(data_share);
            });
            */

        }
    })();
    /**
     * 解决场景切换时，文本框焦点丢失让手机键盘隐藏处理
     */
    $app_page_conatainer.on('tap', function(event)
    {
        var target = event.target;
        var targetNodeName = target.nodeName;

        var activeElement = document.activeElement;
        var activeElementNodeName = activeElement.nodeName;

        if (targetNodeName !== activeElementNodeName && (activeElementNodeName === 'INPUT' ||activeElementNodeName === 'TEXTAREA'))
        {

            activeElement.blur();

        }
    });



    // 触发窗体发生变化的函数
    window.addEventListener('resize', function()
    {
        page_control.window_change_page_triger();

    }, false);

    // 引用页面
    var App = require('./common/I_APP');
    var WeiXin = require('./common/I_WX');

    require('./model_date/hot/index');
    require('./location/index');
    require('./model_date/find/index');
    require('./model_date/search/index');
    require('./model_date/search_result/index');

    // 模特卡页面需要的模块
    require('./model_date/model_card/index');
    /*
     require('./model_date/model_card/edit/index');
     require('./model_date/model_card/edit_all/index');
     require('./model_date/model_card/price_style_select/index');
     require('./model_date/model_card/edit_condition/index');
     */

    // 约拍流程和支付流程
    require('./model_date/model_style/index');
    require('./model_date/submit_application/index');
    require('./model_date/payment/index');
    require('./model_date/submit_success/index');
    require('./model_date/complete_application/index');

    require('./mine/index');
    require('./mine/zone/index');
    require('./mine/consider/index');
    require('./mine/edit/index');
    //require('./mine/authentication/index');
    //require('./mine/authentication_list/index');
    //require('./mine/profile/index');
    require('./mine/consider_details_camera/index');
    //require('./mine/consider_details_model/index');
    //require('./mine/consider_details_model/receive/index');
    //require('./mine/consider_details_model/refuse/index');


    /* 有关活动的模块*/
    require('./mine/status/index');
    require('./act/list/index');
    require('./act/apply/index');

    require('./act/signin/index');
    require('./act/detail/index');
    require('./act/pub/info/index');
    require('./act/pub/intro/index');
    require('./act/pub/arrange/index');
    require('./act/payment/index');


    require('./act/security/index');
    require('./account/login/index');
    //require('./account/front/index');
    //require('./account/setting/index');

    //require('./mine/money/withdrawal/index');
    require('./mine/money/bill/index');
    //require('./account/setup/index');
    //require('./account/setup/bind/index');
    require('./account/setup/bind/enter_phone/index');
    require('./account/setup/bind/enter_pw/index');
    //require('./mine/money/recharge/index');

    require('./account/register/login_phone/index');
    require('./account/register/login_pw/index');
    //require('./account/register/change_phone/index');
    //require('./account/register/bind_phone/index');
    //require('./account/agreement_login/index');

    // 数字码签到
    //require('./mine/checkins/index');

    // 评论
    require('./comment/index');
    require('./comment/list/index');

    // 排行榜
    require('./rank/index');

    require('./model_date/agreement/index');
    require('./mine/info/index');
    require('./topic/info/index');
    require('./report/index');

    require('./account/fans_follows/index');
    require('./account/register/reg/index');

    require('./model_date/v2_validation/index');

    //require('./account/about/index');

    // 信用金
    //require('./mine/credit/index');

    // 消息模块
    //require('./message/index');

    // 绑定支付宝
    //require('./mine/money/bind-alipay/index');

    require('./mine/date_cancel_reason/index');
    require('./topic/index');
    require('./account/login_tips/index');
    require('./edit_page/index');

    require('./topic/person_order/index');
    require('./mine/explain/index');


    require('./act/pay_success/index');

    require('./coupon/choose/index');




    // 设置默认地区
    var default_location =
    {
        location_id : 101029001,
        location_name : '广州'
    };

    if(!cookie.get('yue_location_id'))
    {
        // 设置cookie ，默认地区是广州
        cookie.set('yue_location_id',default_location.location_id);
    }

    // 缓存地区
    var cache_location = utility.storage.get('location');

    if(!cache_location)
    {
        utility.storage.set('location',default_location);


    }


    // 设置路由
    var default_rote = global_config.default_index_route;

    // 获取登录用户信息
    utility.user = new mine;


    /*// 第一次进来app
     if(!utility.storage.get('is_frist_time_open_app'))
     {
     // 注册页面
     default_rote = 'account/register/reg/1';

     }*/

    var header_obj = null;

    // 转场初始化
    page_control.init($app_page_conatainer,
        {
            default_title: '约约',
            default_index_route: default_rote,
            before_route : function(page_his_buff)
            {
                //统计pv
                utility.page_pv_stat_action();

                //utility.stat_action({tj_point:'touch',tj_touch_type :'follow'});
            },
            after_route : function(page_his_buff)
            {
                console.log(window.location);
                // 路由跳转后锁屏解除
                setTimeout(function()
                {
                    $('body').css('pointer-events','auto');

                    if($('.ui-dialog').length>0)
                    {
                        $('.ui-dialog').addClass('fn-hide');
                    }
                },100);

                if(!$("#J-splashscreens-progress-radial").hasClass('fn-hide'))
                {
                    $('#J-splashscreens').addClass('fn-hide');
                    $("#J-splashscreens-progress-radial").addClass('complete');

                    clearInterval(window._splashscreens_interval);
                }



                // 微信取消右上角按钮
                //WeiXin.isWexXin() && WeiXin.hideOptionMenu();

                // 头部滑下问题
                header_obj = null;

                var url_hash = window.location.hash;
                url_hash = url_hash.replace("#","");

                var slash_pos = url_hash.indexOf('/');
                if(slash_pos>0) url_hash = url_hash.substr(0,slash_pos);

                if($.inArray(url_hash, global_config.fixed_header_pos_page) != -1)
                {
                    var current_page_view = page_control.return_current_page_view();

                    header_obj = current_page_view.$el.find('[data-role="slide-header"]');

                    animating_end()
                }

                var hash = utility.getHash();

                var show_btn = true;

                /*
                var ua_str = navigator.userAgent.toLowerCase();

                var wx_str = 'micromessenger';

                var weixin_version_contain = ua_str.substring(ua_str.indexOf(wx_str),ua_str.length);

                var weixin_version;

                if(ua_str.indexOf(wx_str) == -1)
                {
                    weixin_version = false;

                    return false
                }
                else
                {
                    if(weixin_version_contain.indexOf(' ') == -1)
                    {
                        weixin_version = weixin_version_contain.substring(wx_str.length+1,weixin_version_contain.length);
                    }
                    else if(weixin_version_contain.indexOf(' ') != -1)
                    {
                        weixin_version = weixin_version_contain.substring(wx_str.length+1,weixin_version_contain.indexOf(' '));
                    }
                    else
                    {
                        //alert("unknow_version",ua_str)
                    }
                }

                if(weixin_version)
                {
                    //alert(weixin_version);

                    global_config.WeiXin_Version = weixin_version;
                }
                */

                //区分6.0.2 或其他版本


                if(WeiXinSDK.isWeiXin() && global_config.WeiXin_Version >= "6.0.2")//(global_config.WeiXin_Version.indexOf('6.0.2') != -1|| global_config.WeiXin_Version.indexOf('6.1') != -1)
                {

                    //隐藏右上角按钮判断 2015-1-19 nolest //新接口
                    var complete_hash = window.location.hash;

                    var submit_application_str = '#model_date/submit_application';



                    if((complete_hash.indexOf('#comment') == 0) && (complete_hash.indexOf('#comment/list') == -1))
                    {
                        show_btn = false;
                    }
                    else if(complete_hash.indexOf('#model_date/submit_application') && (complete_hash.length == submit_application_str.length))
                    {
                        show_btn = false;
                    }
                    else
                    {
                        $.each(global_config.no_share_pages,function(i,obj)
                        {
                            if(window.location.hash.indexOf(global_config.no_share_pages[i]) != -1)
                            {
                                show_btn = false;

                            }
                        })
                    }


                    if(!show_btn)
                    {
                        WeiXinSDK.ready(function()
                        {
                            WeiXinSDK.hideOptionMenu();
                        })
                    }
                    else
                    {
                        WeiXinSDK.ready(function()
                        {
                            WeiXinSDK.showOptionMenu();
                        })
                    }

                    var loc = window.location;

                    var share_store = utility.wei_xin.get_share_new(loc.hash);


                    var wxData,wx_Data_Timeline;

                    if(share_store)
                    {
                        wxData = share_store.wxData;
                        wx_Data_Timeline = share_store.wxData_Timeline;
                    }
                    else
                    {
                        wxData =
                        {
                            title: "【约yue】 100000+模特随心约", // 分享标题
                            desc: "来模特邀约第一移动平台，不经意的遇上最美丽的风景", // 分享描述
                            link: loc.href,
                            imgUrl:'http://yp.yueus.com/m/images/weixi_yueyue_icon.png?' + new Date().getTime(), // 分享图标
                            type: 'link', // 分享类型,music、video或link，不填默认为link
                            dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
                            success: function () {
                                // 用户确认分享后执行的回调函数
                                //alert("分分分成功")
                                utility.stat_action({tj_point:'touch',tj_touch_type :'share',tj_extend_params :{ hash:loc.hash}});
                            },
                            cancel: function () {
                                // 用户取消分享后执行的回调函数
                                //alert("分分分失败")
                            }
                        };

                        wx_Data_Timeline =
                        {
                            title: "【约yue】 100000+模特随心约", // 分享标题
                            desc: "来模特邀约第一移动平台，不经意的遇上最美丽的风景", // 分享描述
                            link: loc.origin + loc.pathname + loc.search + "&for_share=timeline" + loc.hash, // 分享链接
                            imgUrl:'http://yp.yueus.com/m/images/weixi_yueyue_icon.png?' + new Date().getTime(), // 分享图标
                            type: 'link', // 分享类型,music、video或link，不填默认为link
                            dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
                            success: function () {
                                // 用户确认分享后执行的回调函数
                                //alert("分分分成功")
                                utility.stat_action({tj_point:'touch',tj_touch_type :'share',tj_extend_params :{ hash:loc.hash}});
                            },
                            cancel: function () {
                                // 用户取消分享后执行的回调函数
                                //alert("分分分失败")
                            }
                        };

                        utility.wei_xin.set_share_new(loc.hash,wxData,wx_Data_Timeline);
                    }



                    WeiXinSDK.ready(function()
                    {
                        WeiXinSDK.ShareToFriend(wxData);

                        WeiXinSDK.ShareTimeLine(wx_Data_Timeline);

                        WeiXinSDK.ShareQQ(wxData);


                    });

                    //utility.wei_xin.set_share_new(loc.hash,data_share);
                }
                else
                {
                    //隐藏右上角按钮判断 2015-1-15 nolest //新接口
                    if(WeixinApi.isWexXin())//(WeiXinSDK.isWeiXin())
                    {
                        var complete_hash = window.location.hash;

                        var submit_application_str = '#model_date/submit_application';


                        if((complete_hash.indexOf('#comment') == 0) && (complete_hash.indexOf('#comment/list') == -1))
                        {
                            show_btn = false;
                        }
                        else if(complete_hash.indexOf('#model_date/submit_application') && (complete_hash.length == submit_application_str.length))
                        {
                            show_btn = false;
                        }
                        else
                        {
                            $.each(global_config.no_share_pages,function(i,obj)
                            {
                                if(window.location.hash.indexOf(global_config.no_share_pages[i]) != -1)
                                {
                                    show_btn = false;
                                }
                            })
                        }
                    }

                    //微信分享功能默认设置 2015-1-14 nolest //旧接口
                    if(WeixinApi.isWexXin())
                    {
                        var self = this;

                        WeixinApi.ready(function(Api)
                        {
                            if(!show_btn)
                            {
                                Api.hideOptionMenu();
                            }
                            else
                            {
                                Api.showOptionMenu();
                            }

                            var loc = window.location;

                            var share_store = utility.wei_xin.get_share(loc.hash);


                            var wxData,wx_Data_Timeline,wxCallbacks;

                            if(share_store)
                            {
                                wxData = share_store.wxData;
                                wx_Data_Timeline = share_store.wxData_Timeline;
                                wxCallbacks = share_store.wxCallbacks;
                            }
                            else
                            {
                                wxData = {
                                    "appId": "wx25fbf6e62a52d11e", // 服务号可以填写appId
                                    "imgUrl" : 'http://yp.yueus.com/m/images/weixi_yueyue_icon.png?' + new Date().getTime(),
                                    "link" : loc.href,
                                    "desc" : "【约yue】 100000+模特随心约",
                                    "title" : "来模特邀约第一移动平台，不经意的遇上最美丽的风景"
                                };

                                wx_Data_Timeline  = {
                                    "appId": "wx25fbf6e62a52d11e", // 服务号可以填写appId
                                    "imgUrl" : 'http://yp.yueus.com/m/images/weixi_yueyue_icon.png?' + new Date().getTime(),
                                    "link" : loc.origin + loc.pathname + loc.search + "&for_share=timeline" + loc.hash,
                                    "desc" : "【约yue】 100000+模特随心约",
                                    "title" : "来模特邀约第一移动平台，不经意的遇上最美丽的风景"
                                };

                                // 分享的回调
                                wxCallbacks = {
                                    // 收藏操作不执行回调，默认是开启(true)的
                                    favorite : false,

                                    // 分享操作开始之前
                                    ready : function() {
                                        // 你可以在这里对分享的数据进行重组
                                        //alert("准备分享");
                                    },
                                    // 分享被用户自动取消
                                    cancel : function(resp) {
                                        // 你可以在你的页面上给用户一个小Tip，为什么要取消呢？
                                        //alert("分享被取消，msg=" + resp.err_msg);
                                    },
                                    // 分享失败了
                                    fail : function(resp) {
                                        // 分享失败了，是不是可以告诉用户：不要紧，可能是网络问题，一会儿再试试？
                                        //alert("分享失败，msg=" + resp.err_msg);
                                    },
                                    // 分享成功
                                    confirm : function(resp) {
                                        // 分享成功了，我们是不是可以做一些分享统计呢？
                                        //alert("分享成功，msg=" + resp.err_msg);
                                        utility.stat_action({tj_point:'touch',tj_touch_type :'share',tj_extend_params :{hash:loc.hash}});
                                    },
                                    // 整个分享过程结束
                                    all : function(resp,shareTo) {
                                        // 如果你做的是一个鼓励用户进行分享的产品，在这里是不是可以给用户一些反馈了？
                                        //alert("分享" + (shareTo ? "到" + shareTo : "") + "结束，msg=" + resp.err_msg);
                                    }
                                };

                                utility.wei_xin.set_share(loc.hash,wxData,wx_Data_Timeline,wxCallbacks);
                            }
                            // 安卓好友
                            Api.shareToFriend(wxData, wxCallbacks);

                            // 安卓朋友圈
                            Api.shareToTimeline(wx_Data_Timeline, wxCallbacks);

                            // 腾讯微博
                            //WeixinApi.shareToWeibo(wxData, wxCallbacks);

                            // iOS好友/朋友圈
                            Api.generalShare(wxData,wxCallbacks);
                        });

                    }
                }
            }
        });


    $(document.body).on("swipeup",function()
    {
        fixed_header_pos(false)
    });

    $(document.body).on("swipedown",function()
    {
        fixed_header_pos(true)

    });

    // 页面头部元素滑动隐藏
    // 1.在header元素后加入[data-role="slide-header"] 和 类 fn-hide
    // 2.在global_config的fixed_header_pos_page数组内加入对应页hash
    // 3.header 布局必须为absolute
    // 2014-10-10 nolest
    function fixed_header_pos(show)
    {
        if(header_obj)
        {
            //下拉
            if(show==true)
            {
                header_obj.removeClass("pull-up fn-hide").addClass("drop-down");

                header_obj.attr("is_drop","drop");
            }
            else
            {
                header_obj.removeClass("drop-down").addClass("pull-up");

                header_obj.attr("is_drop","undrop");

            }
        }
    }
    //动画完成监听事件
    function animating_end()
    {

        header_obj.on('webkitAnimationEnd', function()
        {
            if(header_obj.attr("is_drop") == "drop")
            {
                header_obj.addClass("");
            }
            else
            {
                header_obj.addClass("fn-hide");
            }

        });
    }

    // 保存设备信息
    if(App.isPaiApp)
    {
        App.save_device();
    }


    // 判断是否登录
    if (utility.login_id > 0)
    {
        var local_user = utility.storage.get("user");
        if (local_user && local_user.user_id)
        {
            utility.user.set(local_user);
        }


        // 已经登录
        utility.user.once("success:get_info:fetch", function(reponse)
        {
            var data = reponse.result_data.data;

            if (data)
            {
                utility.user.set(data);
            }


        }).once("complete:get_info:fetch", function(xhr, state) {
            state == "success" || utility.user.get_info();
        }).get_info();

        page_control.route_start();


    } else {

        utility.login_id = 0;

        page_control.route_start();
    }






    function _show_upload_progress(progress)
    {
        m_alert.show(progress,'right',{delay:500});
    }

    function _show_upload_finish()
    {
        m_alert.show('上传完毕','right',{delay:1000});
    }




});