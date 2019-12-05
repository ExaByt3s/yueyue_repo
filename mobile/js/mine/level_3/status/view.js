/**
 * Created by nolestLam on 2015/3/31.
 */
define(function(require, exports, module)
{
    var $ = require('$');
    var View = require('../../../common/view');
    var utility = require('../../../common/utility');
    var page_control = require('../../../frame/page_control');
    var level_3_status_tpl = require('./tpl/main.handlebars');
    var m_alert = require('../../../ui/m_alert/view');
    var coupon_per = require('../../../widget/coupon/view');
    var Scroll = require('../../../common/scroll');
    var abnormal = require('../../../widget/abnormal/view');
    var global_config = require('../../../common/global_config');
    var app = require('../../../common/I_APP');


    var level_3_status_view = View.extend
    ({
        attrs:
        {
            template: level_3_status_tpl,
            coupon_map : {}
        },
        events :
        {
            'tap [data-role="page-back"]' : function()
            {
                var self = this;

                page_control.back();
            },
            'tap [data-role="re_take_photo"]' : function()
            {
                var self = this;

                page_control.back();
            },
            'tap [data-role="submit"]' : function()
            {
                var self = this;

                if(app.isPaiApp)
                {
                    app.switchtopage({page:'hot'});
                }
                else
                {
                    page_control.navigate_to_page('hot');
                }
            }
        },
        _get_img_w_h : function(){
            var self = this;
            var w_h ={};
            w_h.width = utility.get_view_port_width() - 30;
            w_h.height = 580 / 400 * w_h.width;
            return w_h;
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
                is_hide_dropdown : true,
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

            });
        },
        refresh : function()
        {
            var self = this;

            //self.model.get_coupon_list(self.submit_data);
        },
        _setup_events : function()
        {
            var self = this;

            self.on('render',function()
            {
                //self.model.get_coupon_list(self.submit_data);

                self._setup_scroll();
            });
            self.model
                .on('before:get_coupon_list:fetch',function(response,options)
                {

                })
                .on('success:get_coupon_list:fetch',function(response,options)
                {

                })
                .on('error:get_coupon_list:fetch',function(response,options)
                {
                    m_alert.show('请求失败，请重试','error');
                })
                .on('complete:get_coupon_list:fetch',function(response,options)
                {

                    m_alert.hide();

                    self._drop_reset();
                })
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

    module.exports = level_3_status_view;
});