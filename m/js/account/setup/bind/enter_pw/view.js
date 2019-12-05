/**
 * Created by nolest on 2014/9/13.
 */

define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../../../../frame/page_control');
    var View = require('../../../../common/view');
    var bind_tpl = require('./tpl/main.handlebars');
    var utility = require('../../../../common/utility');
    var Scroll = require('../../../../common/scroll');
    var m_alert = require('../../../../ui/m_alert/view');

    var enter_pw_view = View.extend
    ({

        attrs:
        {
            template: bind_tpl
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
            'tap [data-role="finish-reg"]' : function()
            {
                var self = this;

                if(self.pw.val() == '')
                {
                    alert('请填写密码！');

                    return
                }


                var data =
                {
                    role : utility.storage.get('user_role'),
                    type : 'phone_reg_step_2',
                    password : self.pw.val()
                };

                var submit = $.extend(true,{},data,self.get("submit_data"));


                self.model.send_data(submit);

            },
            'tap [data-role="get_code"]' : function()
            {
                var self = this;

                var phone_reg = new RegExp(/^[0-9]{11}$/);

                if(!phone_reg.test(self.phone.val().trim()))
                {
                    m_alert.show('请正确填写手机号！','error',{delay:1000});

                    return
                }

                var data =
                {
                    reset : 1,
                    type : 'bind_sent_verify_code',
                    phone : self.phone.val()
                };

                if(self.counting)
                {
                    return
                }

                self._count_down(120);

                self.model.get_reset_code(data);

            },
            'tap [data-role="finish-change"]' : function()
            {
                var self = this;

                var pw_cn = new RegExp(/^[\u4E00-\u9FA5]+$/);

                var phone_reg = new RegExp(/^[0-9]{11}$/);

                if(!phone_reg.test(self.phone.val().trim()))
                {
                    m_alert.show('请正确填写手机号！','error')

                    return
                }

                if(self.phone.val().trim() == '')
                {
                    m_alert.show('手机号不能为空！','error');

                    return
                }


                if(self.new_pw.val().trim() == '')
                {
                    //m_alert.show('请填写新的密码！');
                    m_alert.show('请填写新的密码！','error',{delay:1000});
                    return
                }

                if(pw_cn.test(self.new_pw.val().trim()))
                {
                    //m_alert.show('新密码不能含有汉字','error');
                    m_alert.show('新密码不能含有汉字!','error',{delay:1000});

                    return

                }

                if(self.new_pw.val().trim() == '')
                {
                    //m_alert.show('新密码不能为空！');
                    m_alert.show('新密码不能为空!','error',{delay:1000});
                    return
                }

                if(self.new_pw.val().trim().length < 6 || self.new_pw.val().trim().length > 32)
                {
                    //m_alert.show('密码长度6~32位','error');
                    m_alert.show('密码长度6~32位!','error',{delay:1000});

                    return

                }



                console.log(utility.user)


                var data =
                {
                    phone : self.phone.val(),
                    pwd : self.new_pw.val().trim(),
                    verify_code : self.code.val(),
                    type : 'reset_pwd'
                };

                self.model.send_change_pw(data);
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
                .on('success:fetch',function(response,options)
                {
                    m_alert.show(response.result_data.msg);

                    //设置密码成功后导航到热门
                    if(response.result_data.code != 0 )
                    {
                        page_control.navigate_to_page("hot")
                    }
                })
                .on('complete:fetch',function(response,options)
                {

                })
                .on('success:fetch_change_pw',function(response,options)
                {
                    //m_alert.show(response.result_data.msg,'right');
                    var res = response.result_data
                    if(res.code != 0 )
                    {
                        m_alert.show(response.result_data.msg,'right');
                        setTimeout(function()
                        {
                            page_control.navigate_to_page("mine")
                        },1500);
                    }
                    else if(res.code == 0)
                    {
                        m_alert.show(res.msg,'error')
                    }

                    // if(response.result_data.code != 0 )
                    // {
                    //     setTimeout(function()
                    //     {
                    //         page_control.navigate_to_page("mine")
                    //     },1500)
                    // }
                })
                .on('error:fetch_change_pw',function(response,options)
                {
                    m_alert.show('提交失败，请重试','error')

                })
                .on('complete:error_change_pw',function(response,options)
                {
                    m_alert.show('修改失败，请重试','error');
                })
                .on('success:get_reset_code',function(response,options)
                {
                    var res = response.result_data

                    if(res.code == 1)
                    {
                        m_alert.show(res.msg,'right')
                    }
                    else if(res.code == 0)
                    {
                        self._stop_count_down();

                        m_alert.show(res.msg,'error')
                    }
                })
                .on('error:get_reset_code',function(response,options)
                {
                    m_alert.show('获取失败，请重试','error')

                    self._stop_count_down();
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
        setup : function()
        {
            var self = this;

            self.$container = self.$('[data-role="container"]');

            self.pw = self.$('[data-role="submit_pw"]');

            self.phone = self.$('[data-role="enter_phone"]');

            self.code = self.$('[data-role="enter_code"]');

            self.new_pw = self.$('[data-role="new_pw"]');

            self.count_btn = self.$('[data-role="get_code"]');

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

    module.exports = enter_pw_view;
});