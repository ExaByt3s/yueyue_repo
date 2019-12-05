/**
 * Created by nolest on 2014/9/13.
 */

define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../../../frame/page_control');
    var View = require('../../../common/view');
    var bill = require('./tpl/main.handlebars');
    var utility = require('../../../common/utility');
    var Scroll = require('../../../common/new_iscroll');
    var bill_list_tpl = require('./tpl/bill-list.handlebars');
    var m_alert = require('../../../ui/m_alert/view');
    var abnormal = require('../../../widget/abnormal/view');

    var bill_view = View.extend
    ({

        attrs:
        {
            template: bill
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
            'tap [data-tap]' : function(ev)
            {
                var self = this;

                var $currentTarget = $(ev.currentTarget);

                $currentTarget.addClass("cur").siblings().removeClass("cur");

                self.currentType = $currentTarget.attr('data-role');

                self.reset_or_not = 1;

                self.single_page_count = 1;
                //清空元素
                self.$('[data-role="bill-bill"]').html("");

                self.$container.find('.warp-box').css('background-color','');

                self._send_data();
            },
            'tap [data-role="bill-withdrawals"]' : function()
            {
                var self = this;

                //检查是否绑定支付宝
                self.model.check_bind({
                    type : 'check_bind'
                });
                //page_control.navigate_to_page('mine/money/withdrawal/1');
            }
        },
        _send_data : function()
        {
            var self = this;

            var data =
            {
                type : self.currentType,
                page : self.single_page_count
            };

            self.fetching = true;

            m_alert.show("加载中...","loading",{delay:1000});

            self.model.get_bills(data);

        },
        _setup_events : function()
        {
            var self = this;

            self
                .on('render',function()
                {
                    self._setup_scroll();

                    self._send_data();
                });
            self.model
                .on('success:fetch',function(response,options)
                {

                    if(response.result_data.data.length != 0)
                    {
                        self.single_page_count++;

                        self._render_data(self.model.toJSON())
                    }
                    else
                    {
                        //m_alert.show('没有数据',{delay:1000})

                        if(self.$bill_bill.children().length == 0
                            )
                        {
                            self.abnormal_view = new abnormal({
                                templateModel :
                                {
                                    content_height : utility.get_view_port_height('all') - 75
                                },
                                parentNode:self.$bill_bill
                            }).render();

                            self.reset_bg_color();

                            self.view_scroll_obj.refresh();
                        }
                    }

                    self.fetching = false;

                })
                .on('complete:fetch',function(response,options)
                {

                })
                .on('error:fetch',function(response,options)
                {
                    m_alert.show("加载失败","error");

                    self.fetching = false;
                })


            //检查是否绑定支付宝
            self.model
                .on('before:check_bind:fetch',function(response,options)
                {
                    m_alert.show('加载中...','loading',{delay:1000});
                })
                .on('success:check_bind:fetch',function(response,options)
                {
                    var data = response.result_data;
                    var code = data.code ;
                    switch (code)
                    {
                        case 1: 
                            var data = 
                            {
                                alipay_account : data.data.third_account  
                            };
                            page_control.navigate_to_page('mine/money/withdrawal/1',data);
                            break;

                        case 0: 
                            page_control.navigate_to_page('mine/money/bind_alipay');
                            break;

                        case -1: 
                            page_control.navigate_to_page('mine/money/bind_alipay');
                            break;

                        case 2: 
                            page_control.navigate_to_page('mine/money/bind_alipay');
                            break;
                    }  

                })

                .on('error:check_bind:fetch',function()
                {
                    m_alert.show('网络不给力,请返回重试！','error')
                })

                .on('complete:check_bind:fetch',function(response,options)
                {
                    //m_alert.hide();
                })


        },
        _render_data : function(bill_data)
        {
            var self = this;

            //"处理中" 绿字处理
            $.each(bill_data.data,function(i,obj)
            {
                if(bill_data.data[i].status == '处理中')
                {
                    bill_data.data[i] = $.extend(true,{},obj,{status_success : true})
                }
                else
                {
                    bill_data.data[i] = $.extend(true,{},obj,{status_success : false})
                }

            });

            var html_str = bill_list_tpl
            ({
                data : bill_data.data,
                page : self.single_page_count
            });

            self.$bill_bill.append(html_str);

            self.reset_bg_color();

            self.view_scroll_obj.refresh();

        },
        _setup_scroll : function()
        {
            var self = this;

            var view_scroll_obj = Scroll(self.$container);

            self.$container.height(self.reset_viewport_height());

            view_scroll_obj.on('scrollMoveAfter',function()
            {
                var _self = this;

                var scroll_y = _self.y;

                if(_self.maxScrollY - _self.y < 100 && self.model.get("has_next") && !self.fetching)
                {
                    self._send_data();
                }
            });

            self.view_scroll_obj = view_scroll_obj;
        },
        setup : function()
        {
            var self = this;

            self.$container = self.$('[data-role="container"]');

            self.$bill_bill = self.$('[data-role="bill-bill"]');

            self.currentType = 'trade';

            self.reset_or_not = 0;

            self.fetching = false;

            self.single_page_count = 1;

            self._setup_events();
        },
        render : function()
        {
            var self = this;

            View.prototype.render.apply(self);

            self.trigger('render');

            return self;
        },
        reset_viewport_height : function()
        {
            return utility.get_view_port_height('nav') - 41;
        },
        reset_bg_color : function()
        {
            var self = this;

            setTimeout(function()
            {
                self.$container.find('.warp-box').css('background-color','#fff');
            },300);
        }
    });

    module.exports = bill_view;
});