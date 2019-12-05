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
    var Scroll = require('../../common/scroll');
    var items_tpl = require('./tpl/items.handlebars');
    var App = require('../../common/I_APP');


    var coupon_details_view = View.extend
    ({
        attrs:
        {
            template: coupon_tpl
        },
        events :
        {
            'tap [data-role="page-back"]' : function()
            {
                page_control.back();
            },
            'tap [data-role="free_get"]' : function()
            {
                var self = this;

                self.model.give_supply_coupon(self.get("sn"));
            },
            'tap [data-role="use"]' : function()
            {
                var self = this;

                if(App.isPaiApp)
                {
                    if(self.used_type == 'yuepai')
                    {
                        App.switchtopage({page:'hot'});
                    }
                    else
                    {
                        App.switchtopage({page:'act'});
                    }
                }
                else
                {
                    if(self.used_type == 'yuepai')
                    {
                        page_control.navigate_to_page('hot');
                    }
                    else
                    {
                        page_control.navigate_to_page('act/list');
                    }
                }
            }
        },
        _render_items : function(data)
        {
            var self = this;

            if(self.get('get_coupon'))
            {
                self.$('[data-role="coupon-title"]').html('领取优惠券');
                //领券显示
                if(data.give_status == 0)
                {
                    self.$('[data-role="btn_footer"]').removeClass('fn-hide');

                    self.$('[data-role="free_get"]').removeClass('fn-hide');

                    data = $.extend(true,{},data,{_is_available:true});

                }
                else if(data.give_status == 1)
                {
                    self.$('[data-role="btn_footer"]').removeClass('fn-hide');

                    self.$('[data-role="has"]').removeClass('fn-hide');
                }
                else if(data.give_status == -1)
                {
                    m_alert.show('您没有领取资格','error');
                }


                if(self.get("hide_btn"))
                {
                    self._hide_btn()
                }

                data = $.extend(true,{},data,{_notice_text:"有效期：" + data.start_time_str + "-" + data.end_time_str});

            }
            else
            {
                //详情页显示
                if(data.tab == 'available')
                {
                    data = $.extend(true,{},data,{_is_available:true,_notice_text:"有效期：" + data.start_time_str + "-" + data.end_time_str});

                    self.$('[data-role="btn_footer"]').removeClass('fn-hide');

                    self.$('[data-role="use"]').removeClass('fn-hide');
                }
                else if(data.tab == 'used')
                {
                    data = $.extend(true,{},data,{_is_used:true,_notice_text:data.used_time_str + " 已使用"});
                }
                else if(data.tab == 'expired')
                {
                    data = $.extend(true,{},data,{_is_used:true,_notice_text:data.end_time_str + " 已过期"});
                }

                if(self.get("hide_btn"))
                {
                    self._hide_btn()

                }
            }

            var html_str = items_tpl
            ({
                data : data
            });
            //点击“立即使用”的跳转类型
            self.used_type = data.scope_module_type;

            self.$container.html(html_str);

            self.$use_txt = self.$('[data-role="use-text"]');

            self.$use_txt.html(data.scope_module_txt);

            self.view_scroll_obj.refresh();
        },
        _setup_scroll : function()
        {
            var self = this;

            var view_scroll_obj = Scroll(self.$scroll_container,{
                lazyLoad : true,
                _startY : 45,
                prevent_tag : 'slider',
                is_hide_dropdown : false
            });

            self.view_scroll_obj = view_scroll_obj;

            // 上拉刷新
            self.view_scroll_obj.on('dropload',function(e)
            {
                self.refresh();

                console.log("刷新");

            });
        },
        refresh : function()
        {
            var self = this;

            if(self.get('get_coupon'))
            {
                //此时sn是supply_id 用于查询兑换状态
                self.model.get_supply_detail(self.get("sn"))
            }
            else
            {
                self.model.get_single_coupon(self.get("sn"))
            }

            self._hide_btn();

            self.view_scroll_obj && self.view_scroll_obj.resetload();
        },
        _hide_btn : function()
        {
            var self = this;

            self.$('[data-role="btn_footer"]').addClass('fn-hide');

            self.$('[data-role="free_get"]').addClass('fn-hide');

            self.$('[data-role="has"]').addClass('fn-hide');

            self.$('[data-role="use"]').addClass('fn-hide');
        },
        _setup_events : function()
        {
            var self = this;

            self.on('render',function()
            {
                self._setup_scroll();
            });
            self.model
                .on('before:get_single_coupon:fetch',function(response,options)
                {
                    m_alert.show('加载中...','loading');
                })
                .on('success:get_single_coupon:fetch',function(response,options)
                {
                    self.$container.html("");
                    
                    self._render_items(response.result_data);

                })
                .on('error:get_single_coupon:fetch',function(response,options)
                {
                    m_alert.show('请求失败，请重试','error');
                })
                .on('complete:get_single_coupon:fetch',function(response,options)
                {
                    m_alert.hide();

                })

                //查询兑换状态
                .on('before:get_supply_detail:fetch',function(response,options)
                {
                    m_alert.show('加载中...','loading');
                })
                .on('success:get_supply_detail:fetch',function(response,options)
                {
                    self._render_items(response.result_data);
                })
                .on('error:get_supply_detail:fetch',function(response,options)
                {
                    m_alert.show('请求失败，请重试','error');
                })
                .on('complete:get_supply_detail:fetch',function(response,options)
                {
                    //m_alert.hide();

                })

                //免费领取 按钮
                .on('before:give_supply_coupon:fetch',function(response,options)
                {
                    m_alert.show('领取中...','loading');
                })
                .on('success:give_supply_coupon:fetch',function(response,options)
                {
                    m_alert.show(response.result_data.message,'right');

                    /*
                     response.result_data.result
                     -1 参数错误
                     -2 参数错误
                     -3 未开始
                     -4 已结束
                    */

                    self.refresh();
                })
                .on('error:give_supply_coupon:fetch',function(response,options)
                {
                    m_alert.show('请求失败，请重试','error');
                })
                .on('complete:give_supply_coupon:fetch',function(response,options)
                {
                    //m_alert.hide();
                })


        },
        setup : function()
        {
            var self = this;

            self.$scroll_container = self.$('[data-role="content-body"]');

            self.$container = self.$('[data-role="list-container"]');



            self._setup_events();

            if(self.get('get_coupon'))
            {
                //此时sn是supply_id 用于查询兑换状态
                self.model.get_supply_detail(self.get("sn"))
            }
            else
            {
                self.model.get_single_coupon(self.get("sn"))
            }
        },
        render : function()
        {
            var self = this;

            View.prototype.render.apply(self);

            self.trigger('render');

            return self;
        }
    });

    module.exports = coupon_details_view;
});