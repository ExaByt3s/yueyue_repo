/**
 * 支付页面
 * hudw 2014.8.28
 */
define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var View = require('../../common/view');
    var global_config = require('../../common/global_config');
    var payment = require('../payment/tpl/main.handlebars');
    var utility = require('../../common/utility');
    var templateHelpers = require('../../common/template-helpers');
    var Scroll = require('../../common/scroll');
    var App = require('../../common/I_APP');
    var m_alert = require('../../ui/m_alert/view');
    var pay_items =require('../../widget/pay_item/view');
    var WX = require('../../common/I_WX');
    var WeiXinSDK = require('../../common/WX_JSSDK');

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
            'tap [data-role=page-back]' : function ()
            {
                page_control.back();
            },
            'tap [data-role="pay-btn"]' : function()
            {
                var self = this;

                utility.page_pv_stat_action({tj_point:'pay_order'});

                if(self._submit_disable)
                {
                    return;
                }

                self._submit_disable = true;

                if(confirm('确认支付?'))
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
                        else if(third_code == 'tenpay_wxpub')
                        {
                            // 微信支付

                            if(WeiXinSDK.isWeiXin() && global_config.WeiXin_Version >= "6.0.2")
                            {

                                console.log(JSON.parse(data_url));

                                WeiXinSDK.setConfig(response.wx_sign_package);

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

            utility.user.on('before:get_info:fetch',function()
            {
               m_alert.show('正在加载支付模块...','loading',{delay:-1});

            }).on('success:get_info:fetch',function(response)
            {

                m_alert.hide();

                // 请求成功后再次设置
                self.pay_item_obj.model.set('available_balance',utility.user.get('available_balance'));

                // 初始化表单
                self.count_payment
                ({
                    select_balance : self.model.get('model_selected_info').can_use_balance
                });

            }).on('error:get_info:fetch',function()
            {
                m_alert.show('网络异常，需要重试','error',{delay:1000});

                // 初始化表单
                self.count_payment
                ({
                    select_balance : self.model.get('model_selected_info').can_use_balance
                });

            });

            self.pay_item_obj.on('selected:available_balance',function()
            {
                var select_balance = self.pay_item_obj.model.get('can_use_balance');

                self.count_payment({select_balance:select_balance});

                if(self.pay_item_obj._is_select_balance_require == 0)
                {
                    setTimeout(function()
                    {
                        self.view_scroll_obj.scrollTo(0,200);
                    },100)
                }

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

            var model_id = self.model.get('user_id');
            var hour = self.model.get('model_selected_info').hour;
            var date = self.model.get('model_selected_info').date;
            var type = self.model.get('model_selected_info').type;
            var style = self.model.get('model_selected_info').style;
            var price = self.model.get('model_selected_info').price;
            var address = self.model.get('model_selected_info').address;
            var available_balance = utility.user.get('available_balance');
            var is_available_balance = self.pay_item_obj.model.get('can_use_balance');
            var limit_num = self.model.get('model_selected_info').limit_num;
            var is_from_custom_data = self.model.get('model_selected_info').is_from_custom_data;
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

            var loc = window.location;

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
                wx_version : global_config.WeiXin_Version,
                url:loc.origin + loc.pathname + loc.search,
                direct_confirm_id : direct_confirm_id

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

            if(params.select_balance)
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

            self.$need_pay_str.html(self.must_pay_money);

            //self.pay_item_obj.update_price(self.must_pay_money);

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
        /**
         * 视图初始化入口
         */
        setup : function()
        {

            var self = this;

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
        }

    });

    module.exports = payment_view;
});