/**
 * Created by nolest on 2014/9/13.
 */

define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../../../../frame/page_control');
    var View = require('../../../../common/view');
    var enter_phone_tpl = require('./tpl/main.handlebars');
    var utility = require('../../../../common/utility');
    var Scroll = require('../../../../common/scroll');
    var m_alert = require('../../../../ui/m_alert/view');

    var enter_phone_view = View.extend
    ({
        attrs:
        {
            template: enter_phone_tpl
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
            'tap [data-role="get_code"]' : function()
            {
                var self = this;

                if(self.enter_phone.val() == '')
                {
                    alert('手机号不对哦');

                    return
                }
                if(self.from_setup_bind == 'from_setup_bind')
                {
                    if(self.counting)
                    {
                        return
                    }
                    //从设置来 未绑定手机
                    self._count_down(60);

                    self.model.send_code_request({phone: self.enter_phone.val(),type : 'bind_sent_verify_code'});
                }
                else if(self.get("templateModel").is_form_setup)
                {
                    if(self.counting)
                    {
                        return
                    }
                    //从设置来 更改
                    self._count_down(60);

                    self.model.send_code_request({phone: self.enter_phone.val(),type : 'change_bind_sent_verify_code'});
                }
                else if(self.get("templateModel").is_form_login)
                {
                    if(self.counting)
                    {
                        return
                    }
                    //从注册
                    self._count_down(60);

                    self.model.send_code_request({phone: self.enter_phone.val(),type : 'reg_sent_verify_code'});
                }
            },
            'tap [data-role="btn_save"]' : function()
            {
                //我的-保存
                var self = this;

                if(self.enter_verify_code.val() == '')
                {
                    alert('验证码不对咧！');

                    return
                }

                //从设置来更换手机
                self.model.send_bind_request
                ({
                    phone: self.enter_phone.val(),
                    verify_code : self.enter_verify_code.val(),
                    type : 'change_bind_phone'
                });

            },
            'tap [data-role="btn_login"]' : function()
            {
                //注册或我的设置来绑定手机 - 下一步
                var self = this;

                if(self.enter_verify_code.val() == '')
                {
                    alert('验证码不对咧！');

                    return
                }

                self.pass_data =
                {
                    phone: self.enter_phone.val(),
                    verify_code : self.enter_verify_code.val()
                };

                if(self.from_setup_bind == 'from_setup_bind')
                {
                    //从设置来绑定手机
                    self.model.send_setup_bind_phone
                    ({
                        phone: self.enter_phone.val(),
                        verify_code : self.enter_verify_code.val(),
                        type:'bind_phone'
                    })
                }
                else
                {
                    //从注册来绑定手机
                    self.model.send_login_setp_one
                    ({
                        phone: self.enter_phone.val(),
                        verify_code : self.enter_verify_code.val(),
                        type : 'phone_reg_step_1'
                    });
                }
            }
        },
        _count_down : function(sec)
        {
            var self = this;

            self.counting = true;

            self.count_down.html(sec);

            self.count_Interval = setInterval(function()
            {
                sec--;

                if(sec == 0)
                {
                    self.count_down.html('重新获取');

                    self._stop_count_down();

                    self.counting = false;
                }
                else
                {
                    self.count_down.html(sec);
                }
            },1000)
        },
        _stop_count_down : function()
        {
            var self = this;

            clearInterval(self.count_Interval);
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
                .on('success:fetch_code',function(response,options)
                {
                    m_alert.show(response.result_data.msg);

                })
                .on('success:fetch_bind',function(response,options)
                {
                    if(response.result_data)
                    {
                        m_alert.show(response.result_data.msg);

                        if(response.result_data.code != 0)
                        {
                            page_control.navigate_to_page("mine")
                        }
                    }
                })
                .on('success:fetch_send_login_setp',function(response,options)
                {
                    if(response.result_data)
                    {
                        m_alert.show(response.result_data.msg);

                        if(response.result_data.code != 0)
                        {
                            page_control.navigate_to_page("account/setup/bind/enter_pw/form_login",self.pass_data)
                        }
                    }

                })
                .on('success:send_setup_bind_phone',function(response,options)
                {
                    if(response.result_data)
                    {
                        m_alert.show(response.result_data.msg);

                        if(response.result_data.code != 0)
                        {
                            page_control.navigate_to_page("mine")
                        }
                    }
                })
                .on('complete:fetch_bind',function()
                {
                    m_alert.hide();
                })
                .on('complete:fetch_send_login_setp',function()
                {
                    m_alert.hide();
                })
                .on('complete:send_setup_bind_phone',function()
                {
                    m_alert.hide();
                })
        },
        _setup_scroll : function()
        {
            var self = this;

            var view_scroll_obj = Scroll(self.$container,
                {
                    is_hide_dropdown : true
                });

            self.view_scroll_obj = view_scroll_obj;
        },
        setup : function()
        {
            var self = this;

            self.$container = self.$('[data-role="container"]');

            self.count_down = self.$('[data-role="get_code"]');

            self.enter_phone = self.$('[data-role="enter_phone"]');

            self.enter_verify_code = self.$('[data-role="verify_code"]');

            self.from_setup_bind = self.get("templateModel").from_setup_bind;

            self.counting = false;

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

    module.exports = enter_phone_view;
});