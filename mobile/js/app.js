define(function(require, exports, module) 
{

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
            var err_log_src = 'http://www.yueus.com/mobile_app/log/save_log.php?from_str=app&err_level=2&url='+encodeURIComponent(window.location.href);

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
    // 引用页面
    var App = require('./common/I_APP');

    if(!utility.storage.get('is_first_time_to_open_app'))
    {
        window.isFirstTimeOpenApp = true;

        utility.storage.set('is_first_time_to_open_app',1);
    }
        
    // 设置不可后退的路由
    window._rootHash =
    {
        'hot': 1,
        'find': 1,
        'mine': 1,
        'location' : 1,
        'model_date/submit_success' : 1,
        'account/register/reg' : 1,
        'demand' : 1,
        'model_date/payment' : 1
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


            //扩展增强处理
            var _opt = $.extend
            (opt,{

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
                        console.log(XMLHttpRequest)

                        //错误方法扩展处理
                        console.log('%c请求失败，请求路径是:'+url,'color:#ff5544');

                        utility.err_log(1,url,XMLHttpRequest.response);

                        fn.error(XMLHttpRequest, textStatus, errorThrown);

                        //m_alert.show('网络异常 \n\r'+XMLHttpRequest.response,'error');

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

    /**
     * 解决场景切换时，文本框焦点丢失让手机键盘隐藏处理
     */
    $app_page_conatainer.on('tap', function(event)
    {
        var target = event.target;
        var targetNodeName = target.nodeName;

        hide_key_board(targetNodeName);


    });

    // 触发窗体发生变化的函数
    window.addEventListener('resize', function()
    {
        page_control.window_change_page_triger();

    }, false);



    require('./init/index');

    //require('./model_date/hot/index');
    //require('./location/index');
    //require('./model_date/find/index');
    //require('./model_date/search/index');
    //require('./model_date/search_result/index');
    //require('./model_date/model_card/index');
    //require('./model_date/model_card/edit/index');
    //require('./model_date/model_card/edit_all/index');
    //require('./model_date/model_card/price_style_select/index');
    //require('./model_date/model_card/edit_condition/index');
    require('./model_date/model_style/index');
    require('./model_date/submit_application/index');
    require('./model_date/payment/index');
    require('./model_date/submit_success/index');
    require('./model_date/complete_application/index');
	
	

    require('./mine/index');
    //require('./mine/zone/index');
    require('./mine/consider/index');
    require('./mine/edit/index');
    require('./mine/authentication/index');
    require('./mine/authentication_list/index');
    //require('./mine/profile/index');
    require('./mine/consider_details_camera/index');
    require('./mine/consider_details_model/index');
    require('./mine/consider_details_model/receive/index');
    require('./mine/consider_details_model/refuse/index');
    require('./mine/status/index');
    //require('./mine/money/withdrawal/index');
    //require('./mine/money/bill/index');
    require('./mine/money/recharge/index');
    require('./mine/checkins/index');
    require('./mine/info/index');
    require('./mine/credit/index');
    //require('./mine/money/bind-alipay/index');
    require('./mine/date_cancel_reason/index');
    require('./mine/explain/index');

    require('./act/list/index');
    require('./act/apply/index');
    require('./act/security/index');
    require('./act/signin/index');
    require('./act/detail/index');
    require('./act/pub/info/index');
    require('./act/pub/intro/index');
    require('./act/pub/arrange/index');
    require('./act/payment/index');
    require('./act/pay_success/index');


    require('./account/login/index');
    //require('./account/front/index');
    //require('./account/setting/index');


    //require('./account/setup/index');
    //require('./account/setup/bind/index');
    //require('./account/setup/bind/enter_phone/index');
    //require('./account/setup/bind/enter_pw/index');


    //require('./account/register/login_phone/index');
    //require('./account/register/login_pw/index');
    //require('./account/register/change_phone/index');
    //require('./account/register/bind_phone/index');
    require('./account/agreement_login/index');



    require('./comment/index');
    require('./comment/list/index');

    require('./rank/index');

    require('./model_date/agreement/index');

    require('./topic/info/index');
    require('./report/index');

    //require('./account/fans_follows/index');
    //require('./account/register/reg/index');
    //require('./account/about/index');



    require('./message/index');
    require('./topic/index');


    require('./topic/person_order/index');
    require('./edit_page/index');

    require('./coupon/choose/index');
    require('./coupon/list/index');
    require('./coupon/details/index');
    require('./coupon/code/index');

    require('./demand/index');
    require('./camera_demand/list/index');
    require('./camera_demand/detail/index');
    require('./camera_demand/success/index');
    require('./camera_demand/model_push/index');

    require('./edit_page/photo/index');

    //require('./mine/level_2/notice/index');
    //require('./mine/level_2/ready/index');
    //require('./mine/level_2/status/index');

    //require('./mine/level_3/ready/index');
    //require('./mine/level_3/status/index');

    //require('./mine/level/index');




    // 获取全局的网络状态
    App.isPaiApp&&App.get_netstatus(function(data)
    {
        window.APP_NET_STATUS = data.type;
    });

    // 设置默认地区
    var default_location =
    {
        location_id : 101029001,
        location_name : '广州'
    };
    // 缓存地区
    var cache_location = utility.storage.get('location');

    if(!cache_location)
    {
        utility.storage.set('location',default_location);
    }






    /**
     * 定义App 触发 js事件
     * @param key 函数名
     * @param params 参数 {}
     * @private
     */
    window.$_AppCallJSObj  = $({});

    window._AppCallJSFunc = function(key,params)
    {
        var self = this;

        $_AppCallJSObj.trigger(key,params);
    };

    // App call js接口
    $_AppCallJSObj.on('show_upload_progress',function(event,data)
    {
        // 上传进度

        var progress = data.progress;

        _show_upload_progress(progress);
    }).on('show_upload_finish',function()
    {
        // 上传完成
        _show_upload_finish();
    }).on('web_logout',function(event,data)
    {
        var data = data || {};

        //退出
        system.logout
        ({
            success : function()
            {
                if(typeof data.logout_success == 'function')
                {
                    data.logout_success.call(this);
                }

            },
            error : function()
            {
                if(typeof data.logout_error == 'function')
                {
                    data.logout_error.call(this);
                }

            }
        });
    })
    .on('web_nav_page',function(event,data)
    {
        window._AppPageName = '';

        //app page name

        if(data.app_page_name)
        {
            window._AppPageName = data.app_page_name;
        }


        // web页面导航
        page_control.navigate_to_page(data.url);


    })
    .on('set_location_cookie',function(event,data)
    {
        cookie.set('yue_location_id',data.location_id);
    })
    .on('ctl_appver_update_point',function(event,data)
    {
        //显示红点更新

        var $red_point = $('[data-appver-update-point]');

        if(data.is_show)
        {
            $red_point.removeClass('fn-hide');
        }
        else
        {
            $red_point.addClass('fn-hide');
        }


    });


    // 设置路由
    var default_rote = global_config.default_index_route;

    // 获取登录用户信息
    utility.user = new mine;


    // 第一次进来app
    if(!utility.storage.get('is_frist_time_open_app'))
    {
        // 注册页面
        default_rote = 'account/register/reg/1';

    }

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


        },
        after_route : function(page_his_buff)
        {
            if(App.isPaiApp)
            {
                App.closeloading();
            }

            var current_page = page_control.return_current_page_view()

            var url_hash = window.location.hash;
            url_hash = url_hash.replace("#","");

            /****** 获取页面key *******/
            var page_key = current_page.$el.attr('data-page-key');

            var vid = '';

            if(global_config.analysis_page[page_key])
            {
                // 访问模特卡
                if(/model_card\/\d*/.test(url_hash))
                {
                    vid = url_hash.replace(/model_card\//,'');

                    console.log(vid);
                }
                else if(/zone\/\d*\/cameraman/.test(url_hash))
                {
                    vid = url_hash.replace(/zone\//,'').replace('/cameraman','');

                    console.log(vid);
                }

                global_config.analysis_page[page_key] = $.extend({},global_config.analysis_page[page_key],{vid:vid});

                console.log('%c======= App moduletongji 统计参数START =======','color:#F72EF4');

                console.log('analysis_page params :',global_config.analysis_page[page_key]);

                App.isPaiApp && App.analysis('moduletongji',global_config.analysis_page[page_key]);

                console.log('%c======= App moduletongji 统计参数END =======\n\r','color:#F72EF4');
            }
            /****** 获取页面key *******/

            /****** 启动图 *******/
            $('[data-role="front-screen"]').addClass('fn-hide');

            $app_page_conatainer.removeClass('fn-hide');
            /****** 启动图 *******/



            // 路由跳转后锁屏解除
            setTimeout(function()
            {
                $('body').css('pointer-events','auto');
            },100);

            var from_app = utility.get_url_params(window.location.search,'from_app');



            // 判断页面历史记录，如果是是新打开的并且是从from_app 链接打开的，进行返回特殊处理

            if(ua.isAndroid && App.isPaiApp)
            {
                current_page.$el.children('main').off('swiperight');

                if(App.isPaiApp)
                {
                    window._IS_NEW_WEB_VIEW = true;

                    current_page.$el.find('[data-role=page-back]').off('tap');

                    current_page.$el.find('[data-role=page-back]')
                        .on('tap',function(event)
                        {
                            console.log('测试 点击返回按钮');

                            var target = event.target;
                            var targetNodeName = target.nodeName;

                            hide_key_board(targetNodeName);

                            App.app_back();

                            return false;

                        });



                }
            }
            else if(ua.isIDevice && App.isPaiApp)
            {
                if(current_page.$el.attr('data-page-index') == 0 && from_app)
                {
                    window._IS_NEW_WEB_VIEW = false;

                    current_page.$el.find('[data-role=page-back]').off('tap');


                    current_page.$el.find('[data-role=page-back]')
                        .on('tap',function(event)
                        {
                            console.log('测试 点击返回按钮');

                            if(App.isPaiApp)
                            {
                                var target = event.target;
                                var targetNodeName = target.nodeName;

                                hide_key_board(targetNodeName);

                                App.app_back();
                            }

                            return false;

                        });

                    current_page.$el.children('main').off('swiperight');
                    current_page.$el.children('main').on('swiperight',function(ev)
                    {
                        var $target = $(ev.target).parent('[data-prevent-scroll="slider"]');

                        if($target.length>0)
                        {
                            return;
                        }

                        if(App.isPaiApp)
                        {
                            hide_key_board();

                            App.app_back();
                        }

                        return false;
                    });
                }
                // ios 有参数传递过来，直接跳转到指定页
                else if(window._AppPageName)
                {
                    window._IS_NEW_WEB_VIEW = false;

                    current_page.$el.find('[data-role=page-back]').off('tap');

                    current_page.$el.find('[data-role=page-back]')
                        .on('tap',function(event)
                        {
                            console.log('测试 点击返回按钮');

                            var target = event.target;
                            var targetNodeName = target.nodeName;

                            hide_key_board(targetNodeName);

                            App.switchtopage({page:window._AppPageName});

                            return false;

                        });

                    current_page.$el.children('main').off('swiperight');
                    current_page.$el.children('main').on('swiperight',function(ev)
                    {
                        var $target = $(ev.target).parent('[data-prevent-scroll="slider"]');

                        if($target.length>0)
                        {
                            return;
                        }

                        if(App.isPaiApp)
                        {
                            hide_key_board();

                            App.switchtopage({page:window._AppPageName});
                        }

                        return false;
                    });
                }
            }

            if(!(current_page.$el.attr('data-page-index') == 0 && from_app))
            {
                window._IS_NEW_WEB_VIEW = false;
            }



            // 头部滑下问题
            header_obj = null;



            var slash_pos = url_hash.indexOf('/');
            if(slash_pos>0) url_hash = url_hash.substr(0,slash_pos);

            if($.inArray(url_hash, global_config.fixed_header_pos_page) != -1)
            {
                var current_page_view = page_control.return_current_page_view();

                header_obj = current_page_view.$el.find('[data-role="slide-header"]');

                animating_end()
            }

            var hash = utility.getHash();

            if(App.isPaiApp)
            {
                //App.remove_front();

                //console.log('%cApp.remove_front','color:#f90');

                // 设置后退按钮是否可用
                if (ua.isAndroid)
                {
                    window.AppCanPageBack = !window._rootHash[hash];

                    console.log(window._rootHash);
                }


                // 判断版本 出底部的bar
                App.save_device(true,function(data)
                {
                    if(data.appver>'1.0.0')
                    {

                        // 特殊处理 从支付宝跳转过来的页面
                        if(/mine\/payment_no_/.test(hash))
                        {
                            hash = 'mine';
                        }

                        // 显示底部的路由
                        if(global_config._show_footer_hash[hash])
                        {
                            App.show_bottom_bar(1);
                        }
                        else
                        {
                            App.show_bottom_bar(0)
                        }
                    }

                })

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



   
    // 设置在电脑打开app版本
    if(utility.storage.get('open_app_by_pc'))
    {
        App.isPaiApp = false;
    }


    // 要区分app 和 非app
    if(App.isPaiApp)
    {
        App.check_login(function(ret)
        {
            if(ret.locationid)
            {
                utility.storage.set("location_id",ret.locationid);

                cookie.set("yue_location_id",ret.locationid);
            }


            var local_user = utility.storage.get("user");
            if (local_user && local_user.user_id)
            {
                utility.user.set(local_user);
            }


            // 已经登录
            utility.user.once("success:get_user_info_by_app:fetch", function(reponse)
            {
                var data = reponse.result_data.data;

                // 校对客户端的用户id 和 服务器端的id是否一致
                if(data && utility.int(data.user_id) == utility.int(ret.pocoid))
                {


                    utility.user.set(data);

                    utility.login_id = ret.pocoid;

                    // 确保调用登录App接口
                    var icon = utility.user.get('user_icon');

                    var params =
                        {
                            pocoid : utility.login_id,
                            username : utility.user.get('nickname'),
                            icon : icon,
                            token : data.app_access_token,
                            token_expirein : data.app_expire_time,
                            role : utility.user.get('role')

                        };

                    console.log('========app初始化调用App login 参数========');
                    console.log(params);

                    if(App.isPaiApp)
                    {
                        App.login(params);
                        // 保存设备信息
                        App.save_device();

                    }
                }
                else
                {
                    utility.login_id = 0;
                }
                /*else
                {


                    var debug_txt = '请求返回的poco_id :'+utility.int(data.user_id)+'/r/n 客户端的poco_id:'+utility.int(ret.pocoid);

                    utility.err_log(1,false,debug_txt);

                    m_alert.show('登录账号异常，已退出当前账号 ','error');

                    utility.login_id = 0;



                }*/
            }).once("complete:get_user_info_by_app:fetch", function(xhr, state) {
                state == "success" || utility.user.get_user_info_by_app();
            }).get_user_info_by_app();

            page_control.route_start();

            App.remove_front();

        });


    }
    else
    {
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
    }




    function _show_upload_progress(progress)
    {
        m_alert.show(progress,'right',{delay:500});
    }

    function _show_upload_finish()
    {
        m_alert.show('上传完毕','right',{delay:1000});
    }

    function hide_key_board(targetNodeName)
    {
        var activeElement = document.activeElement;
        var activeElementNodeName = activeElement.nodeName;

        if(targetNodeName)
        {
            if (targetNodeName !== activeElementNodeName && (activeElementNodeName === 'INPUT' ||activeElementNodeName === 'TEXTAREA'))
            {

                activeElement.blur();

            }
        }
        else
        {
            activeElement.blur();
        }


    }




});