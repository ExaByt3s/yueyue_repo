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
        logout : function(params)
        {
            var params = params || {};

            if(App.isPaiApp)
            {
                utility.storage.set('user','');
                utility.storage.set('user-role','');
                utility.login_id = 0;

                var cookieOptions = {domain: 'yueus.com', path: '/'};
                cookie.del('yue_session_id', cookieOptions);
                cookie.del('yue_g_session_id', cookieOptions);
                cookie.del('yue_member_id', cookieOptions);
                cookie.del('yue_fav_userid', cookieOptions);
                cookie.del('yue_session_auth_hash', cookieOptions);
            }
            else
            {
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
                            utility.login_id = 0;

                            var cookieOptions = {domain: 'yueus.com', path: '/'};
                            cookie.del('yue_session_id', cookieOptions);
                            cookie.del('yue_g_session_id', cookieOptions);
                            cookie.del('yue_member_id', cookieOptions);
                            cookie.del('yue_fav_userid', cookieOptions);
                            cookie.del('yue_session_auth_hash', cookieOptions);


                            typeof params.success =='function' && params.success.call(this,data);

                            var url = window.location.origin  +  window.location.pathname;

                            setTimeout(function()
                            {
                                console.log('refresh page');

                                if(!App.isPaiApp)
                                {
                                    window.location.href = url;
                                }
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
            }


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