/**
 * 首页 - 留言列表
 * 汤圆
 */
define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var View = require('../../common/view');
    var list = require('../list/tpl/main.handlebars');
    var utility = require('../../common/utility');
    var templateHelpers = require('../../common/template-helpers');
    var Scroll = require('../../common/new_iscroll');
    var itemTpl = require('./tpl/item.handlebars');
    var load_more = require('../../widget/load_more/view');
    var m_alert = require('../../ui/m_alert/view');
    var abnormal = require('../../widget/abnormal/view');
    var App = require('../../common/I_APP');
    var list_view = View.extend(
    {
        attrs:
        {
            template: list
        },
        events:
        {
            'swiperight': function()
            {
                var self = this;
                if (self.num == 0)
                {
                    page_control.back();
                }
            },
            'tap [data-role=page-back]' : function (ev)
            {
                // page_control.navigate_to_page("mine");
                var self = this;
                self.go_back(self.num);
            }
            ,
            'tap [data-role=load-more-container-v2]': function(ev)
            {
                var self = this;
                self.get_more();
            }
        },
        /**
         * 安装事件
         * @private
         */
        _setup_events: function()
        {
            var self = this;

            self.collection
                .on('reset', self._reset, self)
                .on('add', self._add_one, self)

                .on('before:fetch', function(response, xhr)
                {
                    self.$load_more_container_v2.find('i').removeClass('fn-hide');
                    self.$load_txt.html('加载中...');
                    self.$load_more_container_v2.find('.icon-spinner').addClass('ui-loading-animate');
                })
                .on('success:fetch', function(response, xhr)
                {
                    self.$load_more_container_v2.find('.icon-spinner').removeClass('ui-loading-animate');
                    self._render_comment(response, xhr);

                    
                    self.$load_txt.html('点击加载更多...')
                    self.$load_more_container_v2.find('i').addClass('fn-hide');
                })
                .on('complete:fetch', function(xhr, status) {});

            self.on('update_list', function(response, xhr)
                {
                    // 区分当前对象
                    var _self = this;
                    self._setup_scroll();
                    self.view_scroll_obj.refresh();
                    // 重置渲染队列
                    self._render_queue = [];
                })
                .once('render', self._render_after, self);

            self._setup_scroll();
            self.view_scroll_obj.refresh();

            // self.num = parseInt(self.attrs.is_can_back);

            
        },
        /**
         * 根据来路判断返回
         * @private
         */
        go_back: function(num)
        {
            var self = this;
            switch (num)
            {
                case 0:
                    page_control.back();
                    break;

                case 1:
                    console.log(self.from_app)
                    if(self.from_app)
                    {
                        App.app_back();
                    }
                    else
                    {
                        page_control.navigate_to_page("mine/payment_no_"+(new Date().getTime()));
                    }
                    break;
            }
        },
        /**
         * 安装滚动条
         * @private
         */
        _setup_scroll: function()
        {
            var self = this;
            var view_scroll_obj = Scroll(self.$container,
            {});
            self.view_scroll_obj = view_scroll_obj;
        },
        _reset: function()
        {
            var self = this;
            self.collection.length && self.collection.each(self._add_one, self);
        },
        _add_one: function(model)
        {
            var self = this;
            self._render_queue.push(model.toJSON());
            return self;
        },
        /**
         * 安装加载更多按钮
         * @private
         */
        // _setup_load_more: function()
        // {
        //     var self = this;
        //     self.load_more_btn = new load_more(
        //     {
        //         parentNode: self.$load_more_container
        //     }).render();
        // },


        // _scroll_check: function()
        // {
        //     var self = this;
        //     if (!self.$load_more_container || !self._visible || !self.collection.has_next_page)
        //     {
        //         return;
        //     }
        //     var pos = self.$load_more_container.position();
        //     if (pos.top < self._viewportHeight)
        //     {
        //         self.get_more();
        //     }
        // },
        _render_comment: function(response, xhr)
        {
            var self = this;
            var render_queue = self._render_queue;
            // 没有数据的时候，直接return
            if (render_queue.length == 0)
            {
                self.abnormal_view = new abnormal(
                {
                    templateModel:
                    {
                        content_height: utility.get_view_port_height('all') - 45
                    },
                    parentNode: self.$item_wrap
                }).render();
                //m_alert.show('暂时没有数据！','error',{delay:2000});
                //self.$container.addClass('fn-hide');
                self.$load_more_container_v2.hide();
                return;
            }
            // 判断是否有下一页
            if (self.collection.has_next_page)
            {
                self.$load_more_container_v2.show();
            }
            else
            {
                self.$load_more_container_v2.hide();
            }
            var htmlStr = itemTpl(
            {
                datas: render_queue
            });
            // 判断调用重新加载还是插入方法
            var method = xhr.reset ? 'html' : 'append';
            self.$item_wrap[method](htmlStr);
            //self.$item_wrap[method](htmlStr);
            //self.trigger('update_list',response,xhr);
            //self.$item_wrap.html(htmlStr);
            //self.trigger('updateList', response, xhrOptions);
            self._render_queue = [];
            self.view_scroll_obj.refresh();
        },
        _render_after: function()
        {
            var self = this;
            self.collection.get_list(1);
            //self._viewportHeight = utility.get_view_port_height('nav');
        },
        /**
         * 加载更多
         */
        get_more: function()
        {
            var self = this;
            var current_page = self.collection.get_current_page();
            self.collection.get_list(++current_page);
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
            self.$hot_city_container = self.$('[data-role=hot-city-container]');
            self.$item_wrap = self.$('[data-role=item-data]');
            self.$load_more_container = self.$('[data-role=load-more-container]'); // 加载更多容器
            self.$load_more_container_v2 = self.$('[data-role=load-more-container-v2]'); // 加载更多容器
            self.$load_txt = self.$('[data-role=load-txt]');

            self.from_app = self.get("from_app");
            
            self.num = self.attrs.is_can_back;

            // console.log(self.num);

            // 安装事件
            self._setup_events();

            // 安装加载更多
            // self._setup_load_more();
        },
        /**
         * 重新加载数据
         */
        refresh: function()
        {
            var self = this;
            self.collection.get_list(1);
        },
        render: function()
        {
            var self = this;
            // 调用渲染函数
            View.prototype.render.apply(self);
            self.trigger('render');
            return self;
        }
    });
    module.exports = list_view;
});