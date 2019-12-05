define(function(require,exports,module){
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var View = require('../../common/view');
    var Scroll = require('../../common/scroll');
    var Utility = require('../../common/utility');
    var tip = require('../../ui/m_alert/view');
    var storage = window.localStorage;



    var main_tpl = require('./tpl/main.handlebars');
    var item_tpl = require('./tpl/item.handlebars');
    var search_history_tpl = require('./tpl/search_history.handlebars');


    var search_view = View.extend({
        attrs:{
            template:main_tpl
        },
        events:{
            'tap [data-role=cancel]' : function (ev)
            {
                page_control.back();
            },
            'tap [data-role=del-search-history-item]' : function (ev)
            {
                var self = this;

                var $target = $(ev.currentTarget);

                self.store.change(Utility.user.id,$target.attr('data-tag'),'del');

                if(self.store.get(Utility.user.id).length == 0){
                    self.del_search_history();
                    self.del_search_history_dom();
                }

                $target.parents("li").remove();
            },
            'tap [data-role=del-search-history]' : function(){
                var self = this;
                self.del_search_history();
                self.del_search_history_dom();
            },
            'tap [data-role=btn-back]' : function(){
                var self = this;
                page_control.back();
            },

            
            'tap [data-role=search]' : '_search',
            'tap [data-role=search-form]' : '_submit',

            'tap [data-role=change]' : '_change'
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
                    tip.show('查询中...', 'loading', {
                        delay: -1
                    });
                })
                .listenTo(self.collection, 'success:fetch', function(response, xhrOptions) {

                    if(response.result_data.length == 0){
                        tip.show('暂无数据', 'error', {
                            delay: 800
                        });
                    }else{
                        self._change();
                        self._render_item(response, xhrOptions);
                        tip.hide();
                    }

                })
                .listenTo(self.collection, 'error:fetch', function(xhr, status) {
                    tip.show('查询失败请返回重试', 'error', {
                        delay: 800
                    });
                })
                .listenTo(self.collection, 'complete:fetch', function(xhr, status) {
                });

            //搜索记录相关事件
//            self.$input.on('focus',function(){
//                self._render_item_search_history(self.store.get(Utility.user.id));
//            });
            self.$input.on('focus',function(){
                self._render_item_search_history(self.store.get(Utility.user.id));
            }).on('blur',function(ev){

                console.log(ev);

                //self.del_search_history_dom();
            });

            // 刷新数据
            self.refresh();


            // 视图更新
            self.on('updateList', function(response, xhrOptions) {

                // 第一次载入时iScroll未生成
                if (!self.view_scroll_obj) {
                    self._setup_scroll();
                    self._drop_reset();
                }

                self._drop_reset();
                self.view_scroll_obj.refresh();


            });
        },

        _render_item: function(response, xhrOptions) {
            var self = this;

            self.$security_list.html('');

            /**
             *  modify hudw 2014.12.7
             *  注意！！这里的renderQueue换成了读取response的list字段，这样修改的目的是解决了刷新页面
             *  导致self._renderQueue出错，但尚未知道修改后对换一换的操作有哪些影响。
             *
             */
            var renderQueue = response.result_data.list;

            //var renderQueue = self._renderQueue;

            var html_str = item_tpl({
                search_list: renderQueue
            });


            self.$security_list.html(html_str);
            self.trigger('updateList', response, xhrOptions);


            self.view_scroll_obj.refresh();
        },

        _render_item_search_history : function(search_history_queue){
            var self = this;

            if(!self.store.get(Utility.user.id)){
                return;
            }

            if(self.$search_history){
                return;
            }

            var html_str = search_history_tpl({
                search_historys : search_history_queue.slice(0,4)
            });



            var $html_str = $(html_str);

            self.$search_index.addClass('fn-hide');
            self.$container.append($html_str);

            //必须插入容器后再赋值，否则其他方法将无法在容器中找到此DOM
            self.$search_history = $html_str;



        },

        _reset: function() {
            var self = this;
            self.collection.each(self._addOne, self);
        },

        _addOne: function(dataModel) {
            var self = this;
            self.renderQueue.push(dataModel.toJSON());
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
                is_hide_dropdown : false
            });

            self.view_scroll_obj = view_scroll_obj;

            self.view_scroll_obj.on('dropload',function(e)
            {
                self.refresh();

            });

            //self.view_scroll_obj.refresh();
        },



        setup : function()
        {

            var self = this;

            self.$container = self.$('[data-role=container]'); // 滚动容器
            self.$security_list = self.$('[data-role=search-list]');//搜索标签列表
            self.$search_index = self.$('[data-role=search-index]');
            self.$change_btn_wrap = self.$('[data-role=change-btn-wrap]');//换一换

            self.$input = self.$('[data-role=input]');

            // 安装事件
            self._setup_events();

            self._renderQueue = [];
            self.renderQueue = [];
            self.start_setted = 0;

            //self.view_scroll_obj.refresh();


        },
        _submit : function(ev){
            var self = this;
            var search_info = {};
            search_info.id = '';
            search_info.tag = self.$input.val();
            if(!search_info.tag){
//                tip.show('请输入搜索内容！', 'error', {
//                    delay: 800
//                });
                return;
            }

            self.$input.blur();

            ev.preventDefault();
            ev.stopPropagation();

            self.store.change(Utility.user.id,search_info.tag,'add');


            page_control.navigate_to_page('search_result',search_info);

        },

        _search : function(ev){
            var self = this;
            var $target = $(ev.currentTarget)
            var search_info = {};

            search_info.id = $target.attr('data-id');
            search_info.tag = $target.attr('data-tag');

            self.store.change(Utility.user.id,search_info.tag,'add');

            page_control.navigate_to_page('search_result',search_info);

        },

        /**
         * 删除搜索历史记录
         */
        del_search_history : function(){
            var self = this;
            self.store.remove(Utility.user.id);
        },

        /**
         * 删除搜索历史记录DOM
         */
        del_search_history_dom : function(){
            var self = this;

            if(self.$search_history){
                self.$search_history.remove();
                self.$search_history = null;
                self.$search_index.removeClass('fn-hide');
            }

        },

        /**
         * 换一换
         * @private
         */
        _change : function(){
            var self = this;

            var show_len = 12;
            var settings_len = Math.ceil(self.renderQueue.length/show_len);

            if(settings_len <= show_len){
                self._renderQueue = self.renderQueue.slice(0,show_len);
                self.$change_btn_wrap.addClass('fn-hide');
                return
            }
            //构造开始下标数组
            var settings =[];
            for(var i = 0;i < settings_len; i++){
                settings.push(i * show_len);
            }

            //var start = settings[parseInt(Math.random()*(settings_len - 1 + 1 ) + 1)];

            var start = settings[self.start_setted];
            var end = (start == 0) ? (start + 1) * show_len : start * 2;

            self.$security_list.html('');
            self._renderQueue = self.renderQueue.slice(start,end);


            if(self.start_setted == (settings_len - 1)){
                self.start_setted = 0;
            }else{
                self.start_setted ++
            }

            self._render_item({},{});


        },

        /**
         * 本地储存
         */
        store: {
            /**
             * 前缀
             */
            prefix: 'poco-yuepai-app-storage-',

            /**
             * 设置
             * @param key
             * @param val
             * @returns {*}
             */
            set: function(key, val) {
                var self = this;
                if (val === undefined) {
                    return self.remove(key);
                }
                storage.setItem(self.prefix + key, JSON.stringify(val));
                return val;
            },
            /**
             * 获取
             * @param key
             * @returns {*}
             */
            get: function(key) {
                var self = this;
                var storage_get_item = storage.getItem(self.prefix + key);
                if(!storage_get_item){
                    return storage_get_item;
                }else{
                    return JSON.parse(storage_get_item);
                }
            },
            change : function(key,val,handle){
                var self = this;
                var old_storage =  self.get(key) || [];
                var new_storage = [], i;
                while (i = old_storage.shift()) {
                    if (i !== val) {
                        new_storage.push(i);
                    }
                }

                if(handle != 'del'){
                    new_storage.unshift(val);
                }


                self.set(key,new_storage);

            },
            /**
             * 删除
             * @param key
             * @returns {*}
             */
            remove: function(key) {
                var self = this;
                return storage.removeItem(self.prefix + key);
            }
        },
        refresh : function()
        {
            var self = this;

            self.collection.get_search_list(1);
        },
        _drop_reset : function()
        {
            var self = this;

            self.view_scroll_obj && self.view_scroll_obj.resetload();
        },

    })

    module.exports = search_view;
});