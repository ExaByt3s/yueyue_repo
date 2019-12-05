define('account/set_pwd', function(require, exports, module){ /**
 * ��������
 * 2015.5.11
 **/
"use strict";

var utility = require('common/utility/index');
var $ = require('components/zepto/zepto.js');
var fastclick = require('components/fastclick/fastclick.js');
var yue_ui = require('yue_ui/frozen');
var ua = require('common/ua/index');

if ('addEventListener' in document) {
    document.addEventListener('DOMContentLoaded', function() {
        fastclick.attach(document.body);
    }, false);
}

(function($,window)
{

    var _self = {};

    // ��ʼ��dom
    _self.$page_container = $('[data-role="page-container"]');

    _self.$confirm_btn =  $('[data-role="confrim"]');
    _self.$pwd = $('[data-role="pwd"]');
    _self.$sec_pwd = $('[data-role="sec-pwd"]');

    _self.$confirm_btn.on('click',function(ev)
    {
        var $cur_btn = $(ev.currentTarget);

        if(!confirm('ȷ��ע��?'))
        {
            return;
        }

        var pwd = $.trim(_self.$pwd.val());

        var sec_pwd = $.trim(_self.$sec_pwd.val());

        if(pwd != sec_pwd)
        {

            $.tips
            ({
                content : '���ô�������������������һ��',
                stayTime:3000,
                type:'warn'
            });
            return;
        }

        if(utility.is_empty(pwd) || pwd.length < 6 || pwd.length > 32)
        {
            $.tips
            ({
                content : '������6~32λ����',
                stayTime:3000,
                type:'warn'
            });
            return;
        }


        var $loading = {};

        if(_self.reging)
        {
            return;
        }

        var phone = utility.get_url_params(window.location.href,'phone');
        var verify_code = utility.get_url_params(window.location.href,'verify_code');

        phone = phone.substring(0,phone.length-1);
        phone = parseInt(phone,16);

        var action_url = '';

        if(ua.is_weixin)
        {
            action_url = 'http://yp.yueus.com/m/action/reg.php';
        }
        else
        {
            action_url = window.$__config.ajax_url.reg;
        }

        utility.ajax_request
        ({
            url : action_url,
            type : 'POST',
            data :
            {
                phone : phone,
                verify_code : verify_code,
                pwd : pwd,
                role : 'cameraman',
                type:'reg_act'
            },
            beforeSend : function()
            {
                $loading=$.loading
                ({
                    content:'������...'
                })
            },
            success : function(response)
            {
                _self.reging = false;

                $loading.loading("hide");

                var response = response.result_data;

                var msg = response.msg;

                var code = response.code;

                if(code == 1)
                {
                    // ע��ɹ�������Ҫ�����û���Ϣ

                    $.tips
                    ({
                        content : 'ע��ɹ�������Ϊ����תҳ��...',
                        stayTime:3000,
                        type:"success"
                    });

                    var r_url = utility.get_url_params(window.location.href,'redirect_url');


                    if(r_url)
                    {
                        var redirect_url = decodeURIComponent(r_url);
                    }
                    else
                    {
                        var redirect_url = '../recharge/card.php';
                    }

                    setTimeout(function()
                    {
                        window.location.href = redirect_url;
                    },3000);

                }
                else
                {
                    $.tips
                    ({
                        content : msg,
                        stayTime:3000,
                        type:"warn"
                    });
                }


            },
            error : function()
            {
                _self.reging = false;

                $loading.loading("hide");

                $.tips
                ({
                    content : '�����쳣',
                    stayTime:3000,
                    type:"warn"
                });
            }

        });



    });








})($,window);



 
});