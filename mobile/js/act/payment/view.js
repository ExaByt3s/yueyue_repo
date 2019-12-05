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
    var coupon_view = require('../../coupon/choose/view');
    var coupon_model = require('../../coupon/model');
    var dialog = require('../../ui/dialog/index');

    var mainTpl = require('./tpl/main.handlebars');

    var payment_view = View.extend({
        attrs:
        {
            template:mainTpl
        },
        events:
        {
            'tap [data-role=web-page-back]' : function (ev)
            {
                var self = this;

                if(!self.save_dialog)
                {
                    self._setup_dialog();
                }
                else
                {
                    self.save_dialog.show();
                }
            },
            'click [data-role=pay]' : function (ev)
            {
                var self = this;

                if(!self.pay_btn_can_use)
                {
                    return;
                }

                var loc = window.location;

                var coupon_info_obj = self.pay_item_obj.model.get('coupon_info');

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
                    phone : self.model.get('phone'),
                    coupon_sn : (coupon_info_obj && coupon_info_obj.coupon_sn) || '',
                    table_id : (self.model.get('table_arr') && self.model.get('table_arr')[0].table_id) || 0
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

                console.log(self.model.get('total_budget'))

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
                // 安装使用优惠劵模块
                self.pay_item_obj.on('selected:coupon',function()
                {
                    var table_id = 0;

                    if(self.model.get('table_arr'))
                    {
                        table_id = self.model.get('table_arr')[0].table_id;
                    }

                    var params_info =
                        {
                            module_type : 'waipai',
                            order_total_amount : self.model.get('total_budget'),
                            model_user_id : 0,
                            event_id : self.model.get('event_id'),
                            table_id : table_id,
                            enroll_id : self.enroll_id
                        };

                    params_info = encodeURIComponent(JSON.stringify(params_info));

                    // 初始化可选优惠劵视图
                    if(!self.coupon_choose_view)
                    {
                        self.coupon_choose_view = self._init_coupon_choose([params_info],
                            {
                                pay_item_obj : self.pay_item_obj
                            });

                    }

                    self.coupon_choose_view.show();

                    self.hide();

                    // 优惠劵确认事件
                    self.coupon_choose_view.on('page:confirm',function()
                    {
                        self.coupon_choose_view.hide();

                        self.show();

                        if(self.pay_item_obj.model.has('use_coupon'))
                        {

                            var select_balance = self.pay_item_obj.model.get('can_use_balance');

                            // 初始化表单
                            self.count_payment
                            ({
                                coupon : self.pay_item_obj.model.get('coupon_info')  && self.pay_item_obj.model.get('coupon_info').face_value,
                                can_use_balance : select_balance,
                                total_price : self.model.get('total_budget'),
                                available_balance : utility.user.get('available_balance'),
                                set_pay_type : false
                            });
                        }


                    });

                    // 优惠劵返回事件
                    self.coupon_choose_view.on('page:back',function()
                    {
                        self.coupon_choose_view.hide();

                        self.show();
                    });


                    /*page_control.navigate_to_page('coupon/choose/'+params_info,
                     {
                     pay_item_obj : self.pay_item_obj
                     });*/
                });

                // 监听支付对象的变化
                self.pay_item_obj.on('selected:change',function()
                {

                });


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

            var total_price = utility.float(params.total_price);

            var balance = utility.float(params.available_balance);

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
            var must_pay_money = balance - total_price;

            console.log(self.pay_item_obj)

            // 出现第三方支付选择
            if(!self.pay_item_obj)
            {
                m_alert.show('第三方支付模块尚未加载!','error');
                return;
            }

            // 使用余额支付
            if(params.can_use_balance)
            {

                less_money = must_pay_money;

                if(less_money <= 0)
                {
                    less_money = balance;
                }
                else
                {
                    less_money = total_price;

                    must_pay_money = 0;
                }

                // 余额完全够钱支付了订单
                if(must_pay_money>=0)
                {
                    self.pay_item_obj.hide_other_pay_items();
                }
                // 余额不够钱支付订单的时候，需要混合支付
                else
                {
                    self.pay_item_obj.show_other_pay_items();

                    // 设置默认支付方式，计算优惠劵后不设置默认支付方式
                    if(set_pay_type)
                    {
                        self.pay_item_obj._select_pay_type('alipay_purse');
                    }
                }
            }
            // 完全使用第三方支付
            else
            {
                // 需要给的钱就是总价 减去 优惠劵
                must_pay_money = total_price;

                self.pay_item_obj.show_other_pay_items();

                // 设置默认支付方式，计算优惠劵后不设置默认支付方式
                if(set_pay_type)
                {
                    self.pay_item_obj._select_pay_type('alipay_purse');
                }
            }

            self.must_pay_money = utility.format_float(must_pay_money,2);

            if(self.must_pay_money<0)
            {
                self.must_pay_money = self.must_pay_money * -1;
            }


            self.pay_item_obj.model.set('need_price',self.must_pay_money);
            self.pay_item_obj.model.set('less_money',less_money);



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
                    available_balance : utility.user.get('available_balance'),
                    coupon : self.pay_item_obj.model.get('coupon_info')  && self.pay_item_obj.model.get('coupon_info').face_value
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

            self.pay_btn_can_use = false;

            setTimeout(function()
            {
                self.pay_btn_can_use = true;
            },2000);

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

        },
        /**
         * 初始化优惠劵视图
         * @param route_params_arr
         * @param route_params_obj
         * @returns {*}
         * @private
         */
        _init_coupon_choose : function(route_params_arr,route_params_obj)
        {
            var self = this;

            var params = {};

            var coupon_model_obj = new coupon_model
            ({
                url : global_config.ajax_url.get_user_coupon_list_by_check
            });

            if(route_params_arr[0])
            {
                params = eval('('+decodeURIComponent(route_params_arr[0])+')');
            }

            coupon_model_obj.set(params);

            var coupon_view_obj = new coupon_view
            ({
                model : coupon_model_obj,
                parentNode : self.$el.parent(),
                route_params_obj : route_params_obj,
                is_not_page : true
            }).render();

            return coupon_view_obj;
        },
        _setup_dialog : function()
        {
            var self = this;

            self.save_dialog = new dialog
            ({
                content: '<p class="p5"> 取消订单后，与订单关联的优惠和促销将可能暂时无法使用。<br />如有疑问请联系：4000-82-9003</p>',
                buttons: [{
                    name: 'save',
                    text: '确定'
                },{
                    name: 'close',
                    text: '取消'
                }]
            });

            self.save_dialog.on('tap:button:save', function()
            {
                self.save_dialog.hide();

                page_control.back();
            })
            .on('tap:button:close', function()
            {
                self.save_dialog.hide();

            });

            self.save_dialog.show();
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
        }

    });

    module.exports = payment_view;
});