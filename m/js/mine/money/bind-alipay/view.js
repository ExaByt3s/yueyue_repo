/**
 * 绑定支付宝
 * 汤圆 2014.11.20       
 */
define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../../../frame/page_control');
    var View = require('../../../common/view');
    var bind_alipay = require('../bind-alipay/tpl/main.handlebars');
    var utility = require('../../../common/utility');
    var templateHelpers = require('../../../common/template-helpers');
    var Scroll = require('../../../common/scroll');
    var global_config = require('../../../common/global_config');
    var number_btn_view = require('../../../widget/number_btn/view');
    var m_alert = require('../../../ui/m_alert/view');
    var no_bind = require('../bind-alipay/tpl/no-bind.handlebars');
    var has_bind = require('../bind-alipay/tpl/has-bind.handlebars');

    var bind_alipay_view = View.extend
    ({

        attrs:
        {
            template: bind_alipay
        },
        events :
        {
            'swiperight' : function ()
            {
                page_control.back();
            },
            'tap [data-role=page-back]' : function (ev)
            {
                page_control.back();
            },
            'tap [data-role="btn"]' : function (ev)
            {

                var self = this;
                self.alipay_account_val = self.$('[data-role="alipay-account"]').val().trim();
                self.alipay_name_val = self.$('[data-role="alipay-name"]').val().trim();

                if( self.alipay_account_val == '' || !self.alipay_account_val )
                {
                    m_alert.show('请输入支付宝账号！','error',{delay:1000});
                    return ;
                }
                if( self.alipay_name_val == '' || !self.alipay_name_val )
                {
                    m_alert.show('请输入姓名！','error',{delay:1000});
                    return ;
                }

                //收据收集
                var data = 
                {
                    third_account : self.alipay_account_val,
                    real_name : self.alipay_name_val,
                    type : 'bind_act'
                }

                self.model.send_bind(data);
            }
        },

        //安装滚动条
        _setup_scroll : function()
        {
            var self = this;
            var view_scroll_obj = Scroll(self.$container,{
                is_hide_dropdown : true
            });
            self.view_scroll_obj = view_scroll_obj;
        },

        setup : function()
        {
            var self = this;
            self.$container = self.$('[data-role="container"]'); // 滚动容器
            self.$alipay_account = self.$('[data-role="alipay-account"]'); 
            self.$alipay_name = self.$('[data-role="alipay-name"]');
            self.$main_option = self.$('[data-role="main-option"]');
            self.setup_event();
            self._setup_scroll()

            //检查是否绑定
            self.model.check_bind({
                type : 'check_bind'
            });

            //刷新滚动条
            self.on('update_list',function(response,xhr)
            {

                // 区分当前对象
                var self = this;
                if(!self.view_scroll_obj)
                {
                    self._setup_scroll();
                }
                self.view_scroll_obj.refresh();
            })

        },

        //安装事件
        setup_event : function()
        {
            var self = this;

            //检查是否绑定
            self.model
                .on('before:check_bind:fetch',function(response,options)
                {
                    m_alert.show('加载中...','loading',{delay:1000});
                })
                .on('success:check_bind:fetch',function(response,options)
                {
                    var data = response.result_data;
                    self.render_html(data);
                })
                .on('error:check_bind:fetch',function()
                {
                    m_alert.show('网络不给力,请返回重试！','error')
                })
                .on('complete:check_bind:fetch',function(response,options)
                {
                    //m_alert.hide();
                })

            //发送绑定
            self.model
                .on('before:send_bind:fetch',function(response,options)
                {
                    m_alert.show('加载中...','loading',{delay:1000});
                })
                .on('success:send_bind:fetch',function(response,options)
                {
                    var data = response.result_data;
                    self.render_has_bind(data);
                })
                .on('error:send_bind:fetch',function()
                {
                    m_alert.show('网络不给力,请返回重试！','error')
                })
                .on('complete:send_bind:fetch',function(response,options)
                {
                    //m_alert.hide();
                })

        },
        render_html : function (data)
        {
            var self = this;
            var sate = data.code;
                switch (sate)
                {
                    case 0: 
                        self.$main_option.html(no_bind);
                        break;
                    case 1: 
                        //匹配支付宝星星转换
                        var str = data.data.third_account;
                        self.alipay_account_val = self.reg_alipay_account(str);
                        self.render_has_bind(data);
                        break;
            }
            //self.trigger('update_list');
        },
        render_has_bind : function(data)
        {
            var self = this;
            //支付宝星星匹配
            var ret = self.reg_alipay_account(self.alipay_account_val);
            var has_bind_html = has_bind({
                data : data,
                alipay_account : ret
            })
            self.$main_option.html(has_bind_html);;
        },

        //支付宝星星匹配
        reg_alipay_account : function (str) 
        {
            var self = this;
            var str = str.toString();
            var email = new RegExp(/\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/);
            var email_test = email.test(str);
            if (email_test)
            {
                var ret = str.replace(/[\d\w]{1,4}@{1}/g, "****@");
                return ret;
            }
            else
            {
                var ret = str.replace(/[\d]{4}([\d]{4})$/g, "****$1");
                return ret;
            }
        },

        render : function()
        {
            var self = this;
            View.prototype.render.apply(self);
            self.trigger('render');
            return self;
        }

    });

    module.exports = bind_alipay_view;
});