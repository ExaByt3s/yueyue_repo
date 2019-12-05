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
    var App = require('../../common/I_APP');
    var abnormal = require('../../widget/abnormal/view');

    //当前页引用
    var current_main_template = require('./tpl/main.handlebars');
    var item_tpl = require('./tpl/item.handlebars');


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
            'tap [data-user-id]' : function (ev)
            {
                var self = this;
                var data_user_id = $(ev.currentTarget).attr('data-user-id');

                if(App.isPaiApp)
                {
                    App.nav_to_app_page
                    ({
                        page_type : 'model_card',
                        user_id : data_user_id
                    });
                }
                else
                {
                    page_control.navigate_to_page('model_card/'+data_user_id);
                }


            }
        },
        /**
         * 安装事件
         * @private
         */
         setup_events: function()
         {
             var self = this;

             //collection操作
             self.collection
                 .on('reset', self._reset, self)
                 .on('add', self._add_one, self)
                 .on('before:fetch', function(response, xhr)
                 {
                     m_alert.show('加载中...', 'loading');
                 })
                 .on('success:fetch', function(response, xhr)
                 {
                    m_alert.hide();
                    self.render_html(response, xhr);
                 })
                 .on('error:fetch', function(response, xhr)
                 {
                    m_alert.hide();
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
                 .once('render', self.render_after, self);
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

            self.$item_wrap = self.$('[data-role=container-ele]'); 

            // 安装事件
            self.setup_events();

            // 安装滚动条
            self._setup_scroll();

            self.fetching = false ;

            self.order_id = self.get('order_id');


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
            console.log(render_queue);

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

                // self.$get_more.hide();
                return;
            }


            // 判断是否有下一页
            if (self.collection.has_next_page)
            {
                // self.$get_more.show();
            }
            else
            {
                // self.$get_more.hide();
            }

            var html_str = item_tpl(
            {
                datas: render_queue
            });

            // 判断调用重新加载还是插入方法
            var method = xhr.reset ? 'html' : 'append';
            self.$item_wrap[method](html_str);


            self._drop_reset();
            self.trigger('update_list', response, xhr);


            self.fetching = true ;
        },
        
        render_after: function()
        {
            var self = this;

            //设置参数
            self.collection.set_rqs_params(
            {
                page: 1,
                order_id: self.order_id
            });


            //发送请求
            self.collection.get_list(1);

        },

        /**
         * 安装滚动条
         * @private
         */
        // 滚动条移动加载更多
        _setup_scroll : function()
        {

            var self = this;
            var view_scroll_obj = Scroll(self.$container,
            {
                down_direction : 'down'
            });

            self.view_scroll_obj = view_scroll_obj;

            // 上拉刷新
            self.view_scroll_obj.on('dropload',function(e)
            {
                // self.refresh();
                self._drop_reset();
                //发送请求
                self.collection.get_list(1);
                console.log("刷新");

            });

            // 下拉加载更多
            self.view_scroll_obj.on('pullload',function(e)
            {
                //有下一页时才加载
                if(self.collection.has_next_page && self.fetching ) //fetching 必须在第一次页面渲染完成 才能继续加载
                {
                    self.get_more();
                    self.fetching = false ;
                }
                else
                {
                    self._drop_reset();
                }

            });

            self.view_scroll_obj.refresh();
        },

        /**
         * 加载更多
         */
        get_more : function()
        {
            var self = this;
            var current_page = self.collection.get_current_page();

            //设置参数
            self.collection.set_rqs_params(
            {
                page: ++current_page,
                order_id: self.order_id
            });

            self.collection.get_list(++current_page);

        },
        _drop_reset : function()
        {
            var self = this;

            self.view_scroll_obj && self.view_scroll_obj.resetload();
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
            self.collection.length && self.collection.each(self._add_one, self);
        },
        _add_one: function(model)
        {
            var self = this;
            self._render_queue.push(model.toJSON());
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