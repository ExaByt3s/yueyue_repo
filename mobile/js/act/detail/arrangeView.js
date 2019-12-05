/**
 * Created by nolest on 2014/9/1.
 *
 *
 *
 *  活动预览-arrange view
 */
define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var View = require('../../common/view');
    var tpl = require('./tpl/part.handlebars');
    var utility = require('../../common/utility');
    var Scroll = require('../../common/scroll');
    var templateHelpers = require('../../common/template-helpers');


    var arrange_view = View.extend
    ({

        attrs:
        {
            template: tpl

        },
        events :
        {

        },
        _setup_events : function()
        {
            var self = this;

            self.on('render',function()
            {
                if(!self.view_scroll_obj)
                {
                    self._setup_scroll();

                    self.view_scroll_obj.refresh();

                    return;
                }

                self.view_scroll_obj.refresh();
            });
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
                    lazyLoad : true,
                    is_hide_dropdown : true
                });

            self.view_scroll_obj = view_scroll_obj;
        },
        _parseElement : function()
        {
            var self = this;

            var template_model = self.get('templateModel');


            self.set('templateModel', template_model);

            View.prototype._parseElement.apply(self);
        },
        setup : function()
        {
            var self = this;

            // 安装事件
            self._setup_events();

            self.$container = self.$el;

        },
        render : function()
        {
            var self = this;

            View.prototype.render.apply(self);

            self.trigger('render');

            self.$container.height(self.get('viewport_height'));

            return self;
        },
        show : function()
        {
            var self = this;

            self.$el.removeClass('fn-hide');

            self.view_scroll_obj && self.view_scroll_obj.refresh();
        },
        hide : function()
        {
            var self = this;

            self.$el.addClass('fn-hide');
        }

    });

    module.exports = arrange_view;
});
