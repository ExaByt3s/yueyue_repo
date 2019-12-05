define(function(require,exports,module){
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var ua = require('../../frame/ua');
    var view = require('../../common/view');
    var scroll = require('../../common/scroll');
    var utility = require('../../common/utility');
    var App = require('../../common/I_APP');
    var footer = require('../../widget/footer/index');
    var slide = require('../../widget/slide/view');
    var slide_v2 = require('../../widget/slide_v2/view');
    var slide_v3 = require('../../widget/slide_v3/view');
    var abnormal = require('../../widget/abnormal/view');
    var select_drop = require('../../ui/select_drop/view');
    var tip = require('../../ui/m_alert/view');
    var m_select = require('../../ui/m_select/view');
    var pull_down = require('../../widget/pull_down/view');


    var main_tpl = require('./tpl/main.handlebars');
    //var main_tpl_v2 = require('./tpl/main_v2.handlebars');
    var item_tpl = require('./tpl/item.handlebars');
    var line_tpl = require('./tpl/line.handlebars');


    var index_hot_view = view.extend(
        {
        attrs:{
            //template : main_tpl_v2
            template : main_tpl
        },
        events:{
            'tap [data-role=page-back]' : function (ev)
            {
                page_control.back();
            },
            'tap [data-role="select-find"]' : function(ev)
            {
                var self = this;

                page_control.navigate_to_page('find');
            },
            'tap [data-role="select-hot"]' : function(ev)
            {
                var self = this;

                self.refresh();
            },
            'tap [data-role="head-refresh"]' : function(ev)
            {
                var self = this;

                self.refresh();
            },
            'tap [data-role="select-more"]' : function()
            {
                var self = this;

                if(!utility.auth.is_login())
                {
                    page_control.navigate_to_page('account/login');

                    return
                }


                if(self.select_drop_obj.is_drop())
                {
                    self.select_drop_obj.pull_up();
                }
                else
                {
                    self.select_drop_obj.drop_down();
                }
            },
            'tap [data-role="select-loc"]' : function()
            {
                page_control.navigate_to_page("location/from_hot")
            },
            'tap [data-role="more"]' : function(ev)
            {
                var self = this;
                var $target = $(ev.currentTarget)
                var add_style = $target.attr('data-add');
                self._load_more(add_style);

                //$target.toggleClass('btn-option-more').prev().find('ul.list').toggleClass('show-all')
                //self.view_scroll_obj.refresh();
            },
            'tap [data-role="model"]' : function(ev)
            {
                var $cur_btn = $(ev.currentTarget);

                var user_id = $cur_btn.attr('data-user-id');

                page_control.navigate_to_page('model_card/'+user_id);
            },
            'tap [data-role="to_rank_score"]' : function()
            {
                page_control.navigate_to_page('rank/score');
            },
            'tap [data-role="search"]' : function()
            {
                page_control.navigate_to_page('search');
            },
            'tap [data-role="to_rank_date"]' : function()
            {
                page_control.navigate_to_page('rank/date');
            },
            'tap [data-role="ad-nav"]' : function(ev)
            {
                var $cur_btn = $(ev.currentTarget);

                var link_type = $cur_btn.attr('data-link-type');
                var link_address = $cur_btn.attr('data-link-address');

                if(utility.is_empty(link_address))
                {
                    return;
                }

                switch (link_type)
                {
                    case 'inside':
                        page_control.navigate_to_page(link_address);
                        break;
                    case 'outside':
                        break;
                    case 'other':
                        break;
                }
            }

        },

        _setup_events:function(){
            var self = this;




            // 模型事件
            // --------------------
            self.listenTo(self.collection, 'reset', self._reset)
                 .listenTo(self.collection, 'add', self._addOne)
                 .listenTo(self.collection, 'before:fetch', self._before_fetch)
                 .listenTo(self.collection, 'success:fetch', self._success_fetch)
                 .listenTo(self.collection, 'error:fetch', self._error_fetch)
                 .listenTo(self.collection, 'complete:fetch', self._complete_fetch)
                 .listenTo(self.model, 'before:ad_pic:fetch', self._before_ad_pic_fetch)
                 .listenTo(self.model, 'success:ad_pic:fetch', self._success_ad_pic_fetch)
                 .listenTo(self.model, 'error:ad_pic:fetch', self._error_ad_pic_fetch)
                 .listenTo(self.model, 'complete:ad_pic:fetch', self._complete_ad_pic_fetch);

            /*self.listenTo(self.collection, 'change', function()
            {
                console.log(1111)
            })*/

            self.on('render',function(){
                //获得广告图
                self.model.get_ad_pic({
                    position : 'index'
                });



            });



            self.collection.get_datas({
                type : '',
                page : 1
            });

            self.on('update_list',function(response, xhrOptions)
            {
                if(!self.view_scroll_obj)
                {

                    self._setup_scroll();

                    self.view_scroll_obj.scrollTo(0,0);

                    return;
                }
                else if(xhrOptions.reset)
                {
                    self.view_scroll_obj.resetLazyLoad();

                    self.view_scroll_obj.reset_top();

                }

                self.view_scroll_obj.refresh();
            });


        },
        /**
         *
         * @private
         */
        _setup_pull_down : function()
        {
            var self = this

            self.pull_down_obj = new pull_down
            ({
                parentNode:self.$refresh_top_bar,
                templateModel:
                {
                    top : -45
                }
            }).render();
        },
        /**
         * 安装滚动条
         * @private
         */

        _setup_scroll : function()
        {
            var self = this;
            //self.$container.css({'height': self.$container.height() + 75});
            var view_scroll_obj = scroll(self.$container,{
                lazyLoad : true,
                _startY : 45,
                prevent_tag : 'slider',
                is_hide_dropdown : ua.isIDevice?false : true
            });

            self.view_scroll_obj = view_scroll_obj;


            self.view_scroll_obj.on('dropload',function(e)
            {
                self.refresh();

            });

            // 拉动结束时
            /*self.view_scroll_obj.on('touchEnd', function()
            {
                if (!self.pull_down_obj._refresh_bar_state) {
                    return;
                }

                var _scroll_obj = this;

                self._pull_refresh();

                _scroll_obj.minScrollY = 0;

                self.pull_down_obj._refresh_bar_state = null;
            })
            // 拖拉中
            .on('scrollMove', function()
            {
                var _scroll_obj = this;

                if (_scroll_obj.y > 65)
                {
                    if (!self.pull_down_obj._refresh_bar_state)
                    {
                        self.pull_down_obj.set_pull_down_style('release');
                        self.pull_down_obj._refresh_bar_state = 'ready';
                    }

                    return;
                }

                if (!self.pull_down_obj._refresh_bar_state)
                {
                    return;
                }

                self.pull_down_obj.set_pull_down_style('loaded');
                self.pull_down_obj._refresh_bar_state = null;
            });*/



        },

        /**
         * 加载下一页数据
         * @private
         */
        _load_more: function(style) {
            var self = this;
            if (self.collection.get_state() == 'free') {
                var current_page = self.collection.get_current_page(style);
                self.collection.get_datas({
                    type : style,
                    page : ++current_page
                });
            }
        },

            /**
             * /加载一行数据
             * @param style
             * @private
             */
        _add_line_item : function(response, xhrOptions){
            var self = this;
            var render_queue = self._render_queue;

            var html_str = line_tpl({
                lists : render_queue
            });

            self.$('[data-role="' + response.result_data.type +'"]').append(html_str);

            self.trigger('update_list',response, xhrOptions);

            self._render_queue = [];

        },

        _render_item: function(response, xhrOptions) {
            var self = this;
            var render_queue = self._render_queue;

            var html_str = item_tpl({
                datas : render_queue[0],
                max_size : Math.ceil(((utility.get_view_port_width() - 105) / 4))//向下取整
            });

            if(response.result_data.empty)
            {
                self.abnormal_view = new abnormal
                ({
                    templateModel :
                    {
                        content_height : utility.get_view_port_height('all') - 75
                    },
                    parentNode:self.$item_wrap
                }).render();
            }
            else
            {
                self.$item_wrap.html(html_str);
            }

            // 重置下拉
            self._drop_reset();

            self.trigger('update_list',response, xhrOptions);



            self._render_queue = [];

        },


        _reset: function() {
            var self = this;
            self.collection.each(self._addOne, self);
        },

        _addOne: function(dataModel) {
            var self = this;
            self._render_queue.push(dataModel.toJSON());
            return self;
        },

        /**
         * 安装底部导航
         * @private
         */
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
                    is_model_pai : true
                }
            }).render();
        },

        _setup_select_drop : function()
        {
            var self = this;

            self.select_drop_obj = new select_drop
            ({
                parentNode: self.$el,
                templateModel:
                {
                    is_model : utility.user.get('role') == 'model' ? true : false
                }
            }).render();

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

/*                self.slide_view = new slide({
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
                    }).render();*/

                if(contents.length == 1)
                {
                    // modify hudw 2014.12.16 临时处理 1张广告图时
                    self.$ad_pic_out.append(contents[0].content).height(145);
                }
                else
                {
                    self.slide_view = new slide_v2({
                        templateModel :
                        {
                            contents : contents,
                            no_single : no_single,
                            height : 145
                        },
                        parentNode:self.$ad_pic_out
                    }).set_options({
                            //loop : true,
                            grab_cursor : true,
                            pagination_clickable : no_single,
                            autoplay : 3000
                        }).render();
                }






/*                self.slide_view = new slide_v3({
                    templateModel :
                    {
                        contents : contents,
                        //height:'75',
                        no_single : no_single
                    },
                    parentNode:self.$ad_pic_out
                }).set_options({
                        contents : contents
                    }).render();*/



                


            },


        set_location : function(location)
        {
            var self = this;

            var location_name = location && location.location_name ;

            self.location = location;

            self.$('[data-role=select-city]').html(location_name);
        },



        setup : function()
        {
            var self = this;

            self.$container = self.$('[data-role=container]'); // 滚动容器
            self.$ad_pic = self.$('[data-role=ad-pic]');//广告图容器
            self.$item_wrap = self.$('[data-role=item-wrap]');

            self.$drop_btn = self.$('[data-role=drop]');

            self.$comment_score_top = self.$('[data-role=comment-score-top]');
            self.$hot_model = self.$('[data-role=hot-model]');



            // 安装事件
            self._setup_events();

            // 安装下拉选择组件
            self._setup_select_drop();

            // 安装底部导航
            self._setup_footer();

            // 设置地区
            self.set_location(utility.storage.get('location'));

            // 渲染队列
            self._render_queue = [];

            self.$refresh_top_bar = self.$('[data-role=refresh-bar-container]');

            //self._setup_pull_down();




        },
        render : function()
        {
            var self = this;

            self._visible = true;

            view.prototype.render.apply(self);

            self.trigger('render');

            return self;
        },

        _before_fetch:function(){
            tip.show('加载中...','loading',{
                delay:-1
            });
        },

        _success_fetch:function(response, xhrOptions){
            var self = this;

            if(self.pull_down_obj)
            {
                self.pull_down_obj.set_pull_down_style('loaded')
            }

            if(response.result_data.code)
            {
                if(response.result_data.type != 'home_page')
                {
                    if(!self._visible)
                    {
                        self.once('after_show',function()
                        {
                            self._add_line_item(response,xhrOptions);
                        });
                    }
                    else
                    {
                        self._add_line_item(response,xhrOptions);
                    }


                }
                else
                {
                    if(!self._visible)
                    {
                        self.once('after_show',function()
                        {
                            self._render_item(response, xhrOptions);
                        });
                    }
                    else
                    {
                        self._render_item(response, xhrOptions);
                    }


                }
            }

            if(!response.result_data.has_next_page){
                self.$('[data-add="' + response.result_data.type + '"]').addClass('fn-hide');
            }

            //self.view_scroll_obj.refresh();




            tip.hide();

        },

        _error_fetch:function(){

            var self = this;

            tip.show('网络异常','error');

            /*tip.show('加载失败','error',{
                delay:1000
            });*/

            if(!self.view_scroll_obj)
            {

                self._setup_scroll();

                //self._pull_refresh();

            }


        },

        _complete_fetch:function(){
            /*tip.hide();*/
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
            if(len == 0){
                return;
            }
            var contents = [];


            //for v2

            for(var i=0; i<pics.length; i++){
                contents.push({
                    content : '<i data-role="ad-nav" data-link-type="' + pics[i].link_type + '" data-link-address="' + pics[i].link_address + '" class ="img image-img loaded" style="background-image: url(' + pics[i].img + ');"></i>'
                })
            }

            //===============================================

            //for v3

/*            for(var i=0; i<pics.length; i++){
                contents.push({
                    width:'320px',
                    height:'75px',
                    content : '<i data-role="ad-nav" data-link-type="' + pics[i].link_type + '" data-link-address="' + pics[i].link_address + '" class ="img image-img loaded" style="background-image: url(' + pics[i].img + ');"></i>'
                })
            }*/
            //===============================================



            self._setup_ad_pic(contents);

            //临时只上html 2014-11-13 zy
//            var contents = '<div class="scroll-area swiper-wrapper" data-role="ad-pic"><div class="swiper-slide" style="height: 100%;width: 100%;"><i data-role="ad-nav" data-link-type="' + pics[0].link_type + '" data-link-address="' + pics[0].link_address + '" class ="img image-img loaded" style="background-image: url(' + pics[0].img + ');"></i></div></div>'
//            self.$ad_pic_out = self.$('[data-role=ad-pic-out]');
//            self.$ad_pic_out.html(contents);

        },

        _error_ad_pic_fetch:function(){
//            tip.show('加载失败','loading',{
//             delay:1000
//             });
        },

        _complete_ad_pic_fetch:function(){

        },
        _drop_reset : function()
        {
            var self = this;

            self.view_scroll_obj && self.view_scroll_obj.resetload();
        },
        refresh : function()
        {
            var self = this;

            self.$item_wrap.html('');
            self.collection.default_page();//初始化页码
            self._render_queue = [];

            self.collection.get_datas({
                type : '',
                page : 1
            });



        },
        _pull_refresh: function()
        {
            var self = this;

            return;

            self.pull_down_obj.set_pull_down_style('loading');

            self.view_scroll_obj.minScrollY = 60;
            self.view_scroll_obj.scrollTo(0, 60);
            self.refresh();
        },
        show : function()
        {
            var self = this;

            var is_trigger = !self._visible;

            self._visible = true;

            is_trigger && self.trigger('after_show');

            return self;
        },
        hide : function()
        {
            var self = this;

            self._visible = false;

            return self;
        }
    });

    module.exports = index_hot_view;
});