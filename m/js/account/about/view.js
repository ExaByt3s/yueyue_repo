/**
 * 首页 - 城市选择
 * 汤圆 2014.18.
 */
define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var View = require('../../common/view');
    var global_config = require('../../common/global_config');
    var agreement = require('../about/tpl/main.handlebars');
    var utility = require('../../common/utility');
    var templateHelpers = require('../../common/template-helpers');
    var Scroll = require('../../common/scroll');
    var m_alert = require('../../ui/m_alert/view');
    var App = require('../../common/I_APP');
    var debug_tap =0;

    var agreement_view = View.extend
    ({

        attrs:
        {
            template: agreement
        },
        events :
        {
            'swiperight' : function (){
                //page_control.back();
            },
            'tap [data-role=page-back]' : function (ev)
            {
                var self = this;

        
                page_control.back();
            },
            'tap [data-role=btn-go-to]' : function (ev)
            {
                var self = this;

                self.trigger('receive');

                self.hide();

            },
            /**
             * 支付宝支付，只供测试使用
             */
            'tap [data-role="alipay_purse"]' : function()
            {

                var self = this;

                m_alert.show('请求中','loading',{delay:-1});

                utility.ajax_request
                ({
                    url : global_config.ajax_url.test_wx_pay,
                    data : {third_code : 'alipay_purse'},
                    success : function(params)
                    {
                        m_alert.hide();

                        App.alipay
                        ({
                            alipayparams : params.data,
                            payment_no : params.payment_no

                        },function(res)
                        {
                            console.log(res);
                        });
                    },
                    error : function()
                    {
                        m_alert.show('网络异常','error');
                    }

                });
            },
            /**
             * 微信支付,只供测试使用
             */
            'tap [data-role="wxpay-btn"]' : function()
            {

                var self = this;

                m_alert.show('请求中','loading',{delay:-1});

                utility.ajax_request
                ({
                    url : global_config.ajax_url.test_wx_pay,
                    data : {third_code : 'tenpay_wxapp'},
                    success : function(data)
                    {
                        m_alert.hide();

                        App.wxpay(data);
                    },
                    error : function()
                    {
                        m_alert.show('网络异常','error');
                    }

                });
            },
            'hold [data-role="cache-ver"]' : function()
            {
                var self = this;

                if(App.isPaiApp)
                {
                    App.save_device(true,function(data)
                    {
                        var debugmode = data.debugmode;

                        if(debugmode == 'release')
                        {
                            var app_mode = '正式版';
                        }
                        else if(debugmode == 'preview')
                        {
                            var app_mode = '预览版';
                        }
                        else if(debugmode == 'debugurl')
                        {
                            var app_mode = '调试版';
                        }

                        alert(data.os + "\r\n osver=" + data.osver + "\r\n appver=" + data.appver + "\r\n cachever="+data.cachever+"\r\n"+'当前模式:'+app_mode);
                    });
                }
            },
            'tap [data-role="logo"]' : function()
            {
                if(!App.isPaiApp)
                {
                   return;
                }

                debug_tap++;

                if(debug_tap == 10)
                {
                    alert('调试版');

                    App.debug
                    ({
                        cache : false,
                        debug : true
                    });

                    utility.storage.set('app-mode','调试版');

                }
                else if(debug_tap == 15)
                {
                    alert('预览版');

                    App.debug
                    ({
                        cache : true,
                        debug : true
                    });

                    utility.storage.set('app-mode','预览版');

                }
                else if(debug_tap == 20)
                {
                    alert('正式版');

                    App.debug
                    ({
                        cache : true,
                        debug : false
                    });

                    utility.storage.set('app-mode','正式版');

                    debug_tap = 0;
                }
            }

        },
       
        /**
         * 安装事件
         * @private
         */
        _setup_events : function()
        {

            var self = this; 
            self._setup_scroll()
            //.once('render',self._render_after,self);
            self.view_scroll_obj.refresh();
        },

        /**
         * 安装滚动条
         * @private
         */
        _setup_scroll : function()
        {
            var self = this;
            var view_scroll_obj = Scroll(self.$container,
                {
                    is_hide_dropdown : true
                });
            self.view_scroll_obj = view_scroll_obj;
        },
        /**
         * 安装底部导航
         * @private
         */
       
        _reset : function()
        {
            var self = this;
            
        },
        _add_one : function(model)
        {
            var self = this;
            
        },
        

        /**
         * 视图初始化入口
         */
        setup : function()
        {

            var self = this;

            // 配置交互对象
            self.$container = self.$('[data-role=container]'); // 滚动容器

            // 版本号
            if(App.isPaiApp)
            {
                App.save_device(true,function(data)
                {
                    var debugmode = data.debugmode;

                    if(debugmode == 'release')
                    {
                        var app_mode = '';

                    }
                    else if(debugmode == 'preview')
                    {
                        var app_mode = '预览版';

                    }
                    else if(debugmode == 'debugurl')
                    {
                        var app_mode = '调试版';

                    }

                    var cache_ver_str = data.cachever;

                    self.$('[data-role="app-ver"]').html('V'+data.appver+" "+app_mode+" (" +cache_ver_str+")");

                    // 测试，用于微信支付
                    // modify by hudw 2014.12.2
                    var debugmode = data.debugmode;

                    if(debugmode == 'preview')
                    {
                        self.$('[data-role="alipay_purse"]').removeClass('fn-hide');
                        self.$('[data-role="wxpay-btn"]').removeClass('fn-hide');
                    }
                    else
                    {
                        self.$('[data-role="alipay_purse"]').addClass('fn-hide');
                        self.$('[data-role="wxpay-btn"]').addClass('fn-hide');
                    }


                })
            }

            // 安装事件
            self._setup_events();

        },
        render : function()
        {
            var self = this;

            // 调用渲染函数
            View.prototype.render.apply(self);

            self.trigger('render');

            return self;
        },
        show : function()
        {
            var self = this;

            self.$el.removeClass('fn-hide');

            self.refresh();
        },
        hide : function()
        {
            var self = this;

            self.$el.addClass('fn-hide');

            self.refresh();
        },
        refresh : function()
        {
            var self = this;

            self.view_scroll_obj && self.view_scroll_obj.refresh();
        }

    });

    module.exports = agreement_view;
});