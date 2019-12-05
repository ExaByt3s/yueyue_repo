/**
 * 充值
 * 
 */
define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../../../frame/page_control');
    var View = require('../../../common/view');
    var App = require('../../../common/I_APP');
    var recharge = require('../recharge/tpl/main.handlebars');
    var utility = require('../../../common/utility');
    var templateHelpers = require('../../../common/template-helpers');
    var Scroll = require('../../../common/scroll');
    var choosen_group_view = require('../../../widget/choosen_group/view');
    var m_alert = require('../../../ui/m_alert/view');
    var pay_items= require('../../../widget/pay_item/view');

    var send_money ;

    var recharge_view = View.extend
    ({

        attrs:
        {
            template: recharge
        },
        forms :
        {
            // 表单类
            number : {},
            select : {},
            choosen_btn : {}
        },
        events :
        {
            'swiperight' : function (){
                page_control.back();
            },
            'tap [data-role=page-back]' : function (ev)
            {
                page_control.back();
            },

            'tap [data-role=ui-choosen-btn]' : function (ev)
            {
               var self = this;

               self.$money_other.val('');

               self.val_money_obj_val = self.choosen_group_view_obj.get_value()[0].params;

               self.send_money = self.val_money_obj_val ;
            },
            
            'tap [data-role=btn-money-edit]' : function (ev)
            {
                var self = this;

                self.choosen_group_view_obj._clear_btn_selected();
            },
            'tap [data-role=btn-submit]' : function (ev)
            {
                var self = this;

                self.send_money = !self.choosen_group_view_obj.get_value()[0]?$.trim(self.$money_other.val()):self.send_money;

                // 支付方式
                var third_code = self.pay_item_obj.model.get('pay_type');

                if(self.send_money<=0)
                {
                    m_alert.show('请输入正确金额！','error',{delay:1000});

                    return ;
                }

                if(self.send_money < 10)
                {
                    m_alert.show('金额不能少于10元！','error',{delay:1000});

                    return ;
                }

                if(!third_code)
                {
                    m_alert.show('请选择支付方式！','error',{delay:1000});

                    return ;
                }


                var data =
                {
                    type:'recharge',
                    amount : self.send_money,
                    third_code : third_code,
                    redirect : self.redirect
                };

                utility.user.send_recharge(data);
            },
            'tap [data-role=go-bill]' : function (ev)
            {
                var self = this;
                page_control.navigate_to_page('mine/money/bill'); 
                
            }
           
        },
        /**
         * 安装事件
         * @private
         */
        _setup_events : function()
        {

            var self = this;

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

                    console.log(code);

                    if(self.pay_item_obj.model.get('pay_type') == 'alipay_purse')
                    {

                        if( code == 1)
                        {
                            m_alert.show('第三方支付跳转','right',{delay:1000});

                            // 调用支付宝支付
                            App.alipay
                            ({
                                alipayparams : data
                            });
                        }
                        else
                        {
                            m_alert.show(msg,'error',{delay:1000});

                        }

                    }
                    else
                    {
                        m_alert.show('尚未支持该支付方式','error',{delay:1000});
                    }


                })
                .on('error:send_rechare:fetch',function()
                {
                    m_alert.hide();
                })
                .on('complete:send_rechare:fetch',function()
                {

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
            self.$container.height(self.reset_viewport_height());

            var view_scroll_obj = Scroll(self.$container,
            {
                is_hide_dropdown : true
            });

            

            self.view_scroll_obj = view_scroll_obj;
        },

        reset_viewport_height : function()
        {
            return utility.get_view_port_height('nav') - 60;
        },


        _render_after :function()
        {
            var self = this ;
        },
        /**
         * 视图初始化入口
         */
        setup : function()
        {

            var self = this;

            // 配置交互对象
            self.$container = self.$('[data-role=container]'); // 滚动容器
            self.$btn_money = self.$('[data-role=btn-money]'); // 定死金额   
            self.$btn_money_edit = self.$('[data-role=btn-money-edit]'); // 自己输入   

             // 风格选择选项
            self.$choosen_style_group = self.$('[data-role="choosen_style_group"]');

            self.$ui_choosen_btn = self.$('[data-role=ui-choosen-btn]'); // 自己输入

            self.$btn_submit = self.$('[data-role=btn-submit]'); // 提交

            //self.$money_other = self.$('[data-role="money-other"]');

            self.$pay_item_container = self.$('[data-role="pay-item-container"]');

            self.$money_other = self.$('[data-role=money-other]'); // 提交                

            // 安装事件
            self._setup_events();
            // 安装选择模块
            self._setup_choosen_group();

            // 支付选项参数模型
            var pay_items_model =
            {
                show_price_info : false
            };

            self.pay_item_obj = new pay_items
            ({
                templateModel : pay_items_model,
                parentNode : self.$pay_item_container
            }).render();

        },

        _get_money : function(ev)
        {
            var self = this;
            if( ev.hasClass('.current') ){
                ev.removeClass('.current')
            }{
                ev.addClass('.current');
            }
            var val_money = ev.attr('data-money');
            return  val_money;
        },

        _setup_choosen_group : function()
        {
            var self = this;
            var style_list = [{text:'￥ 100',params:'100'},{text:'￥ 300',params:'300'},{text:'￥ 500',params:'500'}];           
            self.choosen_group_view_obj = new choosen_group_view
            ({
                templateModel :
                {                    
                    list : style_list

                },
                btn_per_line : 3, //每行按钮个数
                line_margin : '0px 0px 0px 0px', //每行margin
                btn_width : '80px', //按钮宽度
                parentNode: self.$choosen_style_group,
                is_multiply : false
            }).render();
        },
        
        render : function()
        {
            var self = this;
            self.$container.height(self.reset_viewport_height());
            // 调用渲染函数
            View.prototype.render.apply(self);
            self.trigger('render');
            return self;
        },
        reset_scroll_height : function()
        {
            var self = this;

            var view_port_height = self.reset_viewport_height();

            self.$container.height(view_port_height);

            self.view_scroll_obj.refresh();
        }

    });

    module.exports = recharge_view;
});