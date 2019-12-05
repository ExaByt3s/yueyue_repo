/**
 * Created by Administrator on 2014/8/21.
 */
define(function(require, exports, module)
{
    var $ = require('$');
    var View = require('../../common/view');
    var utility = require('../../common/utility');
    var page_control = require('../../frame/page_control');
    var grid_tpl = require('../find/tpl/grid_view.handlebars');
    var Scroll = require('../../common/scroll');
    var grid = require('../../widget/model_pic/view');


    var grid_view = View.extend
    ({
        attrs:
        {
            template: grid_tpl
        },
        events :
        {
            'swipeleft': function()
            {

                console.log("123")
            }
        },
        _reset : function()
        {
            var self = this;

            console.log("reset")

            self.collection.length && self.collection.each(self._add_one,self);

        },
        _add_one : function(model)
        {
            var self = this;

            console.log("_add_one")


            self._render_queue.push(model.toJSON());

            return self;
        },
        _render_model_pics_list : function(response,xhr)
        {
            var self = this;

            var render_queue = self._render_queue;

            if(render_queue.length == 0)
            {
                self.empty_list();

                self.view_scroll_obj && self.view_scroll_obj.refresh();

                return
            }

            var grid_list_view = new grid
            ({
                // 模板参数对象
                templateModel :
                {
                    tpl_data : render_queue,
                    tpl_type : 'group'
                }
            }).render();

            self.$grid_list_container.html(grid_list_view.list());

            self.trigger('update_list',response,xhr);

            self._render_queue = [];

            self._drop_reset();

        },
        _setup_events : function()
        {
            var self = this;

            self.collection
                .on('reset',self._reset,self)
                .on('add', self._add_one, self)
                .on('success:fetch',function(response,xhr)
                {
                    console.log(response);

                    self._render_model_pics_list(response,xhr)
                })
                .on('complete:fetch',function(xhr,status)
                {

                });

            self.on('update_list',function(response,xhr)
            {
                // 区分当前对象
                var _self = this;

                if(!self.view_scroll_obj)
                {

                    self._setup_scroll();

                    return;
                }
                else if(xhr.reset)
                {

                    self.view_scroll_obj.reset_top();
                    self.view_scroll_obj.resetLazyLoad();
                }

                self.view_scroll_obj.refresh();

            });

            self.once('render',self._render_after,self);

        },

        _render_after : function()
        {
            var self = this;

            self.collection.get_list(1);

            self._viewportHeight = utility.get_view_port_height('nav');

        },
        _setup_scroll : function()
        {

            var self = this;

            // 下拉
            self._top_offset = 0;

            var view_scroll_obj = Scroll(self.$container,
                {
                    topOffset : self._top_offset,
                    lazyLoad: true
                });

            self.view_scroll_obj = view_scroll_obj;

            self.view_scroll_obj.on('dropload',function(e)
            {
                self.refresh();

            });

            self.view_scroll_obj.scrollTo(0,0);

        },
        setup: function()
        {
            var self = this;

            self.$container = self.$('[data-role="scroll_content"]'); // 滚动容器

            self.$scoll_wrapper = self.$('[data-role="scoll-wrapper"]');

            self.$grid_list_container = self.$('[data-role="grid-insert"]');

            self._render_queue = [];

            self._setup_events();

        },
        render : function()
        {
            var self = this;

            var view_port_height = self.reset_viewport_height() - 50;

            self.$container.height(view_port_height);

            self.$scoll_wrapper.height(view_port_height);

            //self.$grid_list_container.height(view_port_height);

            View.prototype.render.apply(self);

            self.trigger('render');

            return self;
        },
        /**
         * 清空列表，并且方便以后扩展功能
         */
        empty_list : function()
        {
            var self = this;

            self.$grid_list_container.html("");
        },
        reset_viewport_height : function()
        {
            return utility.get_view_port_height('nav');
        },
        refresh : function()
        {
            var self = this;

            self.collection.get_list(1);

        },
        _drop_reset : function()
        {
            var self = this;

            self.view_scroll_obj && self.view_scroll_obj.resetload();
        }
    });

    module.exports = grid_view;

});