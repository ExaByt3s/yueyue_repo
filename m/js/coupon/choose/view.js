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


    var coupon_view = View.extend
    ({
        attrs:
        {
            template: coupon_tpl
        },
        events :
        {

        },
        _render_items : function(data)
        {
            var self = this;

            return;

            var html_str = items_tpl
            ({
                data : data
            });

            self.item_container.html(html_str);

            self._setup_iscroll_list(data.tag.list);

            self.view_scroll_obj.refresh();
        },
        _setup_scroll : function()
        {
            var self = this;

            var view_scroll_obj = scroll(self.$container,{
                lazyLoad : true,
                _startY : 45,
                prevent_tag : 'slider',
                is_hide_dropdown : true
            });

            self.view_scroll_obj = view_scroll_obj;
        },
        _setup_events : function()
        {
            var self = this;

            self.on('render',function()
            {
                self._setup_scroll()
            });



        },
        setup : function()
        {
            var self = this;

            self.$container = self.$('[data-role="content-body"]');

            self.$container_style = self.$('[data-role="container_style"]');

            self.$container_group = self.$('[data-grid]');

            self.location = utility.storage.get('location');

            self.item_container = self.$('[data-role="items"]');

            self._setup_events();

        },
        render : function()
        {
            var self = this;

            View.prototype.render.apply(self);

            self.trigger('render');

            return self;
        }
    });

    module.exports = coupon_view;
});