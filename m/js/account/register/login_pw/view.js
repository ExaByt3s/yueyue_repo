/**
 * Created by nolest on 2014/9/18.
 */
define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../../../frame/page_control');
    var View = require('../../../common/view');
    var login_pw_tpl = require('./tpl/main.handlebars');
    var utility = require('../../../common/utility');
    var Scroll = require('../../../common/scroll');
    var App = require('../../../common/I_APP');
    var global_config = require('../../../common/global_config');
    var m_alert = require('../../../ui/m_alert/view');
    var cookie = require('cookie');

    var login_pw_view = View.extend
    ({

        attrs:
        {
            template: login_pw_tpl
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

                var pwd = $.trim(self.pw.val());

                var sec_pwd = $.trim(self.pw_sec.val());

                if(pwd != sec_pwd)
                {
                    m_alert.show('设置错误，两次输入的密码必须一致','error');

                    return;
                }

                if(utility.is_empty(pwd) || self.pw.val().length < 6 || self.pw.val().length > 32)
                {
                    m_alert.show('请输入6~32位密码');

                    return
                }

                // 跑注册流程
                var params_data = {
                    phone : self.get("data").phone,
                    verify_code : self.get("data").verify_code,
                    pwd : pwd,
                    role : 'cameraman',
                    type :'reg_act'
                };

                if(self.reg_ing)
                {
                    return;
                }

                utility.user.reg(params_data);

                /*page_control.navigate_to_page('account/front',{
                    phone : self.get("data").phone,
                    verify_code : self.get("data").verify_code,
                    pwd : pwd
                });*/

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



            // 这里表示验证码成功
            utility.user
                .on('before:reg',function()
                {
                    m_alert.show('账号正在创建中...','loading',{delay:-1});

                    self.reg_ing = true;
                })
                .on('success:reg',function(response,options)
                {
                    self.reg_ing = false;

                    m_alert.hide();

                    var response = response.result_data;

                    var msg = response.msg;

                    var code = response.code;

                    if(code == 1)
                    {
                        // 注册成功，接着要保存用户信息

                        m_alert.show(msg,'right');

                        var user_info = response.data;

                        utility.user.update_user(user_info);

                        utility.login_id = user_info.user_id;

                        window._Has_reg = true;

                        console.log(cookie.get('yueus_url2'));

                        var yueus_url2 = decodeURIComponent(cookie.get('yueus_url2'));


                        if(yueus_url2)
                        {

                            console.log(yueus_url2);

                            window.location.href = yueus_url2.replace('?','?from_reg_success=1&');


                        }
                        else
                        {
                            page_control.navigate_to_page(global_config.default_index_route);
                        }


                    }
                    else
                    {
                        m_alert.show(msg,'error');
                    }

                })
                .on('error:reg',function(response,options)
                {
                    self.reg_ing = false;

                    m_alert.show('网络异常','error')
                });

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

            self.pw = self.$('[data-role="submit_pw"]');
            self.pw_sec = self.$('[data-role="submit_pw_sec"]');
            self.$user_role_group = self.$('[data-role="user-role-container"]');

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

    module.exports = login_pw_view;
});
