/**
 * 支付页面
 * hudw 2014.8.28
 */
define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var View = require('../../common/view');
    var payment = require('../payment/tpl/main.handlebars');
    var utility = require('../../common/utility');
    var templateHelpers = require('../../common/template-helpers');
    var Scroll = require('../../common/scroll');
    var global_config = require('../../common/global_config');
    var App = require('../../common/I_APP');
    var m_alert = require('../../ui/m_alert/view');
    var pay_items =require('../../widget/pay_item/view');
    var coupon_view = require('../../coupon/choose/view');
    var coupon_model = require('../../coupon/model');
    var dialog = require('../../ui/dialog/index');


    var payment_view = View.extend
    ({

        attrs:
        {
            template: payment
        },
        events :
        {
            'swiperight' : function (){
                page_control.back();
            },
            'tap [data-role=web-page-back]' : function ()
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
            'click [data-role="pay-btn"]' : function()
            {
                var self = this;

                // app 统计事件
                App.isPaiApp && App.analysis('eventtongji',global_config.analysis_event['date_pay']);

                if(!self.pay_btn_can_use)
                {
                    return;
                }

                if(self._submit_disable)
                {
                    return;
                }

                self._submit_disable = true;

                if(confirm('确定支付?'))
                {
                    self.pay_action();
                }
                else
                {
                    self._submit_disable = false;
                }


            }
        },

        /**
         * 安装事件
         * @private
         */
        _setup_events : function()
        {

            var self = this;

            // 安装使用优惠劵模块
            self.pay_item_obj.on('selected:coupon',function()
            {
                if(self.must_pay_money == null || self.must_pay_money == 'undefined')
                {
                    m_alert.show('正在计算支付总价，请稍等','right');

                    return;
                }

                var params_info =
                    {
                        module_type : 'yuepai',
                        order_total_amount : self.model.get('model_selected_info').total_price,
                        model_user_id : self.model.get('user_id'),
                        date_id : self.model.get('model_selected_info').date_id,
                        channel_module :'yuepai'
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
                            select_balance : select_balance,
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


            });



            // 监听支付对象的变化
            self.pay_item_obj.on('selected:change',function(obj)
            {


            });

            self.pay_model
                .on('before:fetch', function()
                {
                    m_alert.show('提交中...', 'loading',
                        {
                            delay : -1
                        });
                }).on('success:fetch', function(response,options)
                {

                    self._submit_disable = false;

                    var response = response && response.result_data;

                    var code = response && response.code;

                    var channel_return = response && response.channel_return;

                    var payment_no = response && response.payment_no;

                    var options = options;


                    switch (code)
                    {
                        // 余额支付成功
                        case 1:

                            m_alert.show(response.message,'right');

                            page_control.navigate_to_page('model_date/submit_success');
                            break;
                        // 生成请求参数成功，待跳转到第三方
                        case 2:
                            m_alert.show('第三方支付跳转', 'right');

                            var data_url = response.data;

                            var third_code = response.third_code;

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

                                    self.after_pay_text(result);

                                    if(result == 1 || result ==-1 || result == -2)
                                    {
                                        window.location.href = channel_return;
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

                                    if(result == 1 || result ==-1 || result == -2)
                                    {
                                        window.location.href = channel_return;
                                    }
                                });
                            }



                            break;
                        // 参数错误
                        case -1:
                        // 用户余额有变动
                        case -2:
                        // 添加到约拍表时产生错误
                        case -3:
                        // 生成第三方请求参数产生错误
                        case -4:
                            m_alert.show(response.message,'error');
                            break;
                        default :
                            m_alert.hide();

                    }


                })
                .on('error:fetch', function (xhr, status)
                {
                    m_alert.show('网络出错','error',{delay:2000});

                    self._submit_disable = false;
                })
                .on('complete:fetch', function (xhr, status)
                {
                    //m_alert.hide();

                    self._submit_disable = false;
                });

            utility.user.once('before:get_info:fetch',function()
            {

            }).once('success:get_info:fetch',function(response)
            {


                // 请求成功后再次设置
                self.pay_item_obj.model.set('available_balance',utility.user.get('available_balance'));

                // 初始化表单
                self.count_payment
                ({
                    select_balance : self.model.get('model_selected_info').can_use_balance
                });

            }).once('error:get_info:fetch',function()
            {
                m_alert.show('网络异常，需要重试','error',{delay:1000});

                // 初始化表单
                self.count_payment
                ({
                    select_balance : self.model.get('model_selected_info').can_use_balance
                });

                page_control.back();

            });

            self.pay_item_obj.on('selected:available_balance',function()
            {
                var select_balance = self.pay_item_obj.model.get('can_use_balance');

                self.count_payment
                ({
                    coupon : self.pay_item_obj.model.get('coupon_info')  && self.pay_item_obj.model.get('coupon_info').face_value,
                    select_balance:select_balance
                });

                self.view_scroll_obj.refresh();

            });

            self._setup_scroll();

            self.view_scroll_obj.refresh();
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
        pay_action : function() {
            var self = this;

            var model_selected_info_obj = self.model.get('model_selected_info');
            var coupon_info_obj = self.pay_item_obj.model.get('coupon_info');

            var model_id = self.model.get('user_id');
            var hour = model_selected_info_obj.hour;
            var date = model_selected_info_obj.date;
            var type = model_selected_info_obj.type;
            var style = model_selected_info_obj.style;
            var price = model_selected_info_obj.price;
            var address = model_selected_info_obj.address;
            var is_from_custom_data = model_selected_info_obj.is_from_custom_data;
            var available_balance = utility.user.get('available_balance');
            var is_available_balance = self.pay_item_obj.model.get('can_use_balance');
            var limit_num = model_selected_info_obj.limit_num;
            var module_type = model_selected_info_obj.module_type || 'yuepai';
            var use_coupon = model_selected_info_obj.use_coupon || false;
            var coupon_sn =  (coupon_info_obj && coupon_info_obj.coupon_sn) || '';
            var date_id = model_selected_info_obj.date_id || false;

            var direct_confirm_id = 0;
            if(self.model.get('model_selected_info').direct_confirm_id)
            {
                direct_confirm_id = self.model.get('model_selected_info').direct_confirm_id;
            }

            // 设置打折优惠
            if (self.model.get('model_selected_info').is_discount)
            {
                hour = 2;
                price = utility.int(price/2);
            }

            var data =
                {
                    model_id : model_id,
                    hour : hour,
                    date : date,
                    type : type || '',
                    style : style,
                    price : price,
                    address : address,
                    available_balance : available_balance,
                    is_available_balance : is_available_balance,
                    limit_num : limit_num,
                    third_code : self.pay_item_obj.model.get('pay_type'),
                    is_from_custom_data : is_from_custom_data,
                    direct_confirm_id : direct_confirm_id,
                    module_type : module_type,
                    use_coupon : use_coupon,
                    coupon_sn : coupon_sn,
                    date_id : date_id

                };


            self.pay_model.set('pay_info',data);

            utility.date_info.set
            ({
                model : self.model,
                pay_ment_model : self.pay_model
            });

            self.pay_model.pay(data);



        },
        /**
         * 计算支付金额
         * @private
         */
        count_payment : function(params)
        {
            var self = this;

            var total_price = utility.float(self.model.get('model_selected_info').total_price);

            var balance = utility.float(utility.user.get('available_balance'));

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

            // 出现第三方支付选择
            if(!self.pay_item_obj)
            {
                m_alert.show('第三方支付模块尚未加载!','error');
                return;
            }

            // 使用余额支付
            if(params.select_balance)
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
            self.$need_pay_str.html(self.must_pay_money);



        },
        /**
         * 视图初始化入口
         */
        setup : function()
        {

            var self = this;

            self.pay_btn_can_use = false;

            setTimeout(function()
            {
                self.pay_btn_can_use = true;
            },1500);

            // 每次进入确保拿最新数据
            utility.user.get_info();

            // 配置交互对象
            self.$container = self.$('[data-role=container]'); // 滚动容器

            self.$submit_btn = self.$('[data-role="pay-btn"]');
            self.$pay_items_container = self.$('[data-role="pay-items-container"]');
            self.$need_pay_str = self.$('[data-role="data-pay-rest"]');

            // 设置支付模型
            self.pay_model = self.get('pay_model');


            // 支付选项参数模型
            var pay_items_model =
                {
                    can_use_balance : self.model.get('model_selected_info').can_use_balance,
                    available_balance : utility.user.get('available_balance'),
                    total_price : self.model.get('model_selected_info').total_price,
                    show_price_info : true,
                    is_support_outtime : true,
                    is_support_now_out : true
                };

            // debugger;

            self.pay_item_obj = new pay_items
            ({
                templateModel : pay_items_model,
                parentNode : self.$pay_items_container
            }).render();


            self.pay_item_obj.hide_other_pay_items();




            // 安装事件
            self._setup_events();


        },
        render : function()
        {
            var self = this;

            var view_port_height = self.reset_viewport_height();

            self.$container.height(view_port_height);
            // 调用渲染函数
            View.prototype.render.apply(self);

            self.trigger('render');

            return self;
        },
        reset_viewport_height : function()
        {
            return utility.get_view_port_height() - 95;
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