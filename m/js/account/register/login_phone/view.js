/**
 * Created by nolest on 2014/9/18.
 */

define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../../../frame/page_control');
    var View = require('../../../common/view');
    var login_phone_tpl = require('./tpl/main.handlebars');
    var utility = require('../../../common/utility');
    var Scroll = require('../../../common/scroll');
    var m_alert = require('../../../ui/m_alert/view');
    var m_select = require('../../../ui/m_select/view');
    var App = require('../../../common/I_APP');

    var login_phone_view = View.extend
    ({

        attrs:
        {
            template: login_phone_tpl
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
                    m_alert.show('手机号不对哦！','error');

                    return
                }

                if(self.counting)
                {
                    return
                }

                self._count_down(120);

                self.model.send_code_request
                ({
                    phone : self.enter_phone.val(),
                    type : 'bind_sent_verify_code'
                })
            },
            'tap [data-role="btn_login"]' : function()
            {
                var self = this;

                //手机格式
                if(self.enter_phone.val().trim() == '' || self.enter_phone.val().trim().indexOf(" ") != -1)
                {
                    m_alert.show('手机号不对哦！','error');

                    return
                }
                //验证码格式
                if(self.enter_code.val().trim() == '' || self.enter_code.val().trim().indexOf(" ") != -1)
                {
                    m_alert.show('验证码格式不对哦！','error');

                    return
                }

                m_alert.show('提交中...','loading');

                if(self.can_login)
                {
                    // 跑登录流程

                    var action_type = 'login_by_phone';
                }
                else
                {

                    var action_type = 'reg_act';
                }

                self.model.send_phone_and_code
                ({
                    phone : self.enter_phone.val(),
                    verify_code : self.enter_code.val(),
                    type : action_type,
                    role : window._role || 0
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

            self.on('render',function()
                {
                    self._setup_scroll();
                });

            self.model.on('success:fetch_code',function(response,options)
                {


                    var code = self.model.toJSON().code;

                    switch (code)
                    {
                        case 0:
                            self._stop_count_down();

                            m_alert.show(self.model.toJSON().msg,'right');
                            break;
                        case 1 :
                            // 用于注册
                            self.can_login = false;
                            break;
                        case 2:

                            self.can_login = true;

                            if(response.result_data.user_info)
                            {


                                /*m_alert.show('登录成功','right');

                                utility.user.update_user(response.result_data.user_info);

                                page_control.navigate_to_page('mine');*/
                            }
                            break;
                    }

                })
                .on('complete:fetch_code',function(response,options)
                {
                    //m_alert.hide({delay : 1000})
                })
                .on('error:fetch_code',function(response,options)
                {
                    m_alert.show(self.model.toJSON().msg,'error');

                    self._stop_count_down()
                })
                .on('success:phone_and_code',function(response,options)
                {
                    var response = response.result_data;

                    m_alert.show(response.msg,'right');

                    switch (response.code)
                    {
                        // 登录错误
                        case 0:
                            break;
                        case 1:
                            if(self.can_login)
                            {
                                m_alert.show('登录成功','right');

                                utility.user.update_user(response.data);

                                utility.login_id = response.data.user_id;

                                var icon = response.data.user_icon;

                                var is_go_to_edit = response.is_go_to_edit;

                                var params =
                                {
                                    pocoid : utility.login_id,
                                    username : utility.user.get('nickname'),
                                    icon : icon

                                };

                                if(App.isPaiApp)
                                {
                                    App.login(params);
                                    // 保存设备信息
                                    App.save_device();

                                }

                                // 导航到补充资料
                                if(is_go_to_edit)
                                {
                                    if(utility.user.get('role') == 'model')
                                    {
                                        page_control.navigate_to_page('model_date/model_card/'+utility.login_id+'/1');
                                    }
                                    else
                                    {
                                        page_control.navigate_to_page('mine/profile/'+utility.login_id+'/1');
                                    }
                                }
                                else
                                {
                                    page_control.navigate_to_page('mine');
                                }

                            }
                            else
                            {
                                // 跑绑定流程

                                //手机号和密码提交成功
                                page_control.navigate_to_page('account/register/login_pw',options.data)

                                /*m_alert.show(self.model.toJSON().msg,'right');

                                if(self.model.toJSON().code == 1)
                                {
                                    //手机号和密码提交成功
                                    page_control.navigate_to_page('account/register/login_pw',options.data)
                                }*/
                            }
                            break;
                        // 选角色
                        case -1 :
                            setTimeout(function()
                            {
                                self.select_role.show();
                            },1100);
                            break;

                    }

                    /*if(self.can_login)
                    {
                        m_alert.show('登录成功','right');

                        utility.user.update_user(response.result_data.data);

                        utility.login_id = response.result_data.data.user_id;

                        page_control.navigate_to_page('mine');
                    }
                    else
                    {
                        // 跑绑定流程

                        m_alert.show(self.model.toJSON().msg,'right');

                        if(self.model.toJSON().code == 1)
                        {
                            //手机号和密码提交成功
                            page_control.navigate_to_page('account/register/login_pw',options.data)
                        }
                    }*/



                })
                .on('complete:phone_and_code',function(response,options)
                {
                    //m_alert.hide({delay : 1000})

                })
                .on('error:phone_and_code',function(response,options)
                {
                    m_alert.show(self.model.toJSON().msg,'error')
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

            self._setup_events();

            self._setup_select_role();
        },
        render : function()
        {
            var self = this;

            View.prototype.render.apply(self);

            self.trigger('render');

            return self;
        },
        /**
         * 配置角色选择
         * @private
         */
        _setup_select_role : function()
        {
            var self = this;

            var arr = [
                {value:'',text:'请选择角色',selected:true},
                {value:'model',text:'模特'},
                {value:'cameraman',text:'摄影师'}
            ];

            self.select_role = new m_select
            ({
                templateModel :
                {
                    options : [arr]

                },
                parentNode: self.$el
            }).render();

            self.select_role.on('confirm:success',function(arr)
            {

                var role = arr[0].value;

                if(!role )
                {
                    m_alert.show('请选择角色','right');

                    return;
                }


                if(self.can_login)
                {
                    // 跑登录流程

                    var action_type = 'login_by_phone';
                }
                else
                {

                    var action_type = 'phone_reg_step_1';
                }

                self.model.send_phone_and_code
                ({
                    phone : self.enter_phone.val(),
                    verify_code : self.enter_code.val(),
                    type : action_type,
                    role : role
                })
            });
        }
    });

    module.exports = login_phone_view;
});