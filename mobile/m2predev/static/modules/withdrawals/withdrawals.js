define('withdrawals', function(require, exports, module){ /**
 * Created by hudingwen on 15/6/3.
 * 提现
 */

"use strict";


var utility = require('common/utility/index');
var $ = require('components/zepto/zepto.js');
var fastclick = require('components/fastclick/fastclick.js');
var yue_ui = require('yue_ui/frozen');
var App =  require('common/I_APP/I_APP');
var scroll = require('common/scroll/index');
var abnormal = require('common/widget/abnormal/index');
var menu = require('menu/index');

if ('addEventListener' in document)
{
    document.addEventListener('DOMContentLoaded', function ()
    {
        fastclick.attach(document.body);
    }, false);
}

(function($,window)
{
    var _self = $({});

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
                async : async
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
            window.location.href = '../../account/login.php?redirect_url='+encodeURIComponent(window.location.href);
        }
    }

    /*********** 右上角菜单栏 ************/
    /*
     传入对象
     {index:索引值,根据索引值从小到大排列选项顺序}
     {content:文本内容}
     {click_event:点击事件}
     */
    var menu_data =
        [
            {
                index:0,
                content:'首页',
                click_event:function()
                {
                    App.isPaiApp && App.switchtopage({page:'hot'});
                }
            },
            {
                index:1,
                content:'刷新',
                click_event:function()
                {
                    window.location.href = window.location.href;
                }
            }
        ];

    /*
     render() 方法传入(父容器，对象)
     show()   下拉菜单
     hide()   上拉菜单
     */
    menu.render($('body'),menu_data);

    var __showTopBarMenuCount = 0;

    utility.$_AppCallJSObj.on('__showTopBarMenu',function(event,data)
    {

        __showTopBarMenuCount++;

        if(__showTopBarMenuCount%2!=0)
        {
            menu.show()
        }
        else
        {
            menu.hide()
        }
    });
    /*********** 右上角菜单栏 ************/

    // 提现类
    var withdrawals_class = function()
    {
        var self = this;

        self.init();
    };

    withdrawals_class.prototype =
    {
        phone : '',
        init : function()
        {
            var self = this;

            _self.$count_down = $('[data-role="btn-get"]');
            _self.$w_money = $('[data-role="w-money"]');
            _self.$third_account = $('[data-role="third-account"]');
            _self.$sms_code = $('[data-role="sms-code"]');
            _self.$tx_btn = $('[data-role="btn-tx"]');

            self._setup_event();

        },
        _setup_event : function()
        {
            var self = this;

            var w_height = window.innerHeight;

            console.log(w_height)

            window.onresize = function()
            {
                if(window.innerHeight < w_height)
                {
                    $('.footer-withdrawals').addClass('fn-hide');
                    return false;
                }
                else
                {
                    $('.footer-withdrawals').removeClass('fn-hide');
                    return false;
                }
            }

            /**
             * 发送短信
             */
            _self.$count_down.on('click',function()
            {
                if(_self.counting)
                {
                    return;
                }

                self.send_sms();
            });

            /**
             * 成功发送短信
             */
            _self.on('success:fetch_sms',function(e,response)
            {
                var res = response.result_data;

                if(res.code == 1)
                {
                    self.count_down(60);
                    $('[data-role="change-phone"]').removeClass('fn-hide').html(res.data)
                }
                else
                {
                    $.tips
                    ({
                        content:res.msg,
                        stayTime:3000,
                        type:'warn'
                    });
                }


            }).
            on('error:fetch_sms',function(e,res)
            {
                self.stop_count_down();

                $.tips
                ({
                    content:'网络异常',
                    stayTime:3000,
                    type:'warn'
                });
            });

            _self.$tx_btn.on('click',function()
            {
                var params = self.get_value_data();

                if(params)
                {
                    self.withdraw_action(params);
                }

            });

            /**
             * 成功提现
             */
            _self.on('success:fetch_submit',function(e,response)
            {
                var res = response.result_data;

                if(res.code >0)
                {
                    $.tips
                    ({
                        content:res.msg,
                        stayTime:3000,
                        type:'success'
                    });

                    setTimeout(function()
                    {
                        window.location.href = '../bill/?type=withdraw';
                    },2000);
                }
                else
                {
                    $.tips
                    ({
                        content:res.msg,
                        stayTime:3000,
                        type:'warn'
                    });
                }


            }).
            on('error:fetch_submit',function(e,res)
            {
                self.stop_count_down();

                $.tips
                ({
                    content:'网络异常',
                    stayTime:3000,
                    type:'warn'
                });
            });
        },
        refresh : function()
        {
            window.location.href = window.location.href;
        },
        count_down : function(sec)
        {
            var self = this;

            _self.counting = true;

            _self.$count_down.html(sec);

            _self.count_Interval = setInterval(function()
            {
                sec--;

                if(sec == 0)
                {
                    _self.$count_down.html('重新获取');

                    self.stop_count_down();

                    _self.counting = false;
                }
                else
                {
                    _self.$count_down.html(sec);
                }
            },1000)
        },
        /**
         * 获取提交值
         * @returns {{type: string, amount: *, third_account: *, sms_code: Number}}
         * @private
         */
        get_value_data : function()
        {
            var self = this;

            var get_sms_code = $.trim(_self.$sms_code.val()); //验证码

            var get_alipay = _self.$third_account.val(); //支付宝

            var get_number_menber = _self.$w_money.val();

            var email_reg = new RegExp(/^[\w-]+(\.[\w-]+)*@[\w-]+(\.[\w-]+)+$/);

            var phone_reg = new RegExp(/^[0-9]{11}$/);

            var pw_cn = new RegExp(/^[\u4E00-\u9FA5]+$/);

            if(get_number_menber == "" || pw_cn.test(get_number_menber.trim()))
            {
                $.tips
                ({
                    content:'请正确输入提现金额！',
                    stayTime:3000,
                    type:'warn'
                });

                return ;
            }


            if( get_alipay == '' || !(email_reg.test(get_alipay) || phone_reg.test(get_alipay)))
            {

                $.tips
                ({
                    content:'请正确输入支付宝账号！',
                    stayTime:3000,
                    type:'warn'
                });

                return ;
            }

            if( get_sms_code == '')
            {
                $.tips
                ({
                    content:'请输入手机验证码！',
                    stayTime:3000,
                    type:'warn'
                });

                return ;
            }

            //手机验证码必须为数字 正则
            var reg = /^\d+$/ ;
            var is_num = get_sms_code.match(reg);

            if( !is_num )
            {

                $.tips
                ({
                    content:'验证码输入错误，验证码只能为数字!',
                    stayTime:3000,
                    type:'warn'
                });
                return ;
            }

            var data =
            {
                type : 'money',
                amount : get_number_menber,
                //third_account : get_alipay,
                third_account : get_alipay,
                sms_code : get_sms_code,
                is_money : 1
            };

            return data;
        },
        stop_count_down : function()
        {
            var self = this;

            clearInterval(_self.count_Interval);
        },
        /**
         * 发送短信请求
         */
        send_sms : function()
        {
            var self = this;

            if(_self.sending)
            {
                return;
            }

            utility.ajax_request
            ({
                url : window.$__config.ajax_url.withdraw,
                data : {
                    type : 'sms'
                },
                beforeSend : function(xhr,options)
                {
                    _self.$loading = $.loading
                    ({
                        content:'发送中...'
                    });

                    _self.trigger('before:fetch_sms',xhr,options);
                },
                success : function(response, options)
                {
                    _self.sending = false;

                    _self.$loading.loading("hide");

                    _self.trigger('success:fetch_sms',response,options);
                },
                error : function(response, options)
                {
                    _self.sending = false;

                    _self.$loading.loading("hide");

                    _self.trigger('error:fetch_sms',response,options)
                }
            });
        },
        /**
         * 提现请求
         * @param params
         */
        withdraw_action : function(params)
        {
            var self = this;

            if(_self.sending)
            {
                return;
            }

            utility.ajax_request
            ({
                url : window.$__config.ajax_url.withdraw,
                data : params,
                beforeSend : function(xhr,options)
                {
                    _self.$loading = $.loading
                    ({
                        content:'发送中...'
                    });

                    _self.trigger('before:fetch_submit',xhr,options);
                },
                success : function(response, options)
                {
                    _self.sending = false;

                    _self.$loading.loading("hide");

                    _self.trigger('success:fetch_submit',response,options);
                },
                error : function(response, options)
                {
                    _self.sending = false;

                    _self.$loading.loading("hide");

                    _self.trigger('error:fetch_submit',response,options)
                }
            });
        }
    };

    var withdrawals_obj = new withdrawals_class();



})($,window);
 
});