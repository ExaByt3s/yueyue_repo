define(function(require,exports,module){
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var View = require('../../common/view');
    var Scroll = require('../../common/scroll');
    var utility = require('../../common/utility');
    var m_alert = require('../../ui/m_alert/view');
    var App = require('../../common/I_APP');
    var pay_items =require('../../widget/pay_item/view');
    var pay_model =require('../model');
    var WX = require('../../common/I_WX');
    var WeiXinSDK = require('../../common/WX_JSSDK');
    var global_config = require('../../common/global_config');


    var mainTpl = require('./tpl/main.handlebars');

    var payment_view = View.extend({
        attrs:
        {
            template:mainTpl
        },
        events:
        {
            'tap [data-role=page-back]' : function (ev)
            {
                var self = this;

                page_control.back();
            },
            'tap [data-role=pay]' : function (ev)
            {
                var self = this;

                var loc = window.location;

                var data =
                {
                    event_id : self.model.get('event_id'),
                    enroll_id_arr : [self.enroll_id],
                    third_code : self.pay_item_obj.model.get('pay_type') || '',
                    is_available_balance : self.pay_item_obj.model.get('can_use_balance') ? 1 : 0,
                    available_balance : utility.user.get('available_balance'),
                    has_join : self.enroll_id,
                    user_id  : utility.login_id,
                    url:loc.origin + loc.pathname + loc.search,
                    table_arr : self.model.get('table_arr'),
                    phone : self.model.get('phone')
                };

                self.model.set(data);

                if(confirm('确定支付?'))
                {
                    self.model.join_again_act(data);
                }
            }
        },

        _setup_events:function()
        {
            var self = this;

            self.model.off('success:pay:fetch');



            utility.user.on('before:get_info:fetch',function()
            {

                m_alert.show('正在加载支付模块...','loading',{delay:-1});

            }).on('success:get_info:fetch',function(response)
            {

                m_alert.hide();

                // 请求成功后再次设置
                //self.pay_item_obj.model.set('available_balance',utility.user.get('available_balance'));

                // 支付选项参数模型
                var pay_items_model =
                    {
                        can_use_balance : utility.user.get('available_balance')>0?true:false,
                        available_balance : utility.user.get('available_balance'),
                        total_price : self.model.get('total_budget'),
                        show_price_info : true
                    };

                // 初始化表单
                self.count_payment(pay_items_model);

            }).on('error:get_info:fetch',function()
            {
                m_alert.show('网络异常，需要重试','error',{delay:1000});

                var pay_items_model =
                    {
                        can_use_balance : utility.user.get('available_balance')>0?true:false,
                        available_balance : utility.user.get('available_balance'),
                        total_price : self.model.get('total_budget'),
                        show_price_info : true
                    };

                // 初始化表单
                self.count_payment(pay_items_model);

            });

            self.model.on('before:get_enroll_info:fetch',function()
                {
                    m_alert.show('加载中...','loading');
                })
                .on('success:get_enroll_info:fetch',function(response,options)
                {
                    m_alert.hide();

                    self._render_info(response);
                })
                .on('complete:get_enroll_info:fetch',function()
                {

                }).on('before:pay:fetch',function()
                {
                    m_alert.show('发送中...','loading');
                })
                .on('success:pay:fetch',function(response,options)
                {
                    m_alert.hide();

                    if(!utility.int(self.enroll_id))
                    {
                        switch (response.result_data.code)
                        {
                            // 余额支付成功
                            case 2:

                                m_alert.show(response.result_data.message, 'right',
                                    {
                                        delay: 1000
                                    });

                                var loc = window.location;

                                loc.href = loc.origin + loc.pathname + '?pay_success=1#' + 'act/pay_success/'+self.model.get('event_id');


                                //page_control.navigate_to_page('act/pay_success/'+self.model.get('event_id'));
                                break;
                            // 生成请求参数成功，待跳转到第三方
                            case 3:

                                m_alert.show(response.result_data.message, 'right');

                                var third_code = response.result_data.third_code;

                                var channel_return = response.result_data.channel_return;

                                var data_url = response.result_data.data;

                                var payment_no = response.result_data.payment_no;

                                if(third_code == 'alipay_purse')
                                {
                                    // 支付宝

                                    App.alipay
                                    ({
                                        alipayparams : data_url,
                                        payment_no : payment_no
                                    },function(data)
                                    {
                                        var result = utility.int(data.result);

                                        debugger;

                                        self.after_pay_text(result);

                                        if(result == 1 || result == -1 || result == -2)
                                        {
                                            window.location.href = channel_return;

                                            debugger;
                                        }
                                    });
                                }
                                else if(third_code == 'tenpay_wxapp')
                                {
                                    // 微信支付

                                    App.wxpay(JSON.parse(data_url),function(data)
                                    {
                                        var result = utility.int(data.result);

                                        self.after_pay_text(result);

                                        if(result == 1 || result == -1 || result == -2)
                                        {
                                            window.location.href = channel_return;
                                        }
                                    });
                                }
                                else if(third_code == 'tenpay_wxpub')
                                {
                                    // 微信支付

                                    if(WeiXinSDK.isWeiXin() && global_config.WeiXin_Version >= "6.0.2")
                                    {

                                        console.log(JSON.parse(data_url));

                                        WeiXinSDK.setConfig(response.result_data.wx_sign_package);

                                        WeiXinSDK.ready(function(Api)
                                        {
                                            WeiXinSDK.chooseWXPay(JSON.parse(data_url),function(data)
                                            {

                                                var result = utility.int(data.code);

                                                if(result == 1)
                                                {
                                                    window.location.href = channel_return;
                                                }
                                            });
                                        });
                                    }
                                    else
                                    {
                                        WX.isWexXin() && WX.wxPay(JSON.parse(data_url),function(data)
                                        {
                                            var result = utility.int(data.code);

                                            if(result == 1)
                                            {
                                                window.location.href = channel_return;
                                            }
                                        });
                                    }


                                }

                                break;
                            // 支付失败
                            case -1:
                            case -2:
                            case -3:
                            case -4:
                            case -5:
                            case -6:
                            case -7:
                            case -8:
                            case -9:
                            case -10:
                            case -11:
                            case -12:
                            case -20:
                            case -21:
                                m_alert.show("支付失败"+response.result_data.message, 'error',
                                    {
                                        delay: 1000
                                    });
                                break;
                        }
                    }
                    else
                    {
                        switch (response.result_data.code)
                        {
                            // 余额支付成功
                            case 1:
                                debugger;
                                m_alert.show(response.result_data.message, 'right',
                                    {
                                        delay: 1000
                                    });

                                var loc = window.location;

                                loc.href = loc.origin + loc.pathname + '?pay_success=1#' + 'act/pay_success/'+self.model.get('event_id');

                                //page_control.navigate_to_page('act/pay_success/'+self.model.get('event_id'));
                                break;
                            // 生成请求参数成功，待跳转到第三方
                            case 2:

                                m_alert.show(response.result_data.message, 'right');

                                var third_code = response.result_data.third_code;

                                var channel_return = response.result_data.channel_return;

                                var data_url = response.result_data.data;

                                var payment_no = response.result_data.payment_no;

                                if(third_code == 'alipay_purse')
                                {
                                    // 支付宝

                                    App.alipay
                                    ({
                                        alipayparams : data_url,
                                        payment_no : payment_no
                                    },function(data)
                                    {
                                        var result = utility.int(data.result);

                                        debugger;

                                        self.after_pay_text(result);

                                        if(result == 1 || result == -1 || result == -2)
                                        {
                                            window.location.href = channel_return;

                                            debugger;
                                        }
                                    });
                                }
                                else if(third_code == 'tenpay_wxapp')
                                {
                                    // 微信支付

                                    App.wxpay(JSON.parse(data_url),function(data)
                                    {
                                        var result = utility.int(data.result);

                                        self.after_pay_text(result);

                                        if(result == 1 || result == -1 || result == -2)
                                        {
                                            window.location.href = channel_return;
                                        }
                                    });
                                }
                                else if(third_code == 'tenpay_wxpub')
                                {
                                    // 微信支付

                                    if(WeiXinSDK.isWeiXin() && global_config.WeiXin_Version >= "6.0.2")
                                    {

                                        console.log(JSON.parse(data_url));

                                        WeiXinSDK.setConfig(response.result_data.wx_sign_package);

                                        WeiXinSDK.ready(function(Api)
                                        {
                                            WeiXinSDK.chooseWXPay(JSON.parse(data_url),function(data)
                                            {

                                                var result = utility.int(data.code);

                                                if(result == 1)
                                                {
                                                    window.location.href = channel_return;
                                                }
                                            });
                                        });
                                    }
                                    else
                                    {
                                        WX.isWexXin() && WX.wxPay(JSON.parse(data_url),function(data)
                                        {
                                            var result = utility.int(data.code);

                                            if(result == 1)
                                            {
                                                window.location.href = channel_return;
                                            }
                                        });
                                    }


                                }

                                break;
                            // 支付失败
                            case -1:
                            case -2:
                            case -3:
                            case -10:
                            case -11:
                            case -12:
                            case -20:
                            case -21:
                                m_alert.show("支付失败"+response.result_data.message, 'error',
                                    {
                                        delay: 1000
                                    });
                                break;
                        }
                    }



                })
                .on('complete:pay:fetch',function()
                {

                });

            self.on('update_info',function()
            {
                if(!self.view_scroll_obj)
                {

                    self._setup_scroll();

                    self.view_scroll_obj.refresh();
                    return;
                }

                self.view_scroll_obj.refresh();
            });


        },
        /**
         * 计算支付金额
         * @private
         */
        count_payment : function(params)
        {
            var self = this;

            var total_price = params.total_price;

            var balance = params.available_balance;

            if(params.can_use_balance)
            {
                var must_pay_money = balance - total_price;

                // 信用金够钱支付的时候
                if(must_pay_money>0)
                {
                    self.must_pay_money = 0;
                }
                else
                {
                    self.must_pay_money = -(balance - total_price);
                }

            }
            else
            {
                // 没有用信用金支付

                self.must_pay_money = total_price;

            }

            self.must_pay_money = utility.format_float(self.must_pay_money,2);

            self.pay_item_obj.model.set('need_price',self.must_pay_money);

            if(self.must_pay_money>0)
            {

                // 余额不够支付,出现第三方支付选择
                if(!self.pay_item_obj)
                {
                    m_alert.show('第三方支付模块尚未加载!','error')
                    return;
                }

                // 隐藏支付宝、财付通、微信支付
                self.pay_item_obj.hide_pay_items_by_type('alipay_purse');
                self.pay_item_obj.hide_pay_items_by_type('caifutong');
                self.pay_item_obj.hide_pay_items_by_type('tenpay_wxapp');

                self.pay_item_obj.show_other_pay_items();

                self.pay_item_obj._select_pay_type('tenpay_wxpub');
            }
            else
            {
                self.pay_item_obj.hide_other_pay_items();
            }
        },
        _render_info : function(response)
        {
            var self = this;

            self.$('[data-role="tel"]').html(self.model.get('phone'));
            self.$('[data-role="amount"]').html(self.model.get('enroll_num')+'人');
            self.$('[data-role="session"]').html(self.model.get('table_info'));
            self.$('[data-role="event_title"]').html(self.model.get('event_title'));
            self.$('[data-role="budget"]').html('￥'+self.model.get('budget'));

            console.log(utility.user.get('available_balance'))

            // 支付选项参数模型
            var pay_items_model =
            {
                can_use_balance : utility.user.get('available_balance')>0?true:false,
                available_balance : utility.user.get('available_balance'),
                total_price : self.model.get('total_budget'),
                show_price_info : true
            };

            // 支付模块
            self.pay_item_obj = new pay_items
            ({
                templateModel : pay_items_model,
                parentNode : self.$pay_item_container
            }).render();

            // 隐藏第三方支付
            self.pay_item_obj.hide_other_pay_items();

            // 计算金额
            //self.count_payment(pay_items_model);

            //触发第三方支付选择
            self.pay_item_obj.on('selected:available_balance',function()
            {
                self.count_payment
                ({
                    can_use_balance : self.pay_item_obj.model.get('can_use_balance'),
                    total_price : self.model.get('total_budget'),
                    available_balance : utility.user.get('available_balance')
                });

                if(self.pay_item_obj._is_select_balance_require == 0)
                {
                    setTimeout(function()
                    {
                        self.view_scroll_obj.scrollTo(0,200);
                    },100)
                }

                self.view_scroll_obj.refresh();
            });

            self.trigger('update_info');
        },
        /**
         * 安装滚动条
         * @private
         */

        _setup_scroll : function()
        {
            var self = this;
            var view_scroll_obj = Scroll(self.$container,
                {
                    is_hide_dropdown : true
                });

            self.view_scroll_obj = view_scroll_obj;


        },
        setup : function()
        {

            var self = this;

            // 每次进入确保拿最新数据
            utility.user.get_info();

            self.$container = self.$('[data-role="container"]');
            self.$pay_item_container = self.$('[data-role="pay-items-container"]');

            self.enroll_id = self.get('enroll_id');

            if(self.enroll_id)
            {
                self.model.set('enroll_id',self.enroll_id);
            }



            self._setup_events();

            self.model.get_enroll_info(self.model.toJSON());

            self._reset_container_height();


        },
        render : function()
        {
            var self = this;

            View.prototype.render.apply(self);

            self.trigger('render');

            return self;
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
        },
        _reset_container_height : function()
        {
            var self = this;

            self.$container.height(window.innerHeight-97);

        }

    });

    module.exports = payment_view;
});