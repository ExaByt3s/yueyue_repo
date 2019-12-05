/**
 * Created by nolest on 2014/8/30.
 * 活动列表
 */

define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var View = require('../../common/view');
    var tpl = require('./tpl/main.handlebars');
    var utility = require('../../common/utility');
    var Scroll = require('../../common/scroll');
    var select_drop = require('../../ui/select_drop/view');
    var slide = require('../../widget/slide/view');
    var footer = require('../../widget/footer/index');
    var act_list_view = require('../../widget/act_list/view');
    var pull_down = require('../../widget/pull_down/view');
    var m_alert = require('../../ui/m_alert/view');
    var abnormal = require('../../widget/abnormal/view');

    var model_card_view = View.extend
    ({

        attrs:
        {
            template: tpl
        },
        events :
        {
            'swiperight' : function (){
                page_control.back();
            },
            'tap [data-tap]' : function (ev)
            {
                var self = this;

                var type = $(ev.currentTarget).find('[data-role]').attr('data-role');

                if(self.current_type == type && self.is_show_condition)
                {
                    self._hide_condition_con();

                    return
                }
                self.current_type = type;

                self.$('[data-role="arrow"]').removeClass("icon-act-list-arrow-red").addClass("icon-act-list-arrow");

                $(ev.currentTarget).find('[data-role="arrow"]').addClass("icon-act-list-arrow-red");

                self.$('[data-text]').removeClass("cur");

                $(ev.currentTarget).find('[data-role]').addClass("cur");

                self._show_condition_con();

                self.$('[data-role="'+ type+'-con"]').removeClass("fn-hide");

            },
            'tap [data-role="fade"]' : function()
            {
                var self = this;

                self.$('[data-tap]').removeClass("cur");

                self._hide_condition_con();

            },
            'tap [data-carry]' : function(ev)
            {
                var self = this;

                var $btn = $(ev.currentTarget);

                $btn.addClass("cur").siblings().removeClass("cur");

                var query = $btn.attr('data-carry');

                var query_html = $btn.text();

                switch (self.current_type)
                {
                    case 'time' :
                        self.mix_options =
                        {
                            page : 1,
                            time_querys : query
                            //price_querys : "",
                            //start_querys : ""
                        };
                        if(query == '')
                        {
                            query_html = '时间'
                        }
                        self.$('[data-role="time"]').html(query_html);
                        break;
                    case 'price' :
                        self.mix_options =
                        {
                            page : 1,
                            //time_querys : "",
                            price_querys : query
                            //start_querys : ""
                        };
                        if(query == '')
                        {
                            query_html = '价格'
                        }
                        self.$('[data-role="price"]').html(query_html);
                        break;
                    case 'start' :
                        self.mix_options =
                        {
                            page : 1,
                            //time_querys : "",
                            //price_querys : "",
                            start_querys : query
                        };
                        if(query == '')
                        {
                            query_html = '发起者'
                        }
                        self.$('[data-role="start"]').html(query_html);
                        break;
                }

                self.$('[data-role="arrow"]').removeClass("icon-act-list-arrow-red").addClass("icon-act-list-arrow");

                self.clearContainer = true;

                self.select_options = $.extend(true,{},self.select_options,self.mix_options,{reset:1});

                self.collection.set_select_options(self.select_options);

                self._hide_condition_con();

                self.collection.get_list();

            },
            'tap [data-role="select-more"]' : function()
            {
                var self = this;
                if(!utility.auth.is_login())
                {
                    page_control.navigate_to_page('account/login');

                    return
                }

                page_control.navigate_to_page("act/pub_info",utility.user);

                return;

                if(self.select_drop_obj.is_drop())
                {
                    self.select_drop_obj.pull_up();
                }
                else
                {
                    self.select_drop_obj.drop_down();
                }
            },
            'tap [data-role="event-item"]' : function(ev)
            {
                var $cur_btn = $(ev.currentTarget);

                if(!utility.auth.is_login())
                {
                    page_control.navigate_to_page('account/login');

                    return
                }

                var event_id = utility.int($cur_btn.attr('data-event-id'));

                page_control.navigate_to_page('act/detail/'+event_id);
            },
            'touch [data-role="content-body"]' : function()
            {
                var self = this;

                if(!self.select_drop_obj.is_drop())
                {
                    return;
                }

                self.select_drop_obj.pull_up();
            }
        },
        _show_condition_con : function()
        {
            var self = this;

            self.$condition.removeClass("fn-hide");

            self.$('[data-con]').addClass("fn-hide");

            self.is_show_condition = true;
        },
        _hide_condition_con : function()
        {
            var self = this;

            self.$condition.addClass("fn-hide");

            self.$('[data-con]').addClass("fn-hide");

            self.is_show_condition = false;
        },
        _is_show : function()
        {
            var self = this;

            return self.is_show_condition;
        },
        _setup_pull_down : function()
        {
            var self = this;

            self.pull_down_obj = new pull_down
            ({
                // 元素插入位置
                parentNode: self.$pull_down_container
            }).render();
        },
        _setup_select_drop : function()
        {
            var self = this;

            self.select_drop_obj = new select_drop
            ({
                parentNode: self.$el

            }).render();

        },
        _setup_events : function()
        {
            var self = this;

//            self.collection
//                .on('reset',self._reset,self)
//                .on('add', self._add_one, self)
//                .on('before:fetch',function(xhr)
//                {
//                    if(xhr.reset)
//                    {
//                        m_alert.show('加载中...','loading',{delay:1000});
//                    }
//                })
//                .on('success:fetch',function(response,xhr)
//                {
//                    m_alert.hide();
//
//                    self._render_act_list(response,xhr)
//                })
//                .on('error:fetch',function(response,xhr)
//                {
//                    m_alert.show('网络不给力','error',{delay:1000})
//                })
//                .on('complete:fetch',function(xhr,status)
//                {
//                    self.fetching = false;
//                });

            self.listenTo(self.collection, 'reset', self._reset)
                .listenTo(self.collection, 'add', self._addOne)
                .listenTo(self.collection, 'before:fetch',function(xhr)
                {
                    // modif by hudw 2015.2.6
                    if(self.collection && self.collection.get_select_options().page == 1)
                    {
                        m_alert.show('加载中...','loading');
                    }
                })
                .listenTo(self.collection, 'success:fetch',function(response,xhr)
                {

                    self._render_act_list(response,xhr)

                    if(self.$list_container.children().length == 0)
                    {
                        self.abnormal_view = new abnormal({
                            templateModel :
                            {
                                content_height : utility.get_view_port_height('all') - 75
                            },
                            parentNode:self.$list_container
                        }).render();
                    }
                    m_alert.hide();
                })
                .listenTo(self.collection, 'error:fetch',function(response,xhr)
                {
                    m_alert.show('网络不给力','error',{delay:1000});

                    // 重置下拉
                    self._drop_reset();
                })
                .listenTo(self.collection, 'complete:fetch',function(xhr,status)
                {
                    self.fetching = false;
                })
                .listenTo(self.model, 'before:ad_pic:fetch', self._before_ad_pic_fetch)
                .listenTo(self.model, 'success:ad_pic:fetch', self._success_ad_pic_fetch)
                .listenTo(self.model, 'error:ad_pic:fetch', self._error_ad_pic_fetch)
                .listenTo(self.model, 'complete:ad_pic:fetch', self._complete_ad_pic_fetch);

            self.on('update_list',function(response,xhr)
            {
                var view_port_height = self.reset_viewport_height()- 90;

                self.$container.height(view_port_height);

                if(!self.view_scroll_obj)
                {
                    self._setup_scroll();

                    // 重置下拉
                    self._drop_reset();

                    return;
                }

                if(xhr.reset)
                {
                    self.view_scroll_obj.reset_top();

                    self.view_scroll_obj.resetLazyLoad();

                }

                // 重置下拉
                self._drop_reset();

                self.view_scroll_obj.refresh();

            });

            self.on('render',function(){
                //获得广告图
                /*self.model.get_ad_pic({
                    position : 'list'
                });*/

            });

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
        _render_act_list: function(response,xhr)
        {
            var self = this;

            var render_queue = (response.result_data && response.result_data.list) || [];

            var document_frag = document.createDocumentFragment();


            $.each(render_queue,function(i,obj)
            {
                var pre_view = new act_list_view
                ({
                    model : obj,
                    templateModel : obj
                }).render();

                document_frag.appendChild(pre_view.list()[0]);

            });

            // 判断调用重新加载还是插入方法
            var method = xhr.reset ? 'html' : 'append';

            self.$list_container[method](document_frag);

            self.trigger('update_list',response,xhr);

            // 清空队列
            self._render_queue = [];

        },
        _setup_scroll : function()
        {
            var self = this;

            self._top_offset = 90;

            var view_scroll_obj = Scroll(self.$container,
                {
                    topOffset : self._top_offset,
                    lazyLoad : true,
                    down_direction : 'down',
                    down_direction_distance :50
                });

            self.view_scroll_obj = view_scroll_obj;

            // 上拉刷新
            self.view_scroll_obj.on('dropload',function(e)
            {
                console.log(e);

                self.collection.set_select_options
                ({
                    page : 1,
                    reset : 1
                });

                self.collection.get_list();

            });

            // 下拉加载更多
            // hudw 2015.1.30
            self.view_scroll_obj.on('pullload',function(e)
            {
                console.log(e)

                self.clearContainer = false;

                self.fetching = true; //防止反复提交请求

                self.collection.set_select_options
                ({
                    page : ++self.collection.get_select_options().page,
                    reset : 0
                });

                self.collection.get_list();

            });

            /*// 下拉刷新标记
            self.pull_refresh = false;

            view_scroll_obj.on('refresh',function()
            {
                console.log("scrollrefresh")
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
                var _self = this;

                var scroll_y = _self.y;

                if(_self.maxScrollY - _self.y > 160 && self.collection.has_next_page && !self.fetching)
                {

                    self.clearContainer = false;

                    self.fetching = true; //防止反复提交请求

                    self.collection.set_select_options
                    ({
                        page : ++self.collection.get_select_options().page
                    });

                    self.collection.get_list();
                }

                if(scroll_y > 0 && !self.pull_refresh)
                {
                    _self.minScrollY = -45;

                    self.pull_refresh = true;

                    self.pull_down_obj.set_pull_down_style('release');
                }
            });

            view_scroll_obj.on('scrollEndAfter',function()
            {
                var _self = this;

                var scroll_y = _self.y;

                if(self.pull_refresh)
                {
                    self.pull_down_obj.set_pull_down_style('loading');

                    self.collection.get_list();
                }


            });
*/


        },
        _drop_reset : function()
        {
            var self = this;

            self.view_scroll_obj && self.view_scroll_obj.resetload();
        },
        _setup_footer : function()
        {
            var self = this;

            var footer_obj = new footer
            ({
                // 元素插入位置
                parentNode: self.$el,
                // 模板参数对象
                templateModel :
                {
                    // 高亮设置参数
                    is_out_pai : true
                }
            }).render();
        },

        reset_viewport_height : function()
        {
            return utility.get_view_port_height('nav');
        },

        _before_ad_pic_fetch:function(){
//            tip.show('加载中...','loading',{
//             delay:-1
//             });
        },

        _success_ad_pic_fetch:function(response, xhrOptions){
            var self = this;

            //安装广告滚动(这个必需在渲染后安装，否则获取不到宽度)
            var pics = response.result_data.list;
            if(!pics ){
                return;
            }
            var len = pics.length;
//            if(len == 0){
//                return;
//            }
            var contents = []

            for(var i=0; i<pics.length; i++){
                contents.push({
                    content : '<i class ="img image-img loaded" style="background-image: url(' + pics[i].img + ');"></i>'
                });
            }

            self._setup_ad_pic(contents);
        },

        _error_ad_pic_fetch:function(){
//            tip.show('加载失败','loading',{
//             delay:1000
//             });
        },

        _complete_ad_pic_fetch:function(){

        },

        /**
         * 安装广告滚动
         * @param contents
         * @private
         */
        _setup_ad_pic : function(contents){


            if(!(contents.length > 0)){
                return;
            }
            var self = this;

            //当只有一张图时不显示小圆点
            var no_single;
            (contents.length > 1) ? ( no_single = true ) : ( no_single = false );
            //设置选中第一个
            contents[0].class = "current";

            self.$ad_pic_out = self.$('[data-role=ad-pic-out]');

            self.slide_view = new slide({
                templateModel :
                {
                    contents : contents,
                    no_single : no_single
                },
                parentNode:self.$ad_pic_out
            }).set_options({
                    disableScroll : true, // 停止滚动冒泡
                    continuous : true,// 无限循环的图片切换效果
                    //auto : 1000,
                    startSlide : 0 //起始图片切换的索引位置
                }).render();


        },

        setup : function()
        {
            var self = this;
            // 配置交互对象
            self.$container = self.$('[data-role="action-list-content"]'); // 滚动容器

            self.$list_container = self.$('[data-role="action-child-con"]'); //列表容器

            self.$condition = self.$('[data-role="condition-con"]');//选项层

            self.$pull_down_container = self.$('[data-role=pull_down_container]');

            // 安装事件
            self._setup_events();

            self._setup_select_drop();

            self._setup_footer();

            //self._setup_pull_down();

            self.is_show_condition = false;

            self.fetching = false;

            self.clearContainer = false;

            self.select_options =
            {
                page : 1,
                time_querys : "",
                price_querys : "",
                start_querys : ""
            };

            self._render_queue = [];

            self.collection.get_list();

        },
        render : function()
        {
            var self = this;

            var view_port_height = self.reset_viewport_height()- 90;

            self.$container.height(view_port_height);

            View.prototype.render.apply(self);

            self.trigger('render');

            return self;
        }

    });

    module.exports = model_card_view;
});