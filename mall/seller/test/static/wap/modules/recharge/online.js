define('recharge/online', function(require, exports, module){ /**
 * 在线充值卡
 * Created by hudw on 2015/5/7.
 */

"use strict";


var utility = require('common/utility/index');
var $ = require('components/zepto/zepto.js');
var fastclick = require('components/fastclick/fastclick.js');
var yue_ui = require('yue_ui/frozen');
var App =  require('common/I_APP/I_APP');
var ua =  require('common/ua/index');



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

    var _self = {};

    // 默认参数
    var defaults=
    {
        title : '付款'
    };

    // 获取参数
    _self.quotes_id = $('#quotes_id').val();

    // 初始化dom
    _self.$page_container = $('[data-role="page-container"]');


    // 构造函数
    var pay_tt_class = function()
    {
        var self = this;

        self.init();

    };

    // 添加方法与属性
    pay_tt_class.prototype =
    {
        refresh : function()
        {
            var self = this;


        },
        page_back : function()
        {

        },
        // 初始化
        init : function()
        {
            var self = this;

            // 安装事件
            self.setup_event();

        },
        hide : function()
        {

        },
        // 安装事件
        setup_event : function()
        {
            var self = this;

            _self.$pay_li = $('[data-role="pay-li"]');
            _self.$select_price = $('[data-role="select-recharge-price"]');
            _self.$recharge_btn = $('[data-role="recharge-btn"]');

            if(ua.is_normal_wap)
            {
                $('[data-pay-type="tenpay_wxapp"]').addClass('fn-hide');
            }
            else if(ua.is_weixin)
            {
                $('[data-pay-type="alipay_purse"]').addClass('fn-hide');
                $('[data-pay-type="tenpay_wxapp"]').addClass('fn-hide');

            }

            // 默认设置选中支付宝

            _self.$pay_li.each(function(i,obj)
            {
                if($(obj).find('[data-role="yes-tag"]').hasClass('icon-select-active'))
                {
                    _self.selected_pay_action_type = $(obj).attr('data-pay-type');
                }
            });

            _self.$select_price.each(function(i,obj)
            {
                if($(obj).find('[data-role="yes-tag"]').hasClass('icon-select-active'))
                {
                    _self.$select_price_val = $(obj).attr('data-price');
                }
            });

            // 选择金额
            _self.$select_price.on('click',function(ev)
            {
                var $cur_btn = $(ev.currentTarget);
                var $yes_tag = $cur_btn.find('[data-role="yes-tag"]');

                var tag = $yes_tag.hasClass('icon-select-active');

                // 清空所有选中的，用于以后扩展
                _self.$select_price.find('[data-role="yes-tag"]').removeClass('icon-select-active').addClass('icon-select-no');

                $yes_tag.addClass('icon-select-active').removeClass('icon-select-no');

                _self.$select_price_val  = $cur_btn.attr('data-price');
            });

            // 选择支付方式选中
            _self.$pay_li.on('click',function(ev)
            {
                var $cur_btn = $(ev.currentTarget);
                var $yes_tag = $cur_btn.find('[data-role="yes-tag"]');

                var tag = $yes_tag.hasClass('icon-select-active');

                // 清空所有选中的，用于以后扩展
                _self.$pay_li.find('[data-role="yes-tag"]').removeClass('icon-select-active').addClass('icon-select-no');

                $yes_tag.addClass('icon-select-active').removeClass('icon-select-no');

                var pay_type =  $cur_btn.attr('data-pay-type');

                // 设置是否选中支付方式
                _self.selected_pay_action_type =pay_type;

            });

            _self.$recharge_btn.on('click',function()
            {
                var pay_price = _self.$select_price_val;
                var third_code = _self.selected_pay_action_type;

                if(!confirm('确定充值?'))
                {
                    return;
                }

                if(!pay_price)
                {
                    $.tips
                    ({
                        content:'请选择充值金额',
                        stayTime:3000,
                        type:'warn'
                    });

                    return;
                }

                if(!third_code)
                {
                    $.tips
                    ({
                        content:'请选择支付方式',
                        stayTime:3000,
                        type:'warn'
                    });

                    return;
                }




                var loc = window.location;
                var redirect_url_prefix = 'http://yp.yueus.com/mobile/';

                if(/predev/.test(loc.href))
                {
                    var redirect_url = 'm2predev/recharge/success.php';
                }
                else
                {
                    var redirect_url = 'm2/recharge/success.php';
                }

                var $loading= {};

                if(ua.is_normal_wap)
                {
                    if(third_code == 'alipay_purse')
                    {
                        third_code = 'alipay_wap';
                    }

                }

                utility.ajax_request
                ({
                    url : window.$__config.ajax_url.recharge,
                    type : 'POST',
                    data :
                    {
                        type : 'recharge',
                        third_code : third_code,
                        amount : pay_price,
                        redirect_url : redirect_url_prefix+redirect_url
                    },
                    beforeSend : function()
                    {
                        $loading = $.loading
                        ({
                            content:'发送中...'
                        });
                    },
                    success : function(res)
                    {
                        $loading.loading("hide");

                        var third_code = res.result_data && res.result_data.third_code;
                        var channel_return = redirect_url_prefix+redirect_url+'?'+res.result_data.payment_no;

                        if(res.result_data.code>0)
                        {
                            var type = 'success';
                        }
                        else
                        {
                            var type = 'warn';
                        }

                        $.tips
                        ({
                            content:res.result_data.message,
                            stayTime:3000,
                            type:type
                        });

                        switch(third_code)
                        {
                            // 支付宝支付，调用App接口
                            case 'alipay_purse':
                                App.alipay
                                ({
                                    alipayparams : res.result_data.data,
                                    payment_no : res.result_data.payment_no
                                },function(data)
                                {
                                    var result = utility.int(data.result);

                                    var text = self.after_pay_text(result);

                                    $.tips
                                    ({
                                        content:text,
                                        stayTime:3000,
                                        type:'success'
                                    });

                                    if(result == 1 || result ==-1 || result == -2)
                                    {
                                        window.location.href = channel_return;
                                    }
                                });
                                break;
                            case 'alipay_wap':
                                if(res.result_data.code == 1)
                                {
                                    window.location.href = res.result_data.data;
                                }
                                else
                                {
                                    var text = self.after_pay_text(res.result_data.code);

                                    $.tips
                                    ({
                                        content:text,
                                        stayTime:3000,
                                        type:'warn'
                                    });
                                }

                                break;
                            // 微信支付
                            case 'tenpay_wxapp':
                                App.wxpay(JSON.parse(res.result_data.data),function(data)
                                {
                                    var result = utility.int(data.result);

                                    var text = self.after_pay_text(result);

                                    $.tips
                                    ({
                                        content:text,
                                        stayTime:3000,
                                        type:'success'
                                    });

                                    if(result == 1 || result ==-1 || result == -2)
                                    {
                                        window.location.href = channel_return;
                                    }
                                });
                                break;
                            case 'tenpay_wxpub':
                                window.location.href = 'http://yp.yueus.com/m/pay_jump.php?third_code=tenpay_wxpub&amount='+pay_price+'&redirect_url='+encodeURIComponent(channel_return)+'&type=recharge'
                        }
                    },
                    error : function()
                    {
                        $loading.loading("hide");

                        $.tips
                        ({
                            content:'网络异常',
                            stayTime:3000,
                            type:'warn'
                        });
                    }
                });
            });


        },
        /**
         * 清除指定选择项
         * @param tag
         */
        clear_select : function(tag)
        {
            var self = this;

            var $yes_tag =_self.$pay_li.find('[data-role="yes-tag"]');

            if(tag)
            {
                _self.$select_ab_btn.find('[data-role="yes-tag"]')
                    .removeClass('icon-select-active').addClass('icon-select-no');
            }
            // 不传递指定标记，清除所有选择
            else
            {
                $yes_tag.removeClass('icon-select-active').addClass('icon-select-no');
            }
        },
        after_pay_text : function(code) {
            var str = '';

            switch (utility.int(code)) {
                case 1:
                case -2:
                case -1:
                    str = '支付成功';
                    break;
                case 0:
                    str = '其它错误';
                    break;
                case -3:
                    str = '支付失败';
                    break;
                case -4:
                    str = '支付取消';
                    break;
            }

            return str;
        }
    };

    // 实例化tt支付类
    var pt_obj = new pay_tt_class();

    pt_obj.refresh();




})($,window); 
});