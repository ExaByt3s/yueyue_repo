/**
 * 系统功能处理
 * hdw  2014.9.16
 */
define(function(require, exports, module)
{
	var $ = require('$');

    var cookie = require('cookie');

    var login_id = parseInt(cookie.get('member_id'));

    var utility = require('./utility');
    var global_config = require('./global_config');
    var App = require('./I_APP');
    var page_control = require('../frame/page_control');

    var system =
    {
        sso_login : function(options)
        {
            var options = options || {};

            utility.ajax_request
            ({
                url : global_config.ajax_url.sso_login,
                data : options.data,
                success : function(response)
                {

                    if(response.result_data.user_info)
                    {
                        utility.user.update_user(response.result_data.user_info);

                        utility.login_id = response.result_data.user_id;

                        var icon = response.result_data.user_info.user_icon;

                        if(typeof options.success == 'function')
                        {
                            options.success.call(this,{code : 1,data:response,msg:'登录成功'});
                        }

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

                        if(!utility.user.get('role'))
                        {
                            /**
                             * 登录成功，但用户信息没有角色数据，此用户为有poco_id但没有执行过约拍流程
                             * 应该根据选择的角色重新跑约拍流程
                             */
                            if(self.get('is_model_first_time'))
                            {
                                page_control.navigate_to_page('model_date/model_card/0');
                            }
                            else
                            {
                                page_control.navigate_to_page('mine/profile/'+utility.login_id+'/1');
                            }

                        }
                        else
                        {
                            /**
                             * 登录成功，并且有角色数据，此用户是约拍的用户
                             * 但要区分进来登录的页面是从封面页还是其他页面
                             */
                            if(utility.storage.get('is_frist_time_open_app'))
                            {
                                page_control.navigate_to_page(global_config.default_index_route);
                            }
                            else
                            {
                                page_control.back();
                            }
                        }

                    }
                    else
                    {
                        if(typeof options.success == 'function')
                        {
                            options.success.call(this,{code : 0,data:response,msg:'登录失败'});
                        }
                    }

                },
                error : function()
                {
                    if(typeof options.error == 'function')
                    {
                        options.error.call(this);
                    }

                }
            });
        },
        logout : function(params)
        {
            var params = params || {};

            utility.ajax_request
            ({
                url : global_config.ajax_url.logout,
                data :
                {
                    user_id : utility.login_id
                },
                success : function(data)
                {
                    if(data.result_data.code)
                    {
                        utility.storage.set('user','');
                        utility.storage.set('user-role','');

                        var cookieOptions = {domain: 'yueus.com', path: '/'};
                        cookie.del('session_id', cookieOptions);
                        cookie.del('g_session_id', cookieOptions);
                        cookie.del('member_id', cookieOptions);
                        cookie.del('fav_userid', cookieOptions);
                        cookie.del('session_auth_hash', cookieOptions);

                        if(App.isPaiApp)
                        {
                            App.logout();
                        }

                        typeof params.success =='function' && params.success.call(this,data);

                        var url = window.location.origin  +  window.location.pathname;

                        setTimeout(function()
                        {
                            console.log('refresh page');

                            window.location.href = url;
                        },500);
                    }
                    else
                    {
                        alert(data.result_data.msg);

                        if(typeof params.error == 'function')
                        {
                            params.error.call(this,data);
                        }
                    }



                },
                error : function()
                {
                    if(typeof params.error == 'function')
                    {
                        params.error.call(this);
                    }

                    //typeof params.error =='function' && params.error.call(this);
                }
            });
        },
        update_mine_msg : function(data)
        {
            var sys_msg = data.sys_msg;
            var act_msg = data.act_msg;


            var $notice_list = $('[data-role="ui-mine-msg-container"]');

            var unread_act_msg = utility.storage.get('unread_act_msg');

            var $sq_notice_container = $('[data-role="notice_security_container"]');

            if($notice_list.length>0)
            {
                var $msg_num = $notice_list.find('[data-role="notice_msg_num"]');

                var notice_security_num = $notice_list.find('[data-role="notice_security_num"]');

                var $notice_security_container = $notice_list.find('[data-role="notice_security_container"]');

                if(sys_msg>0)
                {
                    //$sq_notice_container.removeClass('fn-hide');

                    $msg_num.removeClass('fn-hide').html(sys_msg);
                }
                else
                {
                    //$sq_notice_container.addClass('fn-hide');

                    $msg_num.addClass('fn-hide').html(0);
                }

                console.log(act_msg,unread_act_msg)

                /*if(act_msg>0 && !unread_act_msg)
                {
                    // 设置未读状态

                    $notice_security_container.removeClass('fn-hide');

                    notice_security_num.removeClass('fn-hide').html(act_msg);
                }
                else
                {

                    $notice_security_container.addClass('fn-hide');

                    notice_security_num.addClass('fn-hide').html(0);
                }*/


            }
        }
    };

    module.exports = system;
});