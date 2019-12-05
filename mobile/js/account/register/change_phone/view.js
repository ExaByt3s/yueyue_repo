/**
 * Created by nolest on 2014/9/18.
 */

define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../../../frame/page_control');
    var View = require('../../../common/view');
    var change_phone_tpl = require('./tpl/main.handlebars');
    var utility = require('../../../common/utility');
    var Scroll = require('../../../common/scroll');
    var m_alert = require('../../../ui/m_alert/view');

    var change_phone_view = View.extend
    ({

        attrs:
        {
            template: change_phone_tpl
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

                //不为数字 || 空 || 字符串中有空格时返回
                if(!parseInt(self.enter_phone.val()) || self.enter_phone.val().trim() == '' || self.enter_phone.val().trim().indexOf(" ") != -1)
                {
                    m_alert.show('手机号不对哦！');

                    return
                }

                if(self.counting)
                {
                    return
                }

                self._count_down(120);

                self.model.send_change_phone_code
                ({
                    phone : self.enter_phone.val(),
                    type : 'change_bind_sent_verify_code'
                })
            },
            'tap [data-role="finish-change"]' : function()
            {
                console.log("213")
                var self = this;

                //手机格式
                if(self.enter_phone.val().trim() == '' || self.enter_phone.val().trim().indexOf(" ") != -1)
                {
                    m_alert.show('手机号不对哦！');

                    return
                }
                //验证码格式
                if(self.enter_code.val().trim() == '' || self.enter_code.val().trim().indexOf(" ") != -1)
                {
                    m_alert.show('验证码格式不对哦！');

                    return
                }

                if(self.enter_pw.val() == '')
                {
                    m_alert.show('请输入当前密码！');

                    return
                }

                self.model.send_change_phone_request
                ({
                    phone : self.enter_phone.val(),
                    verify_code : self.enter_code.val(),
                    password : self.enter_pw.val(),
                    type : 'change_bind_phone'
                })
            }
        },
        _count_down : function(sec)
        {
            var self = this;

            self.counting = true;

            self.count_btn.html(sec);

            self.count_Interval = setInterval(function()
            {
                sec--;

                if(sec == 0)
                {
                    self._stop_count_down();
                }
                else
                {
                    self.count_btn.html(sec);
                }
            },1000)
        },
        _stop_count_down : function()
        {
            var self = this;

            clearInterval(self.count_Interval);

            self.count_btn.html('重新获取');

            self.counting = false;
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
                .on('success:change_phone',function(response,options)
                {
                    m_alert.show(self.model.toJSON().msg)

                    if(self.model.toJSON().code == 0)
                    {
                        self._stop_count_down();
                        //避免恶意反复提交请求 设置3秒
                        //self._count_down(3);
                    }
                })
                .on('complete:change_phone',function(response,options)
                {
                    m_alert.hide({delay : 1000})
                })
                .on('error:change_phone',function(response,options)
                {
                    m_alert.show(self.model.toJSON().msg);

                    self._stop_count_down()

                })
                .on('success:change_phone_request',function(response,options)
                {
                    m_alert.show(self.model.toJSON().msg);

                    console.log("change",response.result_data.data,utility.user);
                    if(response.result_data.code == 1)
                    {
                        utility.user.update_user(response.result_data.data);

                        console.log("change",response.result_data.data,utility.user);
                        //修改成功
                        page_control.navigate_to_page('mine')
                    }
                })
                .on('complete:change_phone_request',function(response,options)
                {
                    m_alert.hide({delay : 1000})
                })
                .on('error:change_phone_request',function(response,options)
                {
                    m_alert.show(self.model.toJSON().msg);

                })
        },
        _setup_scroll : function()
        {
            var self = this;

            var view_scroll_obj = Scroll(self.$container);

            self.view_scroll_obj = view_scroll_obj;
        },
        setup : function()
        {
            var self = this;

            self.$container = self.$('[data-role="container"]');

            self.counting = false;

            self.count_btn = self.$('[data-role="get_code"]');

            self.enter_phone = self.$('[data-role="enter_phone"]');

            self.enter_code = self.$('[data-role="verify_code"]');

            self.enter_pw = self.$('[data-role="enter_old_pw"]');


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

    module.exports = change_phone_view;
});