/**
 * 充值选择列表
 * Created by hudw on 2015/5/6.
 */
"use strict";



var $ = require('zepto');
var fastclick = require('fastclick');
var yue_ui = require('../yue_ui/frozen');
var App =  require('../common/I_APP/I_APP');
var utility = require('../common/utility/index');


if ('addEventListener' in document)
{
    document.addEventListener('DOMContentLoaded', function ()
    {
        fastclick.attach(document.body);
    }, false);
}

if(App.isPaiApp)
{
    App.closeloading();
}


(function($,window)
{
    if(App.isPaiApp)
    {
        App.check_login(function(data)
        {
            /**
             * 获取个人信息函数，专用于app
             */

            var params = window.__YUE_APP_USER_INFO__;

            var local_user_id = utility.login_id;
            var client_user_id = window.__YUE_APP_USER_INFO__.user_id || 0;

            var async = (local_user_id == client_user_id);

            console.log("=====local_user_id,client_user_id=====");
            console.log(local_user_id,client_user_id);

            utility.ajax_request
            ({
                url: window.$__config.ajax_url.auth_get_user_info,
                data : params,
                cache: false,
                async : async,
                beforeSend: function (xhr, options) {

                },
                success: function (response, options) {
                    console.log(response);
                },
                error: function (model, xhr, options) {

                },
                complete: function (xhr, status) {

                }
            });

            if(!utility.int(data.pocoid))
            {
                App.openloginpage(function(data)
                {
                    if(data.code == '0000')
                    {
                        utility.refresh_page();
                    }
                });

                return;
            }

        });

    }
    else
    {
        if(!utility.auth.is_login())
        {
            window.location.href = '../account/login.php?redirect_url='+encodeURIComponent(window.location.href);
        }
    }

    $('[data-link]').on('click',function(ev)
    {
        var url = $(ev.currentTarget).attr('data-link');

        window.location.href = url;
    });
})($,window);