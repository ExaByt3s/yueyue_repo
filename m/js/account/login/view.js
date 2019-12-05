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
    var WeixinApi = require('../../common/I_WX');


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
                utility.stat_action({tj_point:'touch',tj_touch_type :'login'});

                var self = this;

                var user_name = self.$user_name.val();
                var user_pwd = self.$user_pwd.val();

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

                return;

                page_control.navigate_to_page('account/agreement_login');
            },
            'tap [data-role="sso-login"]' : function(ev)
            {
                var platform = $(ev.currentTarget).attr('data-platform');


                if(App.isPaiApp)
                {
                    App.sso_login({platform:platform},function(data)
                    {
                        if(data.message)
                        {
                            m_alert.show(data.message ,'right',{delay:1000});
                        }
                        else
                        {
                            m_alert.show('发送成功,正在登录...','loading');
                        }



                        if(data.code == '0000')
                        {
                            // 成功
                            system.sso_login
                            ({
                                data :
                                {
                                    platform : platform,
                                    uid : data.uid,
                                    token : data.token
                                },
                                success : function(options)
                                {
                                    m_alert.hide();

                                    m_alert.show(options.data.msg ,'right',{delay:1000});


                                },
                                error : function()
                                {
                                    m_alert.hide();
                                }
                            });
                        }
                    });
                }
            },
            'tap [data-role="reg"]' : function()
            {
                var self = this;

                utility.stat_action({tj_point:'touch',tj_touch_type :'reg'});

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
                m_alert.show('登录中...','loading');

            }).on('success:login:fetch',function(response,xhr)
            {
                m_alert.show(response.result_data.msg,'right',{delay:1000});

                //授权
                if(WeixinApi.isWexXin() &&  response.result_data.result == 501)
                {
                    window.location.href = response.result_data.auth_url;

                    return
                }
                //是模特就跳转到提示页
                if(WeixinApi.isWexXin() &&  response.result_data.result == 502)
                {
                    page_control.navigate_to_page('account/login_tips');

                    return
                }

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
                        icon : icon

                    };

                    if(App.isPaiApp)
                    {
                        App.login(params);
                        // 保存设备信息
                        App.save_device();

                    }

                    if(utility.int(self.get('type_params')) == 1)
                    {
                        // 1 代表不可返回

                        page_control.navigate_to_page(global_config.default_index_route);
                    }
                    else if(/user_id_/.test(self.get('type_params')))
                    {
                        //代表自动登录，带id过来
                        page_control.navigate_to_page('mine');

                    }
                    else
                    {
                        page_control.back();

                    }



                }



            }).on('complete:login:fetch',function(response,xhr)
            {
                //m_alert.hide();
            })
            .on('error:login:fetch',function(response,xhr)
            {
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

            var location = window.location;

            if(/has_reg_user_id_/.test(self.get('type_params')))
            {
                //代表从注册页面过来登录，并且是已经注册过的账号，带id过来

                self.url_user_id = self.get('type_params').replace(/has_reg_user_id_/,'');

                self.$user_name.val(self.url_user_id);

            }
            else if(/model_card/.test(self.get('type_params')))
            {
                //代表从模特卡页面登录过来

                self.redirect_url = location.origin+"/m/"+window._page_mode+"#find";
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