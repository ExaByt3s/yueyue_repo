define('account/login', function(require, exports, module){ /**
 * 登录页面
 * 2015.5.11
 **/
"use strict";

var utility = require('common/utility/index');
var $ = require('components/zepto/zepto.js');
var fastclick = require('components/fastclick/fastclick.js');
var yue_ui = require('yue_ui/frozen');


if ('addEventListener' in document) {
    document.addEventListener('DOMContentLoaded', function() {
        fastclick.attach(document.body);
    }, false);
}

(function($,window)
{

    var _self = {};

    // 初始化dom
    _self.$page_container = $('[data-role="page-container"]');

    _self.$confirm_btn =  $('[data-role="confrim"]');
    _self.$account_id = $('[data-role="account-number"]');
    _self.$account_pwd = $('[data-role="account-pwd"]');

    _self.$confirm_btn.on('click',function(ev)
    {
        var $cur_btn = $(ev.currentTarget);

        if(utility.is_empty(_self.$account_id.val()))
        {
            $.tips
            ({
                content : '商家ID不能为空',
                stayTime:3000,
                type:"warn"
            });

            return;
        }

        if(utility.is_empty(_self.$account_pwd.val()))
        {
            $.tips
            ({
                content : '密码不能为空',
                stayTime:3000,
                type:"warn"
            });

            return;
        }

        var $loading = {};

        utility.ajax_request
        ({
            url : 'login.php',
            data :
            {
                seller_id : _self.$account_id.val(),
                pwd : _self.$account_pwd.val()
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
                var message = res.message;

                if(res.code >0)
                {
                    window.location.href = 'list.php';
                }
                else
                {
                    $.tips
                    ({
                        content : message,
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


    });






})($,window);



 
});