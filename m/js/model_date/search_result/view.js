define(function(require,exports,module){
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var View = require('../../common/view');
    var Scroll = require('../../common/scroll');
    var Utility = require('../../common/utility');
    var tip = require('../../ui/m_alert/view');
    var abnormal = require('../../widget/abnormal/view');



    var main_tpl = require('./tpl/main.handlebars');
    var item_tpl = require('./tpl/item.handlebars');

    var search_view = View.extend({
        attrs:{
            template:main_tpl
        },
        events:{
            'tap [data-role=page-back]' : function (ev)
            {
                page_control.back();
            },
            'tap [data-role=to-model]' : function(ev)
            {
                var $target = $(ev.currentTarget);

                var user_id = $target.attr('data-user-id');

                page_control.navigate_to_page('model_card/'+user_id);
            },
            'tap [data-choosen]' : function(ev)
            {
                var self = this;

                var $target = $(ev.currentTarget);

                var data_choosen = $target.attr('data-choosen');

                var select_str = '[data-role="condition-' + data_choosen + '"]';



                $target.addClass('cur');

                $target.siblings('[data-choosen]').removeClass('cur');

                self.$('[data-all]').addClass('fn-hide');

                self.$('[data-role="choosen-open"]').removeClass('fn-hide');

                self.$(select_str).removeClass('fn-hide');



            },
            'tap [data-role="child-tap"]':function(ev)
            {
                var self = this;

                var $target = $(ev.currentTarget);

                console.log("1")
                $target.addClass('cur');
                console.log("2")
                $target.siblings().removeClass('cur');
                console.log("3")
                var data_mix = $target.attr('data-mix');
                console.log("4")
                var type = self.$('[data-choosen]').parents().children('.cur').attr('data-choosen');
                console.log("5")
                switch(type)
                {
                    case 'price':self.search_data = $.extend(true,{},self.search_data,{price:data_mix,page:1});break;
                    case 'hour': self.search_data = $.extend(true,{},self.search_data,{hour:data_mix,page:1});break;
                    case 'order': self.search_data = $.extend(true,{},self.search_data,{order:data_mix,page:1});break;
                }
                console.log("6")
                tip.show('查询中...', 'loading', {
                    delay: 800
                });
                console.log("7")

                // modify by hudw 2015.3.3
                // 设置选中筛选项 文案高亮
                var $selectd_text = self.$('[data-choosen="'+type+'"]').find('[data-role="selected-text"]');
                console.log("8")
                $selectd_text.html($target.text());

                self.collection.get_search_result_list(self.search_data);

            },
            'tap [data-role="mask"]' : function()
            {
                var self = this;

                self.$('[data-role="choosen-open"]').addClass('fn-hide');
            }
        },
        /**
         * 事件安装
         * @private
         */
        _setup_events: function() {
            var self = this;

//            self.listenTo(self.collection,'all',function() {
//                // debug 用
//            });
            self.listenTo(self.collection, 'reset', self._reset)
                .listenTo(self.collection, 'add', self._addOne)
                .listenTo(self.collection, 'before:fetch', function() {
//                    tip.show('查询中...', 'loading', {
//                        delay: -1
//                    });
                    if(self.$load_more)
                    {
                        self.$load_more.html('查询中...');
                    }

                })
                .listenTo(self.collection, 'success:fetch', function(response,xhrOptions ) {

                    if(response.result_data.empty){

                        self.$search_result_list.html('');

                        self.abnormal_view = new abnormal({
                            templateModel :
                            {
                                content_height : Utility.get_view_port_height('all') - 35
                            },
                            parentNode:self.$search_result_list
                        }).render();



                    }else{

                        self._render_item(response, xhrOptions);
                        if(self.$load_more)
                        {
                            self.$load_more.html('上拉加载更多');
                        }

                        //self.view_scroll_obj.scrollTo(0,0);
                    }

                    self.has_next_page = response.result_data && response.result_data.has_next_page;

                    //没有更多数据去除可以加载更多提示
                    if (!response.result_data.has_next_page) {
                        //self.$load_more.remove();
                        //self.$load_more = null;
                    }



                    tip.hide();

                })
                .listenTo(self.collection, 'error:fetch', function(xhr, status) {
                    tip.show('网络异常', 'error', {
                        delay: 800
                    });
                })
                .listenTo(self.collection, 'complete:fetch', function(xhr, status) {
                    self.$('[data-role="choosen-open"]') && self.$('[data-role="choosen-open"]').addClass('fn-hide');

                    self.fetching = false;

                    self._drop_reset();
                });


            self.on('render',function(){
                self.collection.get_search_result_list(self.search_data);
            });



            // 视图更新
            self.on('updateList', function(response, xhrOptions) {

                // 第一次载入时iScroll未生成
                if (!self.view_scroll_obj) {
                    self._setup_scroll();

                }
                self.view_scroll_obj.refresh();


            });
        },

        _render_item : function(response, xhrOptions) {
            var self = this;

            // modify by hudw
            var renderQueue = response.result_data.list && response.result_data.list.list;
            var htmlStr = item_tpl({
                search_list: renderQueue
            });


            var method = xhrOptions.reset ? 'html' : 'append';
            self.$search_result_list[method](htmlStr);
            self.trigger('updateList', response, xhrOptions);

            self._renderQueue = [];

            self.view_scroll_obj.refresh();

            if(method == 'html')
            {
                self.view_scroll_obj.reset_top();

                self.view_scroll_obj.force_load_img();

            }


        },

        _reset: function() {
            var self = this;

            self.collection.each(self._addOne, self);
        },

        _addOne: function(dataModel) {
            var self = this;

            self._renderQueue.push(dataModel.toJSON());

            return self;
        },

        /**
         * 安装滚动条
         * @private
         */

        _setup_scroll : function()
        {
            var self = this;

            // modify by hudw 2015.3.3
            // 替换原生滚动条
            var view_scroll_obj = Scroll(self.$container,{
                lazyLoad : true,
                down_direction : 'down',
                down_direction_distance :50
            });


            self.view_scroll_obj = view_scroll_obj;

            // modify by hudw 2015.2.2
            self.view_scroll_obj.on('pullload',function(e)
            {
                if(self.has_next_page && !self.fetching)
                {
                    self.fetching = true;

                    self._load_more();
                }
                else
                {
                    self._drop_reset();
                }

            });

            self.view_scroll_obj.on('dropload',function(e)
            {
                self.refresh();

            });

           /* self.view_scroll_obj.on('scrollEndAfter', function(){
                console.log(this.y);
                console.log(this.maxScrollY);

                if(self.$load_more){
                    if(this.y <= this.maxScrollY){
                        self._load_more();
                    }
                }

            });*/
        },
        _drop_reset : function()
        {
            var self = this;

            self.view_scroll_obj && self.view_scroll_obj.resetload();
        },

        /**
         * 加载下一页数据
         * @private
         */
        _load_more : function() {
            var self = this;
            if(self.collection.get_state() == 'free') {
                var current_page = self.collection.get_current_page();
                self.search_data.page = ++current_page;
                self.collection.get_search_result_list(self.search_data);
            }
        },

        render : function()
        {
            var self = this;

            View.prototype.render.apply(self);

            self.trigger('render');

            return self;
        },

        /**
         * 设置搜索参数
         * @param data
         * @returns {search_view}
         */
        set_search_info : function(data){
            var self = this;
            //因为是渲染前设置所以设page为1
            data.page = 1
            self.search_data = data;
            return self;
        },
        refresh : function()
        {
            var self = this;

            self.$search_result_list['html']('');

            self.collection.get_search_result_list(self.search_data);
        },
        setup : function()
        {

            var self = this;

            self.$container = self.$('[data-role=container]'); // 滚动容器
            self.$search_result_list = self.$('[data-role=search-result-list]');
            self.$search_result = self.$('[data-role=search-result]');
            self.$load_more = self.$('[data-role=load-more]');




            // 安装事件
            self._setup_events();

            self._renderQueue = [];

            //self.view_scroll_obj.refresh();


        }

    })

    module.exports = search_view;
});