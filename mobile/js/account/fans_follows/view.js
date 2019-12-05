/**
 * 首页 - 粉丝列表
 * 汤圆 2014.9.19
 */
define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var View = require('../../common/view');
    var fans = require('../fans_follows/tpl/main.handlebars');
    var utility = require('../../common/utility');
    var templateHelpers = require('../../common/template-helpers');
    var Scroll = require('../../common/new_iscroll');
    var m_alert = require('../../ui/m_alert/view');
    var pull_down = require('../../widget/pull_down/view');
    var load_more = require('../../widget/load_more/view');
    var grid = require('../../widget/model_pic/view');
   
    var abnormal = require('../../widget/abnormal/view');


    var fans_view = View.extend
    ({

        attrs:
        {
            template: fans
        },
        events :
        {
            'swiperight' : function (){
                page_control.back();
            },
            'tap [data-role=page-back]' : function (ev)
            {
                page_control.back();
            },
            'tap [data-role=load-more-container]' : function(ev)
            {
                var self = this;

                self.get_more();
            },
            'touch [data-role="content-body"]' : function()
            {
                var self = this;

                if(!self.select_drop_obj.is_drop())
                {
                    return;
                }

                self.select_drop_obj.pull_up();

            },
            'tap [data-role="select-more"]' : function()
            {
                var self = this;

                if(self.select_drop_obj.is_drop())
                {
                    self.select_drop_obj.pull_up();
                }
                else
                {
                    self.select_drop_obj.drop_down();
                }
            },
            'tap [data-role=load-more-container]' : function(ev)
            {
                var self = this;

                self.get_more();
            },
            'tap [data-role=grid-pic-container]' : function(ev)
            {
                var $current_target = $(ev.currentTarget);
                var user_id = utility.int($current_target.attr('data-user-id'));
                var user_data_role = $current_target.attr('data-user-role') ;
                switch (user_data_role)
                {
                    case 'cameraman': 
                        page_control.navigate_to_page('zone/'+user_id+'/cameraman') ;
                        break;
                    case 'model': 
                        page_control.navigate_to_page('model_card/'+user_id) ;
                        break;
                }     
            }
        },

        /**
         * 安装事件
         * @private
         */
        _setup_events : function()
        {
            var self = this;
            self.collection
            .on('reset',self._reset,self)
            .on('add',self._add_one,self)
            .on('before:fetch',function(xhr,options)
                {
                    if(options.reset)
                    {
                        m_alert.show('加载中...','loading');
                    }
                })
            .on('success:fetch',function(response,xhr)
            {
                m_alert.hide();
                self._render_model_pics_list(response,xhr);
                

            })
            .on('complete:fetch',function(xhr,status)
            {
                //m_alert.hide();
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

                if(xhr.reset)
                {
                    self.view_scroll_obj.scrollTo(0,0);
                    self.view_scroll_obj.resetLazyLoad();
                }

                self.$container.removeClass('fn-hide');

                self.view_scroll_obj.refresh();

            })
            .once('render',self._render_after,self);

            
        },

        /**
         * 安装滚动条
         * @private
         */
        _setup_scroll : function()
        {
            var self = this;

            // 下拉
            self._top_offset = 45;

            var view_scroll_obj = Scroll(self.$container,
            {
                topOffset : self._top_offset,
                lazyLoad: true
            });


            // 下拉刷新标记
            self.pull_refresh = false;

            view_scroll_obj.on('refresh',function()
            {
                // 区分当前对象，_self为滚动条对象
                var _self = this;

                if(self.pull_refresh)
                {
                    self.pull_refresh = false;

                    console.log("下拉刷新");

                    self.pull_down_obj.set_pull_down_style('loaded');

                }

            });

            view_scroll_obj.on('scrollMoveAfter',function()
            {
                // 区分当前对象，_self为滚动条对象
                var _self = this;

                var scroll_y = _self.y;

                if (scroll_y > 5 && !self.pull_refresh)
                {
                    //释放刷新

                    _self.minScrollY = 0;

                    self.pull_refresh = true;

                    self.pull_down_obj.set_pull_down_style('release');

                    console.log('释放刷新');

                }
            });

            view_scroll_obj.on('scrollEndAfter',function()
            {
                // 区分当前对象，_self为滚动条对象
                var _self = this;

                var scroll_y = _self.y;

                if(self.pull_refresh)
                {
                    // 加载中...
                    console.log("加载中");

                    self.pull_down_obj.set_pull_down_style('loading');

                    self.refresh();
                }

                if(self.$load_more_container)
                {

                    self._scroll_check();
                }

            });

            self.view_scroll_obj = view_scroll_obj;
        },
         /**
         * 安装下拉刷新
         * @private
         */
        _setup_pull_down : function()
        {
            var self = this;

            self.pull_down_obj = new pull_down
            ({
                // 元素插入位置
                parentNode: self.$pull_down_container,
                templateModel:
                {
                    top :-45
                }
            }).render();
        },

        /**
         * 安装加载更多按钮
         * @private
         */
        _setup_load_more : function()
        {
            var self = this;

            self.load_more_btn = new load_more
            ({
                parentNode: self.$load_more_container
            }).render();
        },
       
        _reset : function()
        {
            var self = this;
            self.collection.length && self.collection.each(self._add_one,self);
        },
        _add_one : function(model)
        {
            var self = this;
            self._render_queue.push(model.toJSON());
            return self;
        },
        /**
         * 渲染图片列表
         * @param response
         * @param options
         * @private
         */


         _render_model_pics_list : function(response,xhr)
        {
            var self = this;
            var render_queue = self._render_queue;

            // 没有数据的时候，直接return
            if(render_queue.length == 0)
            {

              
                self.$('[data-role=pull-down]').addClass('fn-hide');

                self.abnormal_view = new abnormal({
                    templateModel :
                    {
                        content_height : utility.get_view_port_height('all') - 45
                    },
                    parentNode:self.$grid_list_container
                }).render();       

                //m_alert.show('暂时没有数据！','error',{delay:2000});
                //self.$container.addClass('fn-hide');
                return;
            }


            // 判断是否有下一页
            if(self.collection.has_next_page)
            {
                self.load_more_btn.show();
            }
            else
            {
                self.load_more_btn.hide();
            }
            


            // user id 转换
            if( self.get('type') == 'follow')
            {
                // self.$header_title.html('关注');
                $.each(render_queue, function(i, obj) {
                     obj.user_id = obj.be_follow_user_id
                });
            }
            else
            {
                // self.$header_title.html('粉丝');
               $.each(render_queue, function(i, obj) {
                     obj.user_id = obj.follow_user_id
                });
            };

            var art_imgs = [];
            var pic_arr = render_queue;

            for(var i=0;i < pic_arr.length; i++){
                var type;
                if((i+2)%3==0)
                {
                    type = 'middle';
                }
                else
                {
                    type = 'one';
                }

                pic_arr[i].type = type;

                art_imgs.push(pic_arr[i]);


                /*art_imgs.push({
                    'type' : type,
                    'user_icon' : pic_arr[i].user_icon,
                    'role' : pic_arr[i].role,
                    'user_id' : pic_arr[i].follow_user_id
                });*/
            }

            var grid_list_view = new grid
            ({
                // 模板参数对象
                templateModel :
                {
                    tpl_data : art_imgs,
                    tpl_type : 'one'
                }
            }).render();

            // 判断调用重新加载还是插入方法
            var method = xhr.reset ? 'html' : 'append';

            self.$grid_list_container[method](grid_list_view.list());

            self.trigger('update_list',response,xhr);

            // 情况队列
            self._render_queue = [];

            //self._setup_notice();
        },

        _scroll_check : function()
        {

            var self = this;

            if(!self.$load_more_container || !self._visible || !self.collection.has_next_page)
            {
                return;
            }

            var pos = self.$load_more_container.position();

            if(pos.top < self._viewportHeight)
            {
                self.get_more();
            }
            
        },

         _render_city : function(response,xhr)
        {
            var self = this;
 
            self.trigger('update_list',response,xhr);
        },

        _render_after :function()
        {
            var self = this ;

            self.collection.get_list(1);

            self._viewportHeight = utility.get_view_port_height('nav');
        },
        /**
         * 加载更多
         */
        get_more : function()
        {

            var self = this;

            var current_page = self.collection.get_current_page();

            self.collection.get_list(++current_page);
        },
        change_title : function ()
        {
            var self = this;
            if( self.get('type') == 'follow')
            {
                self.$header_title.html('关注');
            }else{
                self.$header_title.html('粉丝');
            };
        },
        /**
         * 重新加载数据
         */
        refresh : function()
        {
            var self = this;

            self.collection.get_list(1);

        },

        /**
         * 视图初始化入口
         */
        setup : function()
        {

            var self = this;

            // 渲染队列
            self._render_queue = [];

            // 配置交互对象
            self.$container = self.$('[data-role=container]'); // 滚动容器

            self.$grid_list_container = self.$('[data-role=grid-list-container]');// 方图容器
            self.$pull_down_container = self.$('[data-role=pull-down-wrapper]');// 下拉刷新容器
            self.$load_more_container = self.$('[data-role=load-more-container]');// 加载更多容器
            self.$header_title = self.$('[data-role=header-title]');// 加载更多容器


    
            // 渲染队列
            self._render_queue = [];

            // 安装事件
            self._setup_events();

            // 安装下拉刷新
            self._setup_pull_down();

            // 安装加载更多
            self._setup_load_more();

            //self.change_title();

        },
        render : function()
        {
            var self = this;

            // 调用渲染函数
            View.prototype.render.apply(self);

            self.trigger('render');

            return self;
        }

    });

    module.exports = fans_view;
});