/**
 * 首页 - 城市选择
 * 汤圆 2014.18.
 */
define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var View = require('../../common/view');
    var agreement = require('../agreement/tpl/main.handlebars');
    var utility = require('../../common/utility');
    var templateHelpers = require('../../common/template-helpers');
    var Scroll = require('../../common/scroll');
    var m_alert = require('../../ui/m_alert/view');


    var agreement_view = View.extend
    ({

        attrs:
        {
            template: agreement
        },
        events :
        {
            'swiperight' : function (){
                //page_control.back();
            },
            'tap [data-role=page-back]' : function (ev)
            {
                var self = this;

                self.hide();
                //page_control.back();
            },
            'tap [data-role=btn-go-to]' : function (ev)
            {
                var self = this;

                self.trigger('receive');

                self.hide();
                
            }

        },
       
        /**
         * 安装事件
         * @private
         */
        _setup_events : function()
        {

            var self = this; 
            self._setup_scroll()
            //.once('render',self._render_after,self);

            self.view_scroll_obj.refresh();
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
                is_hide_dropdown : true
            });
            self.view_scroll_obj = view_scroll_obj;
        },
        /**
         * 安装底部导航
         * @private
         */
       
        _reset : function()
        {
            var self = this;
            
        },
        _add_one : function(model)
        {
            var self = this;
            
        },
        

        /**
         * 视图初始化入口
         */
        setup : function()
        {

            var self = this;

            // 配置交互对象
            self.$container = self.$('[data-role=container]'); // 滚动容器

            // 安装事件
            self._setup_events();

        },
        render : function()
        {
            var self = this;

            // 调用渲染函数
            View.prototype.render.apply(self);

            self.trigger('render');

            return self;
        },
        show : function()
        {
            var self = this;

            self.$el.removeClass('fn-hide');

            self.refresh();
        },
        hide : function()
        {
            var self = this;

            self.$el.addClass('fn-hide');

            self.refresh();
        },
        refresh : function()
        {
            var self = this;

            self.view_scroll_obj && self.view_scroll_obj.refresh();
        }

    });

    module.exports = agreement_view;
});