/**
 * Created by nolest on 2014/9/18.
 */
define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../../../frame/page_control');
    var View = require('../../../common/view');
    var global_config = require('../../../common/global_config');
    var login_pw_tpl = require('./tpl/main.handlebars');
    var utility = require('../../../common/utility');
    var Scroll = require('../../../common/scroll');
    var App = require('../../../common/I_APP');
    var m_alert = require('../../../ui/m_alert/view');
    var choosen_group_view = require('../../../widget/choosen_group/view');

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

                if(self.get('data') && self.get('data').is_model_no_pwd)
                {
                    utility.ajax_request
                    ({
                        url : global_config.ajax_url.reg,
                        data :
                        {
                            phone : self.get("data").phone,
                            verify_code : self.get("data").verify_code,
                            pwd : pwd,
                            type : 'reset_pwd'
                        },
                        beforeSend : function()
                        {
                            m_alert.show('加载中...','loading',{delay:-1});
                        },
                        success : function(res)
                        {
                            m_alert.hide();

                            var data = res.result_data;

                            var user_info = res.result_data.data;

                            if(user_info)
                            {
                                utility.user.set(user_info);
                                utility.login_id = user_info.user_id;

                                var params =
                                {
                                    pocoid : utility.login_id,
                                    username : utility.user.get('nickname'),
                                    token : res.result_data.app_access_token,
                                    token_expirein : res.result_data.app_expire_time,
                                    icon : utility.user.get('user_icon'),
                                    role : utility.user.get('role')

                                };

                                console.log('========登录调用App login 参数========');
                                console.log(params);

                                if(App.isPaiApp)
                                {
                                    App.login(params);
                                    // 保存设备信息
                                    App.save_device();

                                }
                            }

                            if(data.code == 1)
                            {
                                page_control.navigate_to_page('model_date/model_card/1');
                            }
                            else
                            {
                                m_alert.show(data.msg,'error');
                            }
                        },
                        error : function()
                        {
                            m_alert.show('网络异常','error');
                        }
                    });
                }
                else
                {
                    page_control.navigate_to_page('account/front',{
                        phone : self.get("data").phone,
                        verify_code : self.get("data").verify_code,
                        pwd : pwd
                    });
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
