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
    var page_control = require('../frame/page_control');
    var View = require('../common/view');
    var utility = require('../common/utility');
    var templateHelpers = require('../common/template-helpers');

    // var Scroll = require('../common/scroll'); //new_iscroll 原生
    var Scroll = require('../common/new_iscroll'); //new_iscroll 方法

    var footer = require('../widget/footer/index');
    var m_alert = require('../ui/m_alert/view');
    var I_App = require('../common/I_APP');
    var abnormal = require('../widget/abnormal/view');

    //当前页引用
    var current_main_template = require('./tpl/main.handlebars');
    var item_tpl = require('./tpl/item.handlebars');

    var top_3 = require('./tpl/top_3.handlebars');
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
            'tap [data-role=btn-get-more]' : function(ev)
            {
                var self = this;
                self.get_more();
            },
            'tap [data-role=go_url]' : function(ev)
            {
                var self = this;
                var $cur_btn = $(ev.currentTarget);
                var user_id = $cur_btn.attr('user-id');
                page_control.navigate_to_page('model_card/'+user_id);

            },
            'tap [data-role=icon_display]' : function(ev)
            {
                //排行榜说明跳转
                var self = this;
                var $cur_btn = $(ev.currentTarget);
                var key = $cur_btn.attr('key');
                page_control.navigate_to_page('mine/explain/'+key);

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
                .once('render', self.render_after, self);
        },

        /**
         * 视图初始化入口
         */
        setup: function()
        {
            var self = this;


            // 数据参数
            self.type = self.get('type');

            // 地区id
            var loc;

            if(utility.storage.get('location').location_id)
            {
                loc = utility.storage.get('location').location_id;
            }
            else
            {
                loc = 0;
            }

            self.loc = loc;

            // 渲染队列
            self._render_queue = [];

            // 配置交互对象
            self.$container = self.$('[data-role=container]'); // 滚动容器
            self.$get_more = self.$('[data-role=btn-get-more]');  //加载更多按钮
            self.$item_wrap = self.$('[data-role=item-wrap]'); 

            self.$title = self.$('[data-role=title]'); 
            self.$data_top_3 = self.$('[data-role=data_top_3]'); 
            self.$data_top_7 = self.$('[data-role=data_top_7]'); 

            self.$icon_display = self.$('[data-role=icon_display]'); 

            // 安装事件
            self.setup_events();

            // 安装滚动条
            self._setup_scroll();

            //翻页加载完成控制
            self.fetching = false ;

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

            //top3  计算图片大小，适应屏幕
            self.img_max_w_top = Math.floor(((utility.get_view_port_width() - 40) / 3));
            self.img_max_h_top = Math.floor((self.img_max_w_top / 3) * 4);
            self.img_max_w_top = self.img_max_w_top -2;//减去2像素边框


            //top7  计算图片大小，适应屏幕
            self.img_max_w_7 = Math.floor((utility.get_view_port_width() * 0.195 ));
            self.img_max_h_7 = Math.floor((self.img_max_w_7 / 3) * 4);
            self.img_max_w_7 = self.img_max_w_7 -2;//减去2像素边框




            var render_queue_list = self._render_queue.list;

            self.title = render_queue_list.title;
            self.icon_display = render_queue_list.key;

            //是否显示标题右边icon
            if (self.icon_display && self.icon_display !== '') 
            {
                self.$icon_display.attr('key', self.icon_display);
                self.$icon_display.removeClass('fn-hide');
            } 
           

            self.$title.html(self.title);

            // 没有数据的时候，直接return
            if (!render_queue_list.list && !render_queue_list.top_list)
            {
                self.abnormal_view = new abnormal(
                {
                    templateModel:
                    {
                        content_height: utility.get_view_port_height('all') - 45
                    },
                    parentNode: self.$item_wrap
                }).render();

                self.$get_more.hide();
                return;
            }

            // 判断是否有下一页
            if (self.collection.has_next_page)
            {
                self.$get_more.show();
            }
            else
            {
                self.$get_more.hide();
            }


            //首次渲染
            if (xhr.reset) 
            {
                //前三处理
                // var data_top_3 =  render_queue.slice(0,3);
                // $.each(data_top_3, function(i, val) {
                //     val.index = i+1 ;
                // });
                // var html_top_3 = top_3(
                // {
                //     data: data_top_3
                // });
                // self.$data_top_3.html(html_top_3);
                // 
                // 

                // 头部列表

                if(render_queue_list.top_list)
                {
                    var data_top_3 =  render_queue_list.top_list;
                    $.each(data_top_3, function(i, val) {
                        val.index = i+1 ;
                    });

                    var html_top_3 = top_3(
                        {
                            data: data_top_3,
                            img_max_w : self.img_max_w_top,
                            img_max_h : self.img_max_h_top
                        });
                    self.$data_top_3.html(html_top_3);
                }




                // 前7处理
                // var len = render_queue.length ;
                // var data_top_7 =  render_queue.slice(3,len);

                // $.each(data_top_7, function(i, val) {
                //     val.index = i+4 ;
                // });

                // var html_str = item_tpl(
                // {
                //     data: data_top_7
                // });

                // self.$data_top_7.html(html_str);

                // self.view_scroll_obj.scrollTo(0,0); //解决后加载，第一次显示的问题

                if(render_queue_list.list)
                {
                    var data_top_7 = render_queue_list.list;

                    var html_str = item_tpl(
                        {
                            data: data_top_7,
                            img_max_w : self.img_max_w_7,
                            img_max_h : self.img_max_h_7
                        });

                    self.$data_top_7.html(html_str);

                    self.view_scroll_obj.scrollTo(0,0); //解决后加载，第一次显示的问题
                }




            } 
            else
            {
                // $.each(render_queue, function(i, val) {
                //     val.index = i+11 ;
                // });
                // 
                if (render_queue_list.list) 
                {
                    var render_queue = render_queue_list.list;
                    var html_str = item_tpl(
                    {
                        data: render_queue,
                        img_max_w : self.img_max_w_7,
                        img_max_h : self.img_max_h_7
                    });

                    // 判断调用重新加载还是插入方法
                    var method = xhr.reset ? 'html' : 'append';
                    self.$data_top_7[method](html_str);
                }
            
            }

        
            self.trigger('update_list', response, xhr);

            self.fetching = true ;
        },
        
        render_after: function()
        {
            var self = this;

            //设置参数
            self.collection.set_rqs_params(
            {
                rank_id: self.type,
                page: 1
            });

            //发送请求
            self.collection.get_list(1);
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
                rank_id: self.type,
                page: ++current_page
            });

            self.collection.get_list(++current_page);
        },

        /**
         * 安装滚动条
         * @private
         */

        // _setup_scroll: function()
        // {
        //     var self = this;
        //     var view_scroll_obj = Scroll(self.$container,
        //     {
        //         is_hide_dropdown: true ,
        //         lazyLoad : true // 后加载 
        //     });
        //     self.view_scroll_obj = view_scroll_obj;
        // },


        // 滚动条移动加载更多
        _setup_scroll : function()
        {
            var self = this;
            var view_scroll_obj = Scroll(self.$container,
            {
                is_hide_dropdown: true,
                lazyLoad : true // 后加载 
            });
            view_scroll_obj.on('scrollMoveAfter',function()
            {
                var _self = this; //区分当前滚动条对象
                if(_self.maxScrollY - _self.y > 100) //滚动条移动距离加载
                {
                    //有下一页时才加载
                    if(self.collection.has_next_page && self.fetching ) //fetching 必须在第一次页面渲染完成 才能继续加载
                    {
                        self.get_more();
                        self.fetching = false ;
                    }
                }
            });

            self.view_scroll_obj = view_scroll_obj;
            self.view_scroll_obj.refresh();
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
            self._render_queue =  self._render_queue[0] ;
            return self;
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
    module.exports = current_view;
});