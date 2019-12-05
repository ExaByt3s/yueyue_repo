define('act/pay', function(require, exports, module){ /**
 * 支付页面
 hudw 2015.4.15
 **/
"use strict";

var back_btn = require('common/widget/back_btn/main');
var utility = require('common/utility/index');
var ua = require('common/ua/index');
var $ = require('components/zepto/zepto.js');
var fastclick = require('components/fastclick/fastclick.js');
var yue_ui = require('yue_ui/frozen');
var App =  require('common/I_APP/I_APP');
var coupon = require('coupon/list');
var yueyue_header = require('common/widget/yueyue_header/main');
var header = require('common/widget/header/main');

if ('addEventListener' in document) {
    document.addEventListener('DOMContentLoaded', function() {
        fastclick.attach(document.body);
    }, false);
}

(function($,window)
{




    var $header = $('#nav-header');

    var tpl = Handlebars.template(function (Handlebars,depth0,helpers,partials,data) {
  this.compilerInfo = [4,'>= 1.0.0'];
helpers = this.merge(helpers, Handlebars.helpers); data = data || {};
  var buffer = "", stack1, functionType="function", escapeExpression=this.escapeExpression;


  buffer += "<div id=\"global-header\"></div>\n\n<ul class=\"info ui-list ui-list-text pt15 pr15 pb5\">\n    <li>\n        <p>活动名称："
    + escapeExpression(((stack1 = ((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.detail_list)),stack1 == null || stack1 === false ? stack1 : stack1.event_title)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "</p>\n    </li>\n    <li>\n        <div>手机号码："
    + escapeExpression(((stack1 = ((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.detail_list)),stack1 == null || stack1 === false ? stack1 : stack1.phone)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "</div>\n    </li>\n    <li>\n        <div>活动价格：￥"
    + escapeExpression(((stack1 = ((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.detail_list)),stack1 == null || stack1 === false ? stack1 : stack1.total_budget)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "</div>\n    </li>\n    <li>\n        <div>报名人数："
    + escapeExpression(((stack1 = ((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.detail_list)),stack1 == null || stack1 === false ? stack1 : stack1.enroll_num)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "</div>\n    </li>\n    <li>\n        <div>选择场次："
    + escapeExpression(((stack1 = ((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.detail_list)),stack1 == null || stack1 === false ? stack1 : stack1.table_text)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "</div>\n    </li>\n</ul>\n\n\n\n\n<div class=\"mt30 pl15 mb10\">应付金额</div>\n\n<!--支付模块-->\n<ul class=\"ui-list ui-list-text \" class=\"ui-border-t\">\n    <li class=\"ui-border-t\">\n        <div class=\"ui-txt-default \">支付总价：<span >￥<label data-role=\"pay-amount\">"
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.total_amount)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "</label></span></div>\n    </li>\n    <li data-role=\"coupon-money\" class=\"ui-border-t\">\n        <div class=\"ui-txt-default \">使用优惠券：<span><div class=\"ui-nowrap\" style=\"width: 200px;\" data-role=\"coupon-money-tag\"></div></span></div>\n        <div class=\"ui-edge-right\">\n            <span class=\"count-money-color\" data-role=\"coupon-money-text\"></span>\n            <i class=\"icon icon-select-no\" data-role=\"yes-tag\"></i>\n        </div>\n    </li>\n    <li data-role=\"select-available-balance\" class=\"ui-border-t\">\n        <div class=\"ui-txt-default \">钱包<span>（￥<label data-role=\"available_balance\">"
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.available_balance)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "</label></span>）：<span class=\"count-money-color\" data-role=\"less-money\"></span></div>\n        <div class=\"ui-edge-right\">\n\n            <i class=\"icon icon-select-no\" data-role=\"yes-tag\"></i>\n        </div>\n    </li>\n    <li class=\"ui-border-t\">\n        <div class=\"ui-txt-default \">还需支付：<span class=\"count-money-color_v2 fb\">￥<label data-role=\"need-price\">"
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.pay_amount)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "</label></span></div>\n    </li>\n</ul>\n<ul class=\"ui-list ui-list-text ui-border-b mt20 fn-hide\" style=\"margin-bottom: 0;\"  data-role=\"must-pay-container\"></ul>\n\n<div data-role=\"other-pay-container\">\n    <div class=\"mt30 pl15 mb10\">支付方式</div>\n    <ul class=\"ui-list ui-list-text \" >\n        <li class=\"ui-border-t\" data-pay-type=\"alipay_purse\" data-role=\"pay-li\">\n            <div class=\"ui-txt-default \">\n                <div class=\"pay-type\">\n                    <i class=\"icon icon-zhifubao\"></i>\n                    <div class=\"ui-list-info \">\n                        <h4 class=\"ui-nowrap\" >支付宝支付</h4>\n                        <p class=\"ui-nowrap\">推荐有支付宝账号的用户使用</p>\n                    </div>\n                </div>\n                <div class=\"ui-edge-right\">\n                    <i class=\"icon icon-select-no\" data-role=\"yes-tag\"></i>\n                </div>\n            </div>\n        </li>\n        <li class=\"ui-border-t\" data-pay-type=\"tenpay_wxapp\" data-role=\"pay-li\">\n            <div class=\"ui-txt-default \">\n                <div class=\"pay-type\">\n                    <i class=\"icon icon-wx-pay\"></i>\n                    <div class=\"ui-list-info \">\n                        <h4 class=\"ui-nowrap\" >微信支付</h4>\n                        <p class=\"ui-nowrap\">安装微信5.0及以上版本的使用</p>\n                    </div>\n                </div>\n                <div class=\"ui-edge-right\">\n                    <i class=\"icon icon-select-no\" data-role=\"yes-tag\"></i>\n                </div>\n            </div>\n        </li>\n    </ul>\n\n    <div style=\"height:100px;\" class=\"bg\"></div>\n\n</div>\n\n\n\n\n<div class=\"last-container fn-hide\"></div>\n<!--支付模块-->\n<div class=\"buttom-btn-wrap ui-border-t\">\n    <div class=\"pl10 text-info\">\n        还需支付：<span class=\"count-money-color_v2 red-font\" style=\"font-size: 18px;\">￥<label data-role=\"need-price\">"
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.pay_amount)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "</label></span>\n    </div>\n    <div class=\"right\">\n        <button class=\"ui-tt-pay-btn \" id=\"pay-btn\">\n            <span class=\"ui-button-content\" ><i class=\"icon icon-btn-icon-fk \"></i></span>\n            <span class=\"txt\">去支付</span>\n        </button>\n    </div>\n\n</div>";
  return buffer;
  });

    var _self = {};

    // 默认参数
    var defaults=
        {
            title : '付款'
        };

    // 获取参数
    _self.order_sn = $('#order_sn').val();

    // 初始化dom
    _self.$page_container = $('[data-role="page-container"]');

    // 初始化统计点
    if(App.isPaiApp)
    {
        //App.analysis('moduletongji',{pid:'1220089',mid:'122OD04006'});
    }

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

            var $loading=$.loading
            ({
                content:'加载中...'
            });

            var content = _page_data.result_data;

            _self.$page_container.html('');

            _self.$page_container.html(tpl({data:content}));

            _self.page_params = window._page_params.result_data;

            // 初始化优惠券
            _self.coupon_obj = coupon.init
            ({
                container : $('[data-role="coupon-list-container"]'),
                order_total_amount :content.total_amount,
                extend_params :
                {
                    event_id : _self.page_params.event_id,
                    enroll_id : _self.page_params.enroll_id,
                    table_id : _self.page_params.table_id,
                    module_type : 'waipai'
                },
                page : 1

            });

            // 渲染头部
            header.init({
                ele : $("#global-header"), //头部渲染的节点
                title:"支付",
                header_show : true , //是否显示头部
                right_icon_show : false, //是否显示右边的按钮

                share_icon :
                {
                    show :false,  //是否显示分享按钮icon
                    content:""
                },
                omit_icon :
                {
                    show :false,  //是否显示三个圆点icon
                    content:""
                },
                show_txt :
                {
                    show :false,  //是否显示文字
                    content:"编辑"  //显示文字内容
                }
            });


            // 安装事件
            self.setup_event();

            $loading.loading("hide");
        },
        page_back : function()
        {

        },
        // 初始化
        init : function()
        {
            var self = this;
            var $back_btn_html = back_btn.render();
            $header.prepend($back_btn_html);



            // 安装后退事件
            self.setup_back();
        },
        hide : function()
        {

        },
        // 安装后退按钮
        setup_back : function()
        {
            $('[data-role="page-back"]').on('tap',function()
            {
                if(App.isPaiApp)
                {
                    App.app_back();
                }
            });
        },
        // 安装事件
        setup_event : function()
        {
            var self = this;

            _self.$pay_li = $('[data-role="pay-li"]');
            _self.$pay_btn = $('#pay-btn');
            _self.$select_ab_btn = $('[data-role="select-available-balance"]');
            _self.$less_money = $('[data-role="less-money"]');
            _self.$available_balance = $('[data-role="available_balance"]');
            _self.$need_price = $('[data-role="need-price"]');
            _self.ab = $('[data-role="available_balance"]').html();
            _self.total_price = $('[data-role="pay-amount"]').html();
            _self.$coupon_list_wrap = $('[data-role="coupon-list-wrap"]');
            _self.$coupon_money = $('[data-role="coupon-money"]');
            _self.$coupon_money_tag = $('[data-role="coupon-money-tag"]');
            _self.$coupon_money_text = $('[data-role="coupon-money-text"]');

            // 安装优惠券头部
            yueyue_header.render($('[data-role="nav-header"]')[0],
                {
                    left_text : '返回',
                    title:'可用优惠券',
                    right_text : '确定'
                });

            _self.$left_btn = $('[data-role="left-btn"]');
            _self.$right_btn = $('[data-role="right-btn"]');

            // 默认设置选中支付宝
            _self.selected_pay_action_type ='alipay_purse';

            // 默认设置选中余额
            _self.can_use_balance = true;

            // 选择余额支付
            _self.$select_ab_btn.on('click',function(ev)
            {
                var $cur_btn = $(ev.currentTarget);
                var $yes_tag = $cur_btn.find('[data-role="yes-tag"]');
                var tag = $yes_tag.hasClass('icon-select-active');

                if(tag)
                {
                    $yes_tag.addClass('icon-select-no').removeClass('icon-select-active');
                }
                else
                {
                    $yes_tag.removeClass('icon-select-no').addClass('icon-select-active');
                }

                _self.can_use_balance = $yes_tag.hasClass('icon-select-active');

                var pay_items_model =
                    {
                        can_use_balance : _self.can_use_balance,
                        available_balance : _self.ab,
                        total_price : _self.total_price,
                        coupon : _self.coupon_obj.selected_coupon && _self.coupon_obj.selected_coupon.face_value
                    };

                // 初始化表单
                self.count_money(pay_items_model);
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

                _self.$select_ab_btn.find('[data-role="yes-tag"]').removeClass('icon-select-active');

                if(_self.can_use_balance)
                {
                    _self.$select_ab_btn.find('[data-role="yes-tag"]').addClass('icon-select-active');
                }

            });

            // 关闭优惠券层
            _self.$left_btn.on('click',function()
            {
                _self.$page_container.removeClass('fn-hide');
                _self.$coupon_list_wrap.addClass('fn-hide');

                if(!ua.is_yue_app)
                {
                    $('main').css('padding-top','45px');
                }
            });

            // 关闭优惠券层
            _self.$right_btn.on('click',function()
            {
                _self.$page_container.removeClass('fn-hide');
                _self.$coupon_list_wrap.addClass('fn-hide');

                //要实现选中优惠券时候的处理
                var selected_coupon = _self.coupon_obj.selected_coupon;

                var pay_items_model =
                    {
                        can_use_balance : _self.can_use_balance,
                        available_balance : _self.ab,
                        total_price : _self.total_price,
                        set_pay_type : false,
                        coupon : selected_coupon && selected_coupon.face_value
                    };

                // 初始化表单
                self.count_money(pay_items_model);

                if(selected_coupon)
                {
                    // 显示优惠信息
                    _self.$coupon_money_tag.html(selected_coupon.batch_name);
                }

                if(selected_coupon)
                {
                    _self.$coupon_money.find('[data-role="yes-tag"]').addClass('icon-select-active').removeClass('icon-select-no');
                }
                else
                {
                    _self.$coupon_money.find('[data-role="yes-tag"]').removeClass('icon-select-active').addClass('icon-select-no');
                }

                if(!ua.is_yue_app)
                {
                    $('main').css('padding-top','45px');
                }
            });

            // 进入优惠券列表
            _self.$coupon_money.on('click',function(ev)
            {
                _self.$page_container.addClass('fn-hide');
                _self.$coupon_list_wrap.removeClass('fn-hide');

                if(!ua.is_yue_app)
                {
                    $('main').css('padding-top',0);
                }
            });

            // 确定按钮
            _self.$pay_btn.on('click',function()
            {
                //App.analysis('eventtongji',{id:'1220090'});

                if(!_self.selected_pay_action_type)
                {
                    alert('请选择支付方式');
                    return;
                }


                if(confirm('确认支付?'))
                {
                    var redirect_url = './sign.php?event_id='+_page_params.result_data.event_id;

                    var params =
                        {
                            third_code : _self.selected_pay_action_type,
                            redirect_url : redirect_url,
                            coupon_sn : (_self.coupon_obj.selected_coupon && _self.coupon_obj.selected_coupon.coupon_sn) || '',
                            available_balance : _self.ab,
                            is_available_balance : _self.$select_ab_btn.find('[data-role="yes-tag"]').hasClass('icon-select-active') ? 1 : 0

                        };

                    params = $.extend({},params,_page_params.result_data,true);

                    self.pay_action(params,
                        {
                            success :function(res)
                            {
                                var third_code = res.result_data && res.result_data.third_code;
                                var channel_return = res.result_data && res.result_data.channel_return;
                                var code = res.result_data && res.result_data.code;

                                if(code == 2)
                                {
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
                                    }
                                }
                                else
                                {
                                    $.tips
                                    ({
                                        content:'余额支付成功',
                                        stayTime:3000,
                                        type:'success'
                                    });

                                    window.location.href = channel_return;
                                }






                            },
                            error : function()
                            {

                            }
                        });
                }
            });

            // 初始化计算
            var pay_items_model =
                {
                    can_use_balance : _self.ab>0?true:false,
                    available_balance : _self.ab,
                    total_price : _self.total_price
                };

            // 初始化表单
            self.count_money(pay_items_model);
        },
        count_money : function(params)
        {
            var self = this;

            var total_price = utility.float(params.total_price);

            var available_balance = utility.float(params.available_balance);

            var set_pay_type = params.set_pay_type == null ? true : false;

            var coupon = params.coupon || 0;

            // 钱包要扣的钱
            var less_money = 0;

            // 使用优惠劵的时候
            if(coupon)
            {
                total_price = total_price - utility.float(coupon);
            }

            // 需要给的钱
            var must_pay_money = available_balance - total_price;

            // 使用余额支付
            if(params.can_use_balance)
            {

                less_money = must_pay_money;

                if(less_money <= 0)
                {
                    less_money = available_balance;
                }
                else
                {
                    less_money = total_price;

                    must_pay_money = 0;
                }

                _self.$select_ab_btn.find('[data-role="yes-tag"]').removeClass('icon-select-no').addClass('icon-select-active');

                // 余额完全够钱支付了订单
                if(must_pay_money>=0)
                {
                    self.clear_select();

                    self.control_other_pay_item({show:false});

                    console.log('余额完全够钱支付了订单');
                }
                // 余额不够钱支付订单的时候，需要混合支付
                else
                {

                    self.control_other_pay_item({show:true});

                    // 设置默认支付方式，计算优惠劵后不设置默认支付方式
                    if(set_pay_type)
                    {
                        //self.pay_item_obj._select_pay_type('alipay_purse');
                        $('[data-pay-type="'+_self.selected_pay_action_type+'"]').find('[data-role="yes-tag"]')
                            .removeClass('icon-select-no').addClass('icon-select-active');
                    }
                }
            }
            // 完全使用第三方支付
            else
            {
                // 需要给的钱就是总价 减去 优惠劵
                must_pay_money = total_price;

                self.clear_select(true);

                self.control_other_pay_item({show:true});

                // 设置默认支付方式，计算优惠劵后不设置默认支付方式
                if(set_pay_type)
                {
                    $('[data-pay-type="'+_self.selected_pay_action_type+'"]').find('[data-role="yes-tag"]')
                        .removeClass('icon-select-no').addClass('icon-select-active');
                }
            }

            self.must_pay_money = utility.format_float(must_pay_money,2);

            if(self.must_pay_money<0)
            {
                self.must_pay_money = self.must_pay_money * -1;
            }

            _self.$need_price.html(self.must_pay_money);
            _self.$less_money.html('-￥'+less_money);

            if(coupon)
            {
                _self.$coupon_money_text.html('-￥'+coupon);
            }
            else
            {
                _self.$coupon_money_text.html('');
            }



        },
        // tt 支付请求
        pay_action : function(params,callback)
        {
            var $loading=$.loading
            ({
                content:'发送中...'
            });

            var url = 'join_again_act.php';

            utility.ajax_request
            ({
                url : window.$__ajax_domain+url,
                data : params,
                type : 'POST',
                success : function(res)
                {
                    $loading.loading("hide");

                    if(res.result_data && res.result_data.code>0)
                    {

                        // 支付成功
                        callback.success.call(this,res);

                        var type = 'success';
                    }
                    else
                    {
                        // 支付失败

                        var type = 'warn';

                        $.tips
                        ({
                            content:res.result_data.message,
                            stayTime:3000,
                            type:type
                        });
                    }



                },
                error : function()
                {
                    callback.error.call(this);

                    $loading.loading("hide");

                    $.tips
                    ({
                        content:'网络异常',
                        stayTime:3000,
                        type:'warn'
                    });
                }
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
        /**
         * 控制第三方支付显示隐藏
         * @param options
         */
        control_other_pay_item : function(options)
        {
            var self = this;

            if(options.show)
            {
                $('[data-role="other-pay-container"]').removeClass('fn-hide');
                $('[data-role="must-pay-container"]').removeClass('last-sec-container');
            }
            else
            {
                $('[data-role="other-pay-container"]').addClass('fn-hide');
                $('[data-role="must-pay-container"]').addClass('last-sec-container');
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



/**
 * Created by hudingwen on 15/7/21.
 */
 
});