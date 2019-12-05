/**
 * Created by nolestLam on 2015/3/5.
 */
define(function(require, exports, module)
{
    var $ = require('$');
    var View = require('../../common/view');
    var utility = require('../../common/utility');
    var page_control = require('../../frame/page_control');
    var coupon_tpl = require('./tpl/main.handlebars');
    var m_alert = require('../../ui/m_alert/view');
    var coupon_per = require('../../widget/coupon/view');
    var Scroll = require('../../common/scroll');
    var abnormal = require('../../widget/abnormal/view');
    var global_config = require('../../common/global_config');


    var coupon_view = View.extend
    ({
        attrs:
        {
            template: coupon_tpl,
            coupon_map : {}
        },
        events :
        {
            'tap [data-role="choosen_btn"]' : function(ev)
            {
                var self = this;

                console.log("2222")
                var $cur_btn = $(ev.currentTarget);

                //self.$('[data-role="tap"]').removeClass('cur');

                $cur_btn.find('[data-role="tap"]').toggleClass('cur');

                $cur_btn.parent().siblings().children('[data-role="choosen_btn"]').children('[data-role="tap"]').removeClass('cur')
            },
            'tap [data-role="to_ticket_details"]' : function(ev)
            {
                //2015-3-20 点击左边票体选中， 右边去详情
                var self = this;

                console.log("1111")

                var $cur_btn = $(ev.currentTarget);

                $cur_btn.siblings().find('[data-role="tap"]').toggleClass('cur');

                $cur_btn.parent().siblings().children('[data-role="choosen_btn"]').children('[data-role="tap"]').removeClass('cur')

            },
            'tap [data-role="page-back"]' : function()
            {
                var self = this;

                if(self.get('is_not_page'))
                {
                    self.trigger('page:back');
                }
                else
                {
                    page_control.back();
                }


            },
            'tap [data-role="confirm"]' : function(ev)
            {
                var self = this;

                var sn = self.$('.tap.cur').parents("[data-coupon_sn]").attr("data-coupon_sn");

                // 获取选中的优惠劵
                var selected_coupon_obj = self.get('coupon_map')[sn];

                // 设置传递过来的支付对象
                var route_params_obj = self.get('route_params_obj');

                if(sn)
                {
                    if(self.get('route_params_obj'))
                    {

                        route_params_obj.pay_item_obj.model.set('coupon_info',selected_coupon_obj);

                        route_params_obj.pay_item_obj.model.set('use_coupon',true);

                    }
                }
                else
                {
                    route_params_obj.pay_item_obj.model.set('use_coupon',false);

                    route_params_obj.pay_item_obj.model.set('coupon_info',{});
                }


                if(self.get('is_not_page'))
                {
                    self.trigger('page:confirm');
                }
                else
                {
                    page_control.back();
                }


            },

            'tap [data-role="to_ticket_details_price"]' : function(ev)
            {
                var self = this;

                var $cur_btn = $(ev.currentTarget);

                var sn = $cur_btn.parents("[data-coupon_sn]").attr("data-coupon_sn");

                page_control.navigate_to_page('coupon/details/'+ sn,{hide_btn:true});
            }
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
                is_hide_dropdown : false,
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

                if(self.fetching || !self.has_next_page)
                {
                    self.view_scroll_obj && self.view_scroll_obj.resetload();
                }
                else
                {
                    console.log("下一页");

                    self.fetching = true; //防止反复提交请求

                    self.model.get_coupon_list(self.submit_data);
                }
            });
        },
        refresh : function()
        {
            var self = this;

            self.$container.html("");

            self.submit_data = $.extend(true,{},self.submit_data,{page:1});

            self.model.get_coupon_list(self.submit_data);
        },
        _setup_events : function()
        {
            var self = this;

            self.on('render',function()
            {
                self.model.get_coupon_list(self.submit_data);

                self._setup_scroll();
            });
            self.model
                .on('before:get_coupon_list:fetch',function(response,options)
                {
                    self.fetching = true;
                    m_alert.show('加载中...','loading',{delay:5000});
                })
                .on('success:get_coupon_list:fetch',function(response,options)
                {


                    self.has_next_page = response.result_data.has_next_page;

                    self.has_next_page && self.submit_data.page++;

                    if(response.result_data.code && response.result_data.code !=1)
                    {
                        m_alert.show(response.result_data.message,'right');
                    }
                    else
                    {
                        m_alert.hide();
                    }

                    if(response.result_data.list && !response.result_data.list.length == 0)
                    {
                        self._render_coupon(response.result_data.list);
                    }
                    else
                    {

                        self.$container.html("");
                        
                        self.abnormal_view = new abnormal({
                            templateModel :
                            {
                                content_height : utility.get_view_port_height('all')
                            },
                            parentNode:self.$container
                        }).render();
                    }
                })
                .on('error:get_coupon_list:fetch',function(response,options)
                {
                    m_alert.show('请求失败，请重试','error');
                })
                .on('complete:get_coupon_list:fetch',function(response,options)
                {
                    self.fetching = false;

                    self._drop_reset();
                })
        },
        _render_coupon : function(data)
        {
            var self = this;

            var ins_data =
            {
                data : data
            };

            // 整合选择数据
            // modify by hudw
            var temp_data = {};
            for(var i=0;i<data.length;i++)
            {
                temp_data[data[i].coupon_sn] = data[i];
            }

            self.set('coupon_map',temp_data);

            var coupon_per_obj = new coupon_per(
                {
                    templateModel : ins_data,
                    parentNode: self.$container
                }).render();
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

            self.submit_data  = $.extend(true,{},self.submit_data,self.model.toJSON());

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
        }
    });

    module.exports = coupon_view;
});