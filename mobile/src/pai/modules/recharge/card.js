/**
 * 地推充值卡
 * Created by hudw on 2015/5/7.
 */

"use strict";


var utility = require('../common/utility/index');
var $ = require('zepto');
var fastclick = require('fastclick');
var yue_ui = require('../yue_ui/frozen');
var App =  require('../common/I_APP/I_APP');



if ('addEventListener' in document)
{
    document.addEventListener('DOMContentLoaded', function ()
    {
        fastclick.attach(document.body);
    }, false);
}

(function($,window)
{
    if(App.isPaiApp)
    {
        App.check_login(function(data)
        {
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

    var $number = $('[data-role="account-number"]');
    var $pwd = $('[data-role="account-pwd"]');
    var $confirm_btn = $('[data-role="confrim"]');

    $confirm_btn.on('click',function(ev)
    {
        var $loading = {};

        if(!$number.val() || !$pwd.val())
        {
            $.tips
            ({
                content : '充值卡卡号或密码不能为空',
                stayTime:3000,
                type:"warn"
            });
            return;
        }

        if(confirm('确认充值'))
        {
            utility.ajax_request
            ({
                url : window.$__config.ajax_url.recharge_card,
                type : 'POST',
                data :
                {
                    number : $number.val(),
                    pwd : $pwd.val()
                },
                beforeSend : function()
                {
                    $loading=$.loading
                    ({
                        content:'发送中...'
                    })
                },
                success : function(res)
                {

                    $loading.loading("hide");

                    var res = res.result_data;
                    if(res.code>0)
                    {
                        $.tips
                        ({
                            content : res.message,
                            stayTime:3000,
                            type:"success"
                        });

                        // todo 跳转页面
                        window.location.href = 'success.php';
                    }
                    else
                    {
                        $.tips
                        ({
                            content : res.message,
                            stayTime:3000,
                            type:"warn"
                        });
                    }

                },
                error : function()
                {
                    $loading.loading("hide");

                    $.tips
                    ({
                        content : '网络异常',
                        stayTime:3000,
                        type:"warn"
                    });
                }
            });
        }
    });




})($,window);