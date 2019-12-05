/**
 * 登录 视图
 * hudw 2014.9.3
 */
define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var View = require('../../common/view');
    var tpl = require('../login/tpl/main.handlebars');
    var utility = require('../../common/utility');
    var Scroll = require('../../common/scroll');
    var global_config = require('../../common/global_config');
    var m_alert = require('../../ui/m_alert/view');
    var m_select = require('../../ui/m_select/view');
    var App = require('../../common/I_APP');
    var system = require('../../common/system');


    var mine_view = View.extend
    ({
        attrs:
        {
            template: tpl
        },
        events : {

            'tap [data-role=page-back]': function (ev)
            {
                page_control.back();
            },
            /**
             * 提交登录
             */
            'tap [data-role="submit-btn"]': function ()
            {
                var self = this;

                var user_name = self.$user_name.val();
                var user_pwd = self.$user_pwd.val();

                if(self.logining)
                {
                    return;
                }

                // 这里要加验证提示

                self.login(user_name,user_pwd,window._role,window._role?true:false);
            },
            'tap [data-role="phone"]' :function()
            {
                page_control.navigate_to_page("account/register/login_phone")
            },
            'tap [data-role="nav-to-agreement-login"]' : function()
            {
                var self = this;

                page_control.navigate_to_page('account/agreement_login');
            },
            'tap [data-role="reg"]' : function()
            {
                var self = this;

                page_control.navigate_to_page('account/register/reg');
            },
            'tap [data-role="forget-pwd"]' : function()
            {
                page_control.navigate_to_page('account/setup/bind/enter_pw/form_setup');
            }
        },

        /**
         * 安装事件
         * @private
         */
        _setup_events : function()
        {
            var self = this;

            self.on('before:login:fetch',function(response,xhr)
            {
                self.logining = true;

                m_alert.show('登录中...','loading');

            }).on('success:login:fetch',function(response,xhr)
            {

                self.logining = false;

                m_alert.show(response.result_data.msg,'right',{delay:1000});

                // 有角色时
                if(response.result_data.result == 200)
                {
                    utility.user.update_user(response.result_data.user_info);

                    utility.login_id = response.result_data.user_id;

                    var icon = utility.user.get('user_icon');

                    var params =
                    {
                        pocoid : utility.login_id,
                        username : utility.user.get('nickname'),
                        token : response.result_data.app_access_token,
                        token_expirein : response.result_data.app_expire_time,
                        icon : icon,
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

                    var is_first_time_open_app = window.isFirstTimeOpenApp;

                    // 第一次启动
                    if(is_first_time_open_app)
                    {
                        if(App.isPaiApp)
                        {
                            App.switchtopage({page:'hot'});
                        }
                        else
                        {
                            page_control.navigate_to_page('hot');
                        }

                    }
                    else
                    {
                        // app页面跳转到登录页面
                        if(window._AppPageName)
                        {
                            var page_name = window._AppPageName;

                            App.isPaiApp && App.switchtopage({page:page_name});


                        }
                        else
                        {
                            if(utility.int(self.get('type_params')) == 1)
                            {
                                // 1 代表不可返回

                                if(App.isPaiApp)
                                {
                                    App.switchtopage({page:'mine'});
                                }
                                else
                                {
                                    page_control.navigate_to_page('mine');
                                }


                            }
                            else if(/user_id_/.test(self.get('type_params')))
                            {
                                //代表自动登录，带id过来
                                if(App.isPaiApp)
                                {
                                    App.switchtopage({page:'mine'});
                                }
                                else
                                {
                                    page_control.navigate_to_page('mine');
                                }

                            }
                            else
                            {
                                page_control.back();
                            }
                        }
                    }




                }



            }).on('complete:login:fetch',function(response,xhr)
            {
                //m_alert.hide();
            })
            .on('error:login:fetch',function(response,xhr)
            {
                self.logining = false;

                //m_alert.hide();
                m_alert.show('网络异常','error',{delay:1000});
            });

            //主要滚动条
            //self._setup_scroll();
            //self.view_scroll_obj.refresh();
        },
        /**
         * 安装滚动条
         * @private
         */

        _setup_scroll : function()
        {
            var self = this;
            var view_scroll_obj = Scroll(self.$container);
            self.view_scroll_obj = view_scroll_obj;
        },
        login: function (account,password,role,is_new_user)
        {
            var self = this;

            utility.ajax_request
            ({
                url: global_config.ajax_url.login,
                type : 'POST',
                data :
                {
                    account : account,
                    password : password,
                    role : role || '',
                    is_new_user : is_new_user || ''
                },
                cache: true,
                beforeSend: function (xhr, options)
                {
                    self.trigger('before:login:fetch', xhr, options);
                },
                success: function ( response, options)
                {
                    self.trigger('success:login:fetch', response, options);
                },
                error: function ( xhr, options)
                {
                    self.trigger('error:login:fetch',  xhr, options);
                },
                complete: function (xhr, status)
                {
                    self.trigger('complete:login:fetch', xhr, status);
                }
            });
        },
        /**
         * 视图初始化入口
         */
        setup : function()
        {

            var self = this;

            // 配置交互对象
            self.$container = self.$('[data-role=container]'); // 滚动容器
            self.$user_name = self.$('[data-role="account-name"]');
            self.$user_pwd = self.$('[data-role="account-pwd"]');

            // 安装事件
            self._setup_events();

            if(/has_reg_user_id_/.test(self.get('type_params')))
            {
                //代表从注册页面过来登录，并且是已经注册过的账号，带id过来

                self.url_user_id = self.get('type_params').replace(/has_reg_user_id_/,'');

                self.$user_name.val(self.url_user_id);
            }


        },
        render : function()
        {
            var self = this;

            // 调用渲染函数
            View.prototype.render.apply(self);

            self.trigger('render');

            return self;
        }

    });

    module.exports = mine_view;
});