/**
 * 基础页面框架
 * 汤圆
 * @param  {[type]} require
 * @param  {[type]} exports
 * @param  {[type]} module
 * @return {[type]}
 */
define(function(require, exports, module)
{
    //基础框架
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var View = require('../../common/view');
    var utility = require('../../common/utility');
    var templateHelpers = require('../../common/template-helpers');
    // var Scroll = require('../../common/new_iscroll');
    var Scroll = require('../../common/scroll');
    var footer = require('../../widget/footer/index');
    var m_alert = require('../../ui/m_alert/view');
    var I_App = require('../../common/I_APP');
    var WeixinApi = require('../../common/I_WX');

    //当前页引用
    var current_main_template = require('./tpl/main.handlebars');

    var current_view = View.extend(
    {
        attrs:
        {
            template: current_main_template
        },
        events:
        {
            'swiperight': function()
            {
                page_control.back();
            },
            'tap [data-role=page-back]': function(ev)
            {
                var self = this;
                page_control.back();
            },
            'tap [data-role=btn_close]': function(ev)
            {
                var self = this;
                if ( WeixinApi.isWexXin() ) 
                {
                    WeixinApi.closeWindow();
                }
                else
                {
                    console.log("no weixin");
                };
            }
        },
        /**
         * 安装事件
         * @private
         */
        setup_events: function()
        {
            var self = this;
            // console.log( self.model.toJSON() );
            // console.log( self.model.get("param_a") );

            self.model
                .on('before:fetch', function(response, xhr)
                {
                    m_alert.show('加载中...', 'loading');
                })
                .on('success:fetch', function(response, xhr)
                {
                    self.render_html(response, xhr);
                })
                .on('error:fetch', function(response, xhr)
                {
                    m_alert.show('网络异常', 'error',
                    {
                        delay: 1000
                    });
                })
                .on('complete:fetch', function(xhr, status) {

                });

            //当前view 操作
            self.on('update_list', function(response, xhr)
                {
                    // 区分当前对象
                    if (!self.view_scroll_obj)
                    {
                        self._setup_scroll();
                    }
                    self.view_scroll_obj.refresh();

                    // 重置渲染队列
                    self._render_queue = [];

                })
                // .once('render', self.render_after, self);
        },

        /**
         * 视图初始化入口
         */
        setup: function()
        {
            var self = this;

            // 渲染队列
            self._render_queue = [];

            // 配置交互对象
            self.$container = self.$('[data-role=container]'); // 滚动容器

            // 安装事件
            self.setup_events();

            // 安装滚动条
            self._setup_scroll();

            // 安装底部导航
            //self._setup_footer();
        },

        /**
         * 渲染模板
         * @param response
         * @param options
         * @private
         */
        render_html: function(response, xhr)
        {
            var self = this;
            var render_queue = self._render_queue;

            self.trigger('update_list', response, xhr);
        },
        
        render_after: function()
        {
            var self = this;
            // self.model.get_list();
        },

        /**
         * 安装滚动条
         * @private
         */
        _setup_scroll: function()
        {
            var self = this;
            var view_scroll_obj = Scroll(self.$container,
            {
                is_hide_dropdown: true
            });
            self.view_scroll_obj = view_scroll_obj;
        },
        /**
         * 安装底部导航
         * @private
         */
        _setup_footer: function()
        {
            var self = this;
            var footer_obj = new footer(
            {
                // 元素插入位置
                parentNode: self.$el,
                // 模板参数对象
                templateModel:
                {
                    // 高亮设置参数
                    is_model_pai: true
                }
            }).render();
        },
        _reset: function()
        {
            var self = this;
            // self.collection.length && self.collection.each(self._add_one, self);
        },
        _add_one: function(model)
        {
            var self = this;
            // self._render_queue.push(model.toJSON());
            return self;
        },
        render: function()
        {
            var self = this;
            // 调用渲染函数
            View.prototype.render.apply(self);
            self.trigger('render');
            self.trigger('update_list');
            return self;
        }
    });
    module.exports = current_view;
});