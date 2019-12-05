/**
 * 登录页面
 * 2015.5.11
 **/
"use strict";

var utility = require('../common/utility/index');
var $ = require('zepto');
var fastclick = require('fastclick');
var yue_ui = require('../yue_ui/frozen');


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

    _self.$login_btn =  $('[data-role="login"]');
    _self.$confirm_btn =  $('[data-role="confrim"]');
    _self.$verfiy_code = $('[data-role="account-verfiy-code"]');
    _self.$enter_phone = $('[data-role="enter-phone"]');
    _self.$verfiy_btn = $('[data-role="get_code"]');
    _self.count_btn = _self.$verfiy_btn.find('span');

    _self.$login_btn.on('click',function()
    {
        var r_url = utility.get_url_params(window.location.href,'redirect_url');
        if(r_url)
        {
            var redirect_url = '?redirect_url='+r_url;
        }
        else
        {
            var redirect_url = '';
        }

        window.location.href = 'login.php'+redirect_url;
    });

    // 计时函数
    _self._count_down = function(sec)
    {
        var self = this;

        _self.counting = true;

        _self.count_btn.html(sec);

        _self.count_Interval = setInterval(function()
        {
            sec--;

            if(sec == 0)
            {
                _self._stop_count_down();
            }
            else
            {
                _self.count_btn.html(sec);
            }
        },1000)
    };

    // 停止函数
     _self._stop_count_down = function()
    {
        var self = this;

        clearInterval(_self.count_Interval);

        _self.count_btn.html('重新获取');

        _self.counting = false;
    };

    var reg_action = function(options)
    {
        var $loading = {};

        utility.ajax_request
        ({
            url : window.$__config.ajax_url.reg,
            data : options.data,
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

                options.success.call(this,res);

                /*var res = res.result_data;
                 var message = res.msg;

                 if(res.result == '200')
                 {
                 var redirect_url = utility.get_url_params(window.location.href,'redirect_url');

                 if(redirect_url)
                 {
                 redirect_url = decodeURIComponent(redirect_url);

                 window.location.href = redirect_url;
                 }
                 else{
                 window.location.href = '../recharge/card.php';
                 }


                 }
                 else
                 {
                 $.tips
                 ({
                 content : message,
                 stayTime:3000,
                 type:"warn"
                 });
                 }*/
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

                _self.has_reg = false;
            }

        });
    }

    _self.$enter_phone[0].oninput = function()
    {
        if(_self.$enter_phone.val() != "")
        {
            _self.$confirm_btn.removeClass("no-active").removeAttr('disabled');
        }
        else
        {
            _self.$confirm_btn.addClass("no-active").attr('disabled','disabled');
        }
    };

    // 获取验证码
    _self.$verfiy_btn.on('click',function()
    {
        var phone = _self.$enter_phone.val();
        var type = 'bind_sent_verify_code';

        if(_self.has_reg)
        {
            return;
        }

        //不为数字 || 空 || 字符串中有空格时返回
        if(!parseInt(phone) || phone.trim() == '' || phone.trim().indexOf(" ") != -1)
        {
            $.tips
            ({
                content : '手机号格式错误',
                stayTime:3000,
                type:'warn'
            });
            return;
        }

        if(_self.counting)
        {
            return
        }

        // 计时开始
        _self._count_down(60);

        reg_action
        ({
            data :
            {
                phone : phone,
                type : type
            },
            success : function(response)
            {
                var response = response.result_data;

                var msg = response.msg;

                var code = response.code;

                var status = '';

                _self.has_reg = false;

                _self.is_model_no_pwd = false;

                if(code == 1)
                {
                    status = 'success';



                }
                // 得知是已经注册过的账号，直接跳到登录页
                else if (code == 2)
                {
                    status = 'success';

                    _self._stop_count_down();

                    _self.has_reg = false;

                    //todo 提示已经存在的账号，显示dialog

                }
                // 已经存在的模特，但是没有密码
                else if( code == 5)
                {
                    status = 'success';

                    _self.is_model_no_pwd = true;
                }
                else
                {
                    status = 'warn';

                    // modify hudw 2014.11.8
                    _self._stop_count_down();
                }

                $.tips
                ({
                    content : msg,
                    stayTime:3000,
                    type:status
                });
            }
        })
    });

    _self.$confirm_btn.on('click',function(ev)
    {
        var $cur_btn = $(ev.currentTarget);

        if($cur_btn.hasClass('no-active'))
        {
            return;
        }

        if(utility.is_empty(_self.$enter_phone.val()))
        {
            $.tips
            ({
                content : '手机号不能为空',
                stayTime:3000,
                type:"warn"
            });

            return;
        }

        if(utility.is_empty(_self.$verfiy_code.val()))
        {
            $.tips
            ({
                content : '验证码不能为空',
                stayTime:3000,
                type:"warn"
            });

            return;
        }

        var phone = _self.$enter_phone.val();
        var verfiy_code = _self.$verfiy_code.val();

        reg_action
        ({
            data :
            {
                phone : phone,
                type : 'verify_phone',
                verify_code : verfiy_code
            },
            success : function(response)
            {
                var response = response.result_data;

                var msg = response.msg;

                var code = response.code;

                $.tips
                ({
                    content : msg,
                    stayTime:3000,
                    type:'warn'
                });

                if(code == 1)
                {
                    var phone = _self.$enter_phone.val()*1;
                    phone = phone.toString(16);

                    var r_url = utility.get_url_params(window.location.href,'redirect_url');

                    if(r_url)
                    {
                        var redirect_url = '&redirect_url='+r_url;
                    }
                    else
                    {
                        var redirect_url = '';
                    }

                    window.location.href = 'set_pwd.php?verify_code='+_self.$verfiy_code.val()+'&phone='+phone+'0'+redirect_url;
                }


            }
        })




    });








})($,window);



