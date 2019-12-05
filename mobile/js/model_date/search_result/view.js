define(function(require,exports,module){
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var View = require('../../common/view');
    var Scroll = require('../../common/new_iscroll');
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
                    self.$load_more.html('查询中...');
                })
                .listenTo(self.collection, 'success:fetch', function(response, xhrOptions) {

                    if(response.result_data.empty){
                        self.abnormal_view = new abnormal({
                            templateModel :
                            {
                                content_height : Utility.get_view_port_height('all') - 35
                            },
                            parentNode:self.$search_result_list
                        }).render();
                    }else{
                        self._render_item(response, xhrOptions);
                        self.$load_more.html('上拉加载更多');
                        self.view_scroll_obj.scrollTo(0,0);
                    }

                    //没有更多数据去除可以加载更多提示
                    if (!response.result_data.has_next_page) {
                        self.$load_more.remove();
                        self.$load_more = null;
                    }

                    tip.hide();

                })
                .listenTo(self.collection, 'error:fetch', function(xhr, status) {
                    tip.show('查询失败请返回重试', 'error', {
                        delay: 800
                    });
                })
                .listenTo(self.collection, 'complete:fetch', function(xhr, status) {
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

            var renderQueue = self._renderQueue;
            var htmlStr = item_tpl({
                search_list: renderQueue
            });


            var method = xhrOptions.reset ? 'html' : 'append';
            self.$search_result_list[method](htmlStr);
            self.trigger('updateList', response, xhrOptions);

            self._renderQueue = [];

            self.view_scroll_obj.refresh();
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
            var view_scroll_obj = Scroll(self.$container,{
                lazyLoad : true
            });

            self.view_scroll_obj = view_scroll_obj;

            self.view_scroll_obj.on('scrollEndAfter', function(){
                console.log(this.y);
                console.log(this.maxScrollY);

                if(self.$load_more){
                    if(this.y <= this.maxScrollY){
                        self._load_more();
                    }
                }

            });
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