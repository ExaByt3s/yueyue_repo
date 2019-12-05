/**
 * 提现
 * 汤圆 2014.9.12
 */
define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../../../frame/page_control');
    var View = require('../../../common/view');
    var withdrawal = require('../withdrawal/tpl/main.handlebars');
    var utility = require('../../../common/utility');
    var templateHelpers = require('../../../common/template-helpers');
    var Scroll = require('../../../common/scroll');
    var global_config = require('../../../common/global_config');
    var number_btn_view = require('../../../widget/number_btn/view');
    var m_alert = require('../../../ui/m_alert/view');

    var withdrawal_view = View.extend
    ({

        attrs:
        {
            template: withdrawal
        },
        events :
        {
            'swiperight' : function ()
            {
                page_control.back();
            },
            'tap [data-role=page-back]' : function (ev)
            {
                page_control.back();
            },
            'tap [data-role="btn-get"]' : function (ev)
            {
                //点击获取验证码
                var self = this;


                if(!self.counting)
                {
                    
                    self.model.send_sms();
                }
            },
            'tap [data-role="btn-tx"]' : function (ev)
            {
                var self = this ;

                if (!self._get_value_data())
                {
                    return ;
                }

                self.model.submit_details(self._get_value_data());
            },
            'tap [data-role="to_bill"]' :function()
            {
                var self = this;

                page_control.navigate_to_page("mine/money/bill");
            }
        },
        /**
         * 获取提交值
         * @returns {{type: string, amount: *, third_account: *, sms_code: Number}}
         * @private
         */
        _get_value_data : function()
        {
            var self = this;

            var get_sms_code = $.trim(self.$btn_sms_code.val()); //验证码

            var get_alipay = self.$btn_alipay.val(); //支付宝

            //var get_number_menber = self.$number_menber.find('.input-text').val(); //提现金额

            var get_number_menber = self.$number_menber.val();

            var email_reg = new RegExp(/^[\w-]+(\.[\w-]+)*@[\w-]+(\.[\w-]+)+$/);

            var phone_reg = new RegExp(/^[0-9]{11}$/);

            var pw_cn = new RegExp(/^[\u4E00-\u9FA5]+$/);

            if(get_number_menber == "" || pw_cn.test(get_number_menber.trim()))
            {
                alert('请正确输入提现金额！');
                return ;
            }


            if( get_alipay == '' || !(email_reg.test(get_alipay) || phone_reg.test(get_alipay)))
            {
                alert('请正确输入支付宝账号！');
                return ;
            }

            if( get_sms_code == '')
            {
                alert('请输入手机验证码！');
                return ;
            }

            //手机验证码必须为数字 正则
            var reg = /^\d+$/ ;
            var is_num = get_sms_code.match(reg);
            
            if( !is_num )
            {
                alert('验证码输入错误，验证码只能为数字!') ;
                return ;
            }

            var data =
            {
                type : 'money',
                amount : get_number_menber,
                //third_account : get_alipay,
                third_account : self.get('alipay_account'),
                sms_code : get_sms_code,
                is_money : self.model.get('is_money')
            };

            return data;
        },
        _count_down : function(sec)
        {
            var self = this;

            self.counting = true;

            self.count_down.html(sec);

            self.count_Interval = setInterval(function()
            {
                sec--;

                if(sec == 0)
                {
                    self.count_down.html('重新获取');

                    self._stop_count_down();

                    self.counting = false;
                }
                else
                {
                    self.count_down.html(sec);
                }
            },1000)
        },
        _stop_count_down : function()
        {
            var self = this;

            clearInterval(self.count_Interval);
        },
        _setup_events : function()
        {
            var self = this;
            self
                .on('render',function()
                {
                    self._setup_scroll();
                });



            //检查是否绑定支付宝
            // self.model
            //     .on('before:check_bind:fetch',function(response,options)
            //     {
            //         m_alert.show('检查支付宝是否绑定','loading',{delay:1000});
            //     })
            //     .on('success:check_bind:fetch',function(response,options)
            //     {
            //         var data = response.result_data;
            //         self.render_html(data);
            //     })
            //     .on('error:check_bind:fetch',function()
            //     {
            //         m_alert.show('网络不给力,请返回重试！','error')
            //     })
            //     .on('complete:check_bind:fetch',function(response,options)
            //     {
            //         //m_alert.hide();
            //     })


            self.model
                .on('success:fetch_sms',function(response,options)
                {
    
                    var data = response.result_data;

                    if(data.code == 1)
                    {
                        self.phone_change();
                        self._count_down(60);

                    }
                    else
                    {
                        m_alert.show(data.msg,'error');
                    }


                })
                .on('error:fetch_sms',function(response,options)
                {
                    m_alert.show('网络不好哦！');

                    self._stop_count_down();
                })
                .on('complete:fetch_sms',function(response,options)
                {
                    //m_alert.hide();
                })
                .on('before:fetch_submit',function(response,options)
                {
                    m_alert.show('发送中...','loading');

                    //page_control.navigate_to_page('mine');

                })
                .on('success:fetch_submit',function(response,options)
                {
                    if(response.result_data.code>0)
                    {
                        m_alert.show(response.result_data.msg,'right');

                        page_control.navigate_to_page('mine');
                    }
                    else
                    {
                        m_alert.show(response.result_data.msg,'error');
                    }   

                })
                .on('error:fetch_submit',function()
                {
                    m_alert.show('网络不给力！','error')
                })
                .on('complete:fetch_submit',function(response,options)
                {
                    //m_alert.hide();
                })

        },

        phone_change : function ()
        {
            var self = this;
            self.$('[data-role=change-phone]').removeClass('fn-hide'); 
            var phone_num = utility.user.get('phone').toString();

            var phone_first = phone_num.substring(0, 3);
            //var phone_center_start = phone_num.substring(3, 7);
            var phone_center_start = '****';
            var phone_end =  phone_num.substring(7, 11);

            var new_phone_num = phone_first+phone_center_start+phone_end;

            self.$('[data-role=change-phone-str]').html(new_phone_num);
        },


        _setup_scroll : function()
        {
            var self = this;

            var view_scroll_obj = Scroll(self.$container,
                {
                    is_hide_dropdown : true
                });

            self.view_scroll_obj = view_scroll_obj;
        },
        _setup_number_btn : function()
        {
            var self = this;

            self['member'] = new number_btn_view
            ({
                templateModel :
                {
                    type : 'tel'
                },
                min_value : 0,
                step : 50,
                parentNode: self.$number_menber,
                value : 1
            }).render();
        },
        setup : function()
        {
            var self = this;
            //取本地数据
            self.balance = utility.user.get("balance"); //余额

            self.available_balance = utility.user.get("available_balance"); //提现

            self.bail_available_balance = utility.user.get('bail_available_balance'); //信用金

            self.$container = self.$('[data-role="container"]'); // 滚动容器

            self.$btn_sms_code = self.$('[data-role=btn-sms-code]'); // 验证码

            self.$btn_alipay = self.$('[data-role=btn-alipay]'); // 支付宝账号

            self.$number_menber = self.$('[data-role="btn_view"]');
            //self.$number_menber = self.$('[data-role=btn_view]'); // 金额模块

            self.count_down = self.$('[data-role="btn-get"]');

            self.$alipay_account = self.$('[data-role="alipay-account"]');

            self.$footer_top_css = self.$('[data-role="footer-top-css"]');

            


            self.counting = false;

            if(self.model.get('is_money'))
            {
               // 钱包余额
               self.$('[data-role="fee-all"]').html(self.available_balance);

               //debugger;
            }
            else
            {
                // 信用金余额
                self.$('[data-role="fee-all"]').html(self.bail_available_balance);

                //debugger;
            }

            // 安装事件
            self._setup_events();

            //打印支付宝账号
            self.$btn_alipay.val(self.get('alipay_account'));
            self.$btn_alipay.attr('disabled', 'true');

            //底部偏移头部距离，让按钮始终贴底部
            self.set_footer_margin_top();

            //检查是否绑定支付宝
            // self.model.check_bind({
            //     type : 'check_bind'
            // });

        },

        //打印支付宝账号
        render_html : function (data)
        {   
            var self = this;
            console.log(data);
            var code = data.code ;
            switch (code)
            {
                case 1: 
                    self.bind_alipay_account = data.data.third_account;
                    //self.$alipay_account.html(self.bind_alipay_account);
                    self.$btn_alipay.val(self.bind_alipay_account);
                    self.$btn_alipay.attr('disabled', 'false');
                    break;

                case 0: 
                    self.$btn_alipay.val(data.msg);
                    self.$btn_alipay.attr('disabled', 'false');
                    setTimeout( function(){
                        page_control.back();
                    }, 3000 );
                    break;

                case -1: 
                    self.$btn_alipay.val(data.msg);
                    self.$btn_alipay.attr('disabled', 'false');
                    setTimeout( function(){
                        page_control.navigate_to_page("mine/money/bind_alipay");
                    }, 3000 );
                    break;

                case 2: 
                    self.$btn_alipay.val(data.msg);
                    self.$btn_alipay.attr('disabled', 'false');
                    setTimeout( function(){
                        page_control.back();
                    }, 3000 );
                    break;

            }  

        },

        render : function()
        {
            var self = this;

            //var view_port_height = self.reset_viewport_height();

            //self.$container.height(view_port_height);

            View.prototype.render.apply(self);

            self.trigger('render');

            return self;
        },
        reset_viewport_height : function()
        {
            return utility.get_view_port_height('nav') - 50;
        },
        reset_scroll_height : function()
        {
            var self = this;

            var view_port_height = self.reset_viewport_height();

            self.$container.height(view_port_height);

            self.view_scroll_obj.refresh();
        },

        //底部偏移头部距离，让按钮始终贴底部
        set_footer_margin_top : function()
        {
            var self = this;
            var main_height = utility.get_view_port_height('nav') - 320 - 51 ;
            self.$footer_top_css.css({
                marginTop: main_height+'px'
            });
            //self.$footer_top_css.height(main_height);
        }



    });

    module.exports = withdrawal_view;
});