/**
 * Created by nolestLam on 2015/3/31.
 */
define(function(require, exports, module)
{
    var $ = require('$');
    var View = require('../../../common/view');
    var utility = require('../../../common/utility');
    var page_control = require('../../../frame/page_control');
    var level_3_ready_tpl = require('./tpl/main.handlebars');
    var m_alert = require('../../../ui/m_alert/view');
    var coupon_per = require('../../../widget/coupon/view');
    var Scroll = require('../../../common/scroll');
    var abnormal = require('../../../widget/abnormal/view');
    var pay_items =require('../../../widget/pay_item/view');
    var global_config = require('../../../common/global_config');
    var app = require('../../../common/I_APP');


    var level_3_ready_view = View.extend
    ({
        attrs:
        {
            template: level_3_ready_tpl,
            coupon_map : {}
        },
        events :
        {
            'tap [data-role="page-back"]' : function()
            {
                var self = this;

                page_control.back();
            },
            'tap [data-role="re_take_photo"]' : function()
            {
                var self = this;

                page_control.back();
            },
            'click [data-role="submit"]' : function()
            {
                var self = this;

                var data =
                    {
                        type:'recharge',
                        amount : 300,
                        third_code : self.pay_item_obj.model.get('pay_type'),
                        redirect : 'mine/level_3/status',
                        is_refresh : true
                    };

                if(confirm('确认支付?'))
                {
                    utility.user.send_recharge(data);
                }



            }
        },
        _get_img_w_h : function(){
            var self = this;
            var w_h ={};
            w_h.width = utility.get_view_port_width() - 30;
            w_h.height = 580 / 400 * w_h.width;
            return w_h;
        },
        _render_items : function(data)
        {
            var self = this;

            self.view_scroll_obj.refresh();
        },
        _setup_scroll : function()
        {
            var self = this;

            var view_scroll_obj = Scroll(self.$scroll_container,{
                lazyLoad : false,
                _startY : 45,
                prevent_tag : 'slider',
                is_hide_dropdown : true,
                down_direction : 'down',
                down_direction_distance :50
            });

            self.view_scroll_obj = view_scroll_obj;

            // 上拉刷新
            self.view_scroll_obj.on('dropload',function(e)
            {
                self.refresh();

                console.log("刷新");

            });

            // 下拉加载更多
            self.view_scroll_obj.on('pullload',function(e)
            {

            });
        },
        refresh : function()
        {
            var self = this;

            //self.model.get_coupon_list(self.submit_data);
        },
        _setup_events : function()
        {
            var self = this;

            self.on('render',function()
            {
                //self.model.get_coupon_list(self.submit_data);

                self._setup_scroll();
            });

            if(!self.pay_item_obj)
            {
                // 安装第三方支付
                self._setup_pay_items();
            }

            // modify by hudw
            // 2015.4.8 增加支付模块
            utility.user.on('before:send_rechare:fetch',function()
            {
                m_alert.show('加载中...','loading');
            })
                .on('success:send_rechare:fetch',function(response,options)
                {
                    //m_alert.hide();

                    var response = response.result_data;
                    var code = response.code;
                    var msg = response.msg;
                    var data = response.data;
                    var channel_return = response.channel_return;
                    var third_code = response.third_code;
                    var payment_no = response && response.payment_no;


                    if( code == 1)
                    {
                        m_alert.show('第三方支付跳转','right',{delay:1000});

                        if(third_code == 'alipay_purse')
                        {
                            // 支付宝
                            app.alipay
                            ({
                                alipayparams : data,
                                payment_no : payment_no
                            },function(res)
                            {
                                var result = utility.int(res.result);

                                self.after_pay_text(result);

                                if(result == 1 || result == -1 || result == -2)
                                {
                                    window.location.href = channel_return;
                                }
                            });
                        }
                        else if(third_code == 'tenpay_wxapp')
                        {
                            // 微信支付

                            app.wxpay(JSON.parse(data),function(res)
                            {
                                var result = utility.int(res.result);

                                self.after_pay_text(result);

                                if(result == 1 || result == -1 || result == -2)
                                {
                                    window.location.href = channel_return;
                                }
                            });
                        }


                    }
                    else
                    {
                        m_alert.show(msg,'error',{delay:1000});

                    }


                })
                .on('error:send_rechare:fetch',function()
                {
                    m_alert.hide();
                })
        },
        /**
         * 安装第三方支付选择模块
         * hudw 2014.12.9
         * @private
         */
        _setup_pay_items : function()
        {
            var self = this;
            // 支付选项参数模型
            var pay_items_model =
                {
                    can_use_balance : false,
                    total_price : 300,
                    show_price_info : false,
                    is_support_outtime : true,
                    is_support_now_out : true
                };

            self.$pay_items_container = self.$('[data-role="pay"]');

            self.pay_item_obj = new pay_items
            ({
                templateModel : pay_items_model,
                parentNode : self.$pay_items_container
            }).render();

            self.pay_item_obj.show_other_pay_items();

            self.pay_item_obj._select_pay_type('alipay_purse');

            self.$('.pay-details-payment-title').addClass('fn-hide');
        },
        setup : function()
        {
            var self = this;

            self.$scroll_container = self.$('[data-role="content-body"]');

            self.$container = self.$('[data-role="list-container"]');

            self.fetching = false;

            self.submit_data =
            {
                page : 1
            };

            self._setup_events();
        },
        render : function()
        {
            var self = this;

            View.prototype.render.apply(self);

            self.trigger('render');

            return self;
        },
        _drop_reset : function()
        {
            var self = this;

            self.view_scroll_obj && self.view_scroll_obj.resetload();
        },
        show : function()
        {
            var self = this;

            self.$el.removeClass('fn-hide');
        },
        hide : function()
        {
            var self = this;

            self.$el.addClass('fn-hide');
        },
        /**
         * 显示支付结束后的文案
         * @param code
         */
        after_pay_text : function(code)
        {
            switch (utility.int(code))
            {
                case 1:
                case -2:
                case -1:
                    m_alert.show('支付成功','right');
                    break;
                case 0:
                    m_alert.show('其它错误','error');
                    break;
                case -3:
                    m_alert.show('支付失败','error');
                    break;
                case -4:
                    m_alert.show('支付取消','error');
                    break;
            }
        }
    });

    module.exports = level_3_ready_view;
});