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


    var coupon_list_view = View.extend
    ({
        attrs:
        {
            template: coupon_tpl
        },
        events :
        {
            'tap [data-tap]' : function(ev)
            {
                var self = this;

                var $cur_btn = $(ev.currentTarget);

                var tap = $cur_btn.attr('data-tap');

                self.current_tap = tap;

                console.log(tap);

                $cur_btn.siblings().removeClass('cur');

                $cur_btn.addClass('cur');

                self.refresh();
                //$cur_btn.find('[data-role="tap"]').toggleClass('cur');

            },
            'tap [data-role="page-back"]' : function()
            {
                page_control.back();
            },
            'tap [data-role="to_ticket_details"]' : function(ev)
            {
                var self = this;

                var $cur_btn = $(ev.currentTarget);

                var sn = $cur_btn.parents("[data-coupon_sn]").attr("data-coupon_sn");

                page_control.navigate_to_page('coupon/details/'+ sn);
                console.log(sn);
            },
            'tap [data-role="code"]' : function()
            {
                page_control.navigate_to_page('coupon/code');
            },
            'tap [data-role="to_ticket_details_price"]' : function(ev)
            {
                var self = this;

                var $cur_btn = $(ev.currentTarget);

                var sn = $cur_btn.parents("[data-coupon_sn]").attr("data-coupon_sn");

                page_control.navigate_to_page('coupon/details/'+ sn);
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

            self.submit_data = $.extend(true,{},self.submit_data,{page:1,tab:self.current_tap});

            self.model.get_coupon_list(self.submit_data);
        },
        _setup_events : function()
        {
            var self = this;

            self.on('render',function()
            {
                self._setup_scroll();

                self.$('[data-tap]').removeClass('cur');

                //调整初始化的tap cur
                switch(self.current_tap)
                {
                    case 'available' : self.$('[data-tap="available"]').addClass('cur');break;
                    case 'used' : self.$('[data-tap="used"]').addClass('cur');break;
                    case 'expired' : self.$('[data-tap="expired"]').addClass('cur');break;
                }
            });
            self.model
                .on('before:get_coupon_list:fetch',function(response,options)
                {
                    self.fetching = true;
                    //m_alert.show('加载中...','loading');
                })
                .on('success:get_coupon_list:fetch',function(response,options)
                {


                    self.has_next_page = response.result_data.has_next_page;

                    self.has_next_page && self.submit_data.page++;

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
                                content_height : utility.get_view_port_height('all') - 44
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

                    m_alert.hide();

                    self._drop_reset();
                })

        },
        _render_coupon : function(data)
        {
            var self = this;

            var ins_data =
            {
                data : data,
                no_choose_btn : true //隐藏勾选按钮
            };

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

            self._setup_events();

            self.fetching = false;

            self.current_tap = self.get('type');

            self.submit_data =
            {
                page : 1,
                tab: self.current_tap
            };

            self.model.get_coupon_list(self.submit_data);


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
        }
    });

    module.exports = coupon_list_view;
});