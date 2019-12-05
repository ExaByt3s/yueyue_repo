/**
 *
 *  活动报名页面
 */
define(function(require,exports,module){
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var view = require('../../common/view');
    var scroll = require('../../common/scroll');
    var utility = require('../../common/utility');
    var tip = require('../../ui/m_alert/view');
    var App = require('../../common/I_APP');
    var choosen_group_view = require('../../widget/choosen_group/view');
    var number_btn_view = require('../../widget/number_btn/view');
    var pay_items =require('../../widget/pay_item/view');
    var pay_model =require('../model');
    var WX = require('../../common/I_WX');
    var WeiXinSDK = require('../../common/WX_JSSDK');


    var main_tpl = require('./tpl/main.handlebars');

    var action_apply_view = view.extend({
        attrs:{
            template : main_tpl
        },
        events:{
            'swiperight' : function (){
                page_control.back();
            },
            'tap [data-role=page-back]' : function (ev)
            {
                var self = this;

                page_control.back();
            },
            'tap [data-role=pay]' : function (ev)
            {
                var self = this;

                if(!self.table_selected)
                {
                    tip.show('请选择场次', 'error', {
                        delay: 1000
                    });
                    return;
                }

                if(self.is_can_select)
                {
                    tip.show('你已经报了该场次，请选择其他场次', 'error', {
                        delay: 1000
                    });
                    return;
                }

                var pay_arr = {};
                pay_arr.phone = self.$phone_number.val();
                pay_arr.event_id = self.model.get('event_id');
                //self.pay_item_obj.get_value();

                var enroll_num = self.action_number_btn_view.get_value();
                var table_id =  self.action_choosen_group_view.get_value()[0].id

                var wrong_txt;

                pay_arr.phone || (wrong_txt = '请输入手机号');
                (pay_arr.phone.length ==11) || (wrong_txt = '请输入11位手机的号');
                self.action_choosen_group_view.get_value()[0].id || (wrong_txt = '请选择一个组');
                self.action_number_btn_view.get_value() || (wrong_txt = '至少需要一人参加');


                /*pay_arr.available_balance = utility.user.get('available_balance');
                pay_arr.is_available_balance = self.pay_item_obj.model.get('can_use_balance');
                pay_arr.third_code = self.pay_item_obj.model.get('pay_type');

                pay_arr.table_arr =
                [{
                    enroll_num : enroll_num,
                    table_id : table_id
                }];*/

                if(wrong_txt){
                    tip.show(wrong_txt, 'error', {
                        delay: 800
                    });
                    return;
                }


                //self.pay_model_obj.join_act(pay_arr);

                // 整合专递到支付页面的参数
                var pay_params =
                {
                    phone       : pay_arr.phone,
                    event_id    : self.get('event_id'),
                    event_title : self.model.get('act_intro').title,
                    table_id  : self.action_choosen_group_view.get_value()[0].id,
                    table_info    : self.action_choosen_group_view.get_value()[0].text,
                    budget      : self.model.get('act_info').budget,
                    enroll_num  : self.action_number_btn_view.get_value(),
                    table_arr   :
                    [{
                        enroll_num : enroll_num,
                        table_id : table_id
                    }],
                    is_count_money : 1
                };

                console.log(pay_params);

                pay_params = encodeURIComponent(JSON.stringify(pay_params));

                // 第一次报名和支付，enroll_id为0
                page_control.navigate_to_page('act/payment/'+self.get('event_id')+'/0/'+pay_params);

            }
        },

        _setup_events:function(){
            var self = this;

            

            // 模型事件
            // --------------------
            /*self.pay_model_obj.on('before:join_act:fetch', self._join_act_before, self)
                .on('success:join_act:fetch', self._join_act_success, self)
                .on('error:join_act:fetch', self._join_act_error, self)
                .on('complete:join_act:fetch', self._join_act_complete, self);*/

            self.model.on('before:get_list:fetch',function()
            {
                tip.show('加载中...','loading',{delay:-1});

            })
            .on("success:fetch",function(response,options)
            {
                tip.hide();

                if(!self.action_choosen_group_view)
                {
                    self._setup_choosen_group();

                    // 默认选中1个人
                    self.count_payment(1);

                    self.view_scroll_obj.refresh();
                }

                if(response.result_data.user_info)
                {
                    utility.user.update_user(response.result_data.user_info);
                }

            }).on("error:fetch",function()
            {
                tip.show('网络异常', 'error');

                self.model.get_list(true);

            });

            /*utility.user.on('before:get_info:fetch',function()
            {

            }).on('success:get_info:fetch',function(response)
            {

                utility.user.update_user(response.result_data.data);

                // 请求成功后再次设置
                self.pay_item_obj.model.set('available_balance',utility.user.get('available_balance'));


            }).on('error:get_info:fetch',function()
            {
                tip.show('网络异常，需要重试','error',{delay:1000});

                page_control.back();

            });*/

            //报名人数输入框事件绑定
            self.action_number_btn_view.$number_input.on("input",function(ev)
            {
                var $target = $(ev.currentTarget);
                self.count_payment($target.val());
            });

            self.action_number_btn_view.on('add_value:success',function(value)
            {
                self.count_payment(value);
            });

            self.action_number_btn_view.on('minus:success',function(value)
            {
                self.count_payment(value);
            });

            /*self.pay_item_obj.on('selected:available_balance',function()
            {
                var select_balance = self.pay_item_obj.model.get('can_use_balance');

                self.count_payment(self.action_number_btn_view.get_value());


                self.view_scroll_obj.refresh();
            });*/



            if(!self.view_scroll_obj)
            {

                self._setup_scroll();

                self.view_scroll_obj.refresh();
                return;
            }

            self.view_scroll_obj.refresh();
        },

        /**
         * 安装滚动条
         * @private
         */

        _setup_scroll : function()
        {
            var self = this;
            var view_scroll_obj = scroll(self.$container,
                {
                    is_hide_dropdown : true
                });

            self.view_scroll_obj = view_scroll_obj;

            //self.view_scroll_obj.refresh();
        },

        /**
         * 安装选择组模块
         * @private
         */
        _setup_choosen_group : function()
        {
            var self = this;

            var table_info = self.model.get('act_arrange').table_info;

            // 设置是否报名了
            if(table_info)
            {
                for(var i =0;i<table_info.length;i++)
                {
                    table_info[i].params = table_info[i].is_duplicate ? 1 : 0;
                    table_info[i].normal_status = table_info[i].table_status;

                    /*switch (table_info[i].table_status)
                    {
                        case 1 :
                            table_info[i].status_txt = '（已过期）';
                            break;
                        case 2 :
                            table_info[i].status_txt = '（未支付）';
                            break;
                        case 3 :
                            break;
                    }

                    */

                    var font_color = ''

                    if(table_info[i].table_status == 1)
                    {
                        var font_color = 'light_status_txt';

                        table_info[i].readonly = 1;
                    }
                    else if (table_info[i].table_status == 3)
                    {
                        var font_color = 'light_status_txt';

                        table_info[i].readonly = 1;
                    }



                    table_info[i].id = table_info[i].id;
                    table_info[i].remark = table_info[i].enroll_id;

                    table_info[i].text = '<span class="'+font_color+'">'+table_info[i].text+'</span>' + '<span class="status_txt '+font_color+'">'+table_info[i].table_text+'</span>'

                }

                self.action_choosen_group_view = new choosen_group_view
                ({
                    templateModel :
                    {
                        list : table_info
                    },
                    btn_per_line:1,
                    line_margin: '0px 0px 10px 0px',
                    parentNode: self.$session,
                    is_multiply : false
                }).render();
            }






            // 选择组选中事件
            self.action_choosen_group_view && self.action_choosen_group_view.on('success:selected',function(obj)
            {

                self.view_scroll_obj.refresh();

                self.table_selected = true;

                self.is_can_select = !obj.params;

                //2015.2.27 已经支付的场次不能再次支付
                if(obj.normal_status == 1)
                {
                    tip.show("该场次已经支付了",'right');

                    self.$pay_btn.addClass('fn-hide');
                    self.$unpay_btn.removeClass('fn-hide');
                    self.$('[data-has-selected="1"]').addClass('fn-hide');

                    return;
                }
                //未支付，属于已经报名未支付
                else if (obj.normal_status == 2)
                {
                    self.$('[data-has-selected="1"]').addClass('fn-hide');
                    page_control.navigate_to_page('act/payment/'+self.get('event_id')+'/'+obj.remark);

                    return;
                }
                //场次已经过期
                else if (obj.normal_status == 3)
                {
                    tip.show("该场次已经过期了",'right');

                    self.$('[data-has-selected="1"]').addClass('fn-hide');
                    self.$pay_btn.addClass('fn-hide');
                    self.$unpay_btn.removeClass('fn-hide');

                    return;
                }
                else
                {
                    self.$('[data-has-selected="1"]').removeClass('fn-hide');
                }




                self.$pay_btn.removeClass('fn-hide');
                self.$unpay_btn.addClass('fn-hide');

                /*if(utility.int(obj.normal_status)  )
                {
                    if(!utility.int(obj.remark))
                    {
                        alert('场次已过期');

                        return;
                    }

                    page_control.navigate_to_page('act/payment/'+self.get('event_id')+'/'+obj.remark);

                    return;
                }*/


                //self.pay_item_obj.show();

                // 默认选中1个人
                //self.count_payment(self.action_number_btn_view.get_value());


            });


        },
        /**
         * 安装数字按钮
         * @private
         */
        _setup_number_btn : function()
        {
            var self = this;

            self.action_number_btn_view = new number_btn_view
            ({
                templateModel :
                {
                    type : 'tel',
                    disable : true
                },
                min_value : 1,
                max_value : 3,
                step : 1,
                parentNode: self.$amount,
                value : 1
            }).render();

        },

        setup : function()
        {

            var self = this;

            utility.user.get_info();

            self.$container = self.$('[data-role=container]'); // 滚动容器
            self.$phone_number = self.$('[data-role=phone-number]');
            self.$amount = self.$('[data-role=amount]'); // 报名人数容器
            self.$session = self.$('[data-role=session]'); // 选择场次容器
            self.$pay_items_container = self.$('[data-role=pay-items-container]');
            self.budget = utility.int(self.model.get('act_info').budget);

            self.$pay_btn = self.$('[data-role="pay"]');
            self.$unpay_btn = self.$('[data-role="unpay"]');





            /*if(!self.model.get('is_new_fetch'))
            {
                // 安装选择模块
                self._setup_choosen_group();
            }
            else
            {
                self.model.get_list(true);
            }*/



            // 支付选项参数模型
            /*var pay_items_model =
            {
                can_use_balance : true,
                available_balance : utility.user.get('available_balance'),
                total_price : self.budget,
                need_price : 0,
                show_price_info : true,
                is_support_outtime : true,
                is_support_now_out : true
            };


            self.pay_item_obj = new pay_items
            ({
                templateModel : pay_items_model,
                parentNode : self.$pay_items_container
            }).render();

            self.pay_model_obj = new pay_model();

            self.pay_item_obj.hide();

            */

            // 安装按钮模块
            self._setup_number_btn();

            // 安装事件
            self._setup_events();

            self.model.get_list(true);

            self.table_selected = false;



            if(utility.auth.is_login())
            {
                self.$phone_number.val(utility.user.get('cellphone') || '');
            }


        },
        _change_pay_moneys : function(peoples){

            var self = this;

            var total_money = peoples * self.budget;
            var need_money = utility.int(total_money - self.pay_item_obj.model.get('balance'));


            self.pay_item_obj.model.set('need_price',need_money);
            self.pay_item_obj.model.set('total_price',total_money);
        },
        /**
         * 计算支付金额
         * @private
         */
        count_payment : function(peoples)
        {
            var self = this;

            return;

            var total_price = utility.float(peoples * self.model.get('act_info').budget);

            var balance = utility.float(utility.user.get('available_balance'));

            if(self.pay_item_obj.model.get('can_use_balance'))
            {
                var must_pay_money = balance - total_price;

                // 信用金够钱支付的时候
                if(must_pay_money>0)
                {
                    self.must_pay_money = 0;
                }
                else
                {
                    self.must_pay_money = total_price-balance;
                }

            }
            else
            {
                // 没有用信用金支付

                self.must_pay_money = total_price;

            }



            self.must_pay_money = utility.format_float(self.must_pay_money,2);

            self.pay_item_obj.model.set('need_price',self.must_pay_money);
            self.pay_item_obj.model.set('total_price',total_price);
            self.pay_item_obj.model.set('can_use_balance',self.must_pay_money<=0);

            if(self.must_pay_money>0)
            {


                // 余额不够支付,出现第三方支付选择
                if(!self.pay_item_obj)
                {
                    tip.show('第三方支付模块尚未加载!','error');
                    return;
                }

                self.pay_item_obj.show_other_pay_items();

                self.pay_item_obj._select_pay_type('alipay_purse');
            }
            else
            {
                self.pay_item_obj.hide_other_pay_items();
            }

            self.view_scroll_obj.refresh();
        },

        _join_act_before: function(response, options) {
            var self = this;
            tip.show('提交中...', 'loading', {
                delay: -1
            });

        },
        _join_act_success: function(response, options)
        {
            var self = this;


            switch (response.result_data.code)
            {
                // 余额支付成功
                case 2:
                    tip.show(response.result_data.message, 'right',
                        {
                            delay: 800
                        });

                    page_control.navigate_to_page('act/signin/'+self.model.get('event_id')+'/'+(new Date().getTime()));

                    // 马上更新用户信息
                    utility.user.update_user(response.result_data.user_info);

                    break;

                // 生成请求参数成功，待跳转到第三方
                case 3 :

                    tip.show(response.result_data.message, 'right');

                    var data_url = response.result_data.data;

                    var third_code = response.result_data.third_code;

                    var channel_return = response.result_data.channel_return;

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

                            self.after_pay_text(result);

                            console.log(channel_return);

                            if(result == 1 || result == -1 || result == -2)
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

                            console.log(channel_return);

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
                    tip.show("支付失败!"+response.result_data.message, 'error',
                    {
                        delay: 800
                    });
                    break;
            }




        },
        _join_act_error: function(response, options) {
            var self = this;
            tip.show('网络异常！', 'error', {
                delay: 800
            });

        },
        _join_act_complete: function(response, options) {
            var self = this;


        },
        render : function()
        {
            var self = this;

            view.prototype.render.apply(self);

            var view_port_height = self.reset_viewport_height()-45;

            self.$container.height(view_port_height);

            self.trigger('render');

            return self;
        },
        reset_viewport_height : function()
        {
            return utility.get_view_port_height() - 56;
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
                    tip.show('支付成功','right');
                    break;
                case 0:
                    tip.show('其它错误','error');
                    break;
                case -3:
                    tip.show('支付失败','error');
                    break;
                case -4:
                    tip.show('支付取消','error');
                    break;
            }
        }

    })

    module.exports = action_apply_view;
});