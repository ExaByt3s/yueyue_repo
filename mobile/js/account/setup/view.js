/**
 * Created by nolest on 2014/9/13.
 */

define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var View = require('../../common/view');
    var setup_tpl = require('./tpl/main.handlebars');
    var utility = require('../../common/utility');
    var Scroll = require('../../common/scroll');
    var system = require('../../common/system');
    var App = require('../../common/I_APP');
    var m_alert = require('../../ui/m_alert/view');
    var global_config = require('../../common/global_config');


    var setup_view = View.extend
    ({

        attrs:
        {
            template: setup_tpl
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
            'hold [data-role="transit"]' : function (ev)
            {
                window.location.href = 'http://yp.yueus.com/mobile/test/transit.html'
            },
            'tap [data-role=clear-cache]' : function (ev)
            {
                App.clear_cache();
            },

            'tap [data-role=bind-alipay]' : function (ev)
            {
                page_control.navigate_to_page('mine/money/bind_alipay')
            },
            
            
            'tap [data-role="bind-phone"]' : function()
            {
                var self = this;

                if(!utility.auth.is_login())
                {
                    page_control.navigate_to_page('account/login');

                    return;
                }

                if( !utility.user.get("phone") || utility.user.get("phone") == '')
                {
                    //从设置绑定手机号
                    page_control.navigate_to_page('account/register/bind_phone')

                }
                else
                {
                    //更换绑定手机号
                    page_control.navigate_to_page("account/register/change_phone")

                }

            },
            'tap [data-role="undate-pw"]' : function()
            {
                var self = this;

                if(!utility.auth.is_login())
                {
                    page_control.navigate_to_page('account/login');

                    return;
                }

                page_control.navigate_to_page("account/setup/bind/enter_pw/form_setup")
            },
            'tap [data-role="setting"]' : function()
            {

                page_control.navigate_to_page("account/setting")
            },
            'tap [data-role="about"]' : function()
            {
                
                page_control.navigate_to_page("account/about")

            },
            'tap [data-role="logout"]' : function()
            {

                if(confirm('是否退出'))
                {
                    m_alert.show('正在退出','loading');

                    system.logout
                    ({
                        success : function(data)
                        {

                            m_alert.hide();

                        },
                        error : function()
                        {
                            m_alert.show('网络异常，退出失败','error');
                        }
                    });
                }


            },
            'tap [data-role="ver-num"]' : function()
            {

            },
            'tap [data-role="check-ver"]' : function()
            {
                var self = this;

                console.log('检查版本');

                if(App.isPaiApp)
                {
                    App.check_update();
                }
            }
        },
        _setup_events : function()
        {
            var self = this;
            self
                .on('render',function()
                {
                    self._setup_scroll();
                });
            self.model
                .on('success:fetch_sms',function(response,options)
                {

                })
                .on('complete:fetch_sms',function(response,options)
                {

                })
                .on('success:fetch_submit',function(response,options)
                {

                })
                .on('complete:fetch_submit',function(response,options)
                {

                })
        },
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

            self.$container = self.$('[data-role="container"]');

            self.phone_num = self.$('[data-role="phone-num"]');

            self.phone_num.html(utility.user.get("phone"));

            self.$ver_num = self.$('[data-role="ver-num"]').find('label');

            self.$log_out_btn = self.$('[data-role="logout"]');

            if(App.isPaiApp)
            {
                App.save_device(true,function(data)
                {
                    self.$ver_num.html(data.cachever);
                });
            }
            else
            {
                self.$ver_num.html('0.1');
            }


            // *************退出按钮*********************
            if(!utility.auth.is_login())
            {
                self.$log_out_btn.addClass('fn-hide');
                self.$('[data-role="bind-alipay"]').addClass('fn-hide');
                self.$('[data-role="undate-pw"]').addClass('fn-hide');

            }
            else
            {
                self.$log_out_btn.removeClass('fn-hide');
            }

            // 检查是否有红点更新
            self.$appver_update_point = self.$('[data-appver-update-point]');

            App.isPaiApp && App.app_info(function(data)
            {
                if(data.appupdate == 1)
                {
                    self.$appver_update_point.removeClass('fn-hide');
                }
                else
                {
                    self.$appver_update_point.addClass('fn-hide');
                }
            });


            self._setup_events();
        },
        render : function()
        {
            var self = this;

            View.prototype.render.apply(self);

            self.trigger('render');

            return self;
        }
    });

    module.exports = setup_view;
});