define(function(require,exports,module){
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var view = require('../../common/view');
    var utility = require('../../common/utility');
    var Dialog = require('../../ui/dialog/index');
    var tip = require('../../ui/m_alert/view');
    var Scroll = require('../../common/scroll');
    var abnormal = require('../../widget/abnormal/view');


    var mainTpl = require('./tpl/main.handlebars');
    var itemTpl = require('./tpl/item.handlebars');

    var qr_tpl = require('./tpl/big_qr_code.handlebars');

    var action_security_view = view.extend({
        attrs:{
            template:mainTpl
        },
        events:{
            'swiperight' : function (){
                page_control.back();
            },
            'tap [data-role=page-back]' : function (ev)
            {
                page_control.back();
            },
            'tap [data-role=pay]' : function (ev)
            {
                var self = this;
                self.$phone_number.val();
            },
            'tap [data-role=password]' : function (ev)
            {
                var self = this;

                var $cur_btn = $(ev.currentTarget);

                // 放大二维码数据整合
                var code = $cur_btn.attr('data-code');
                var qr_code = $cur_btn.attr('data-qr-code');
                var pic_w_h = Math.ceil(((utility.get_view_port_width() - 90)));//滚动外框宽高
                var out_height = pic_w_h + 35;

                // 数据结构
                var data =
                {
                    qr_code_arr :
                    [
                        {
                            code : code,
                            qr_code : qr_code,
                            pic_w_h : pic_w_h,
                            out_height : out_height
                        }
                    ],
                    pic_w_h : pic_w_h,
                    out_height : out_height
                };

                var html_str = qr_tpl(data);

                // 放大二维码弹出层
                var  dialog = new Dialog({
                    content : html_str
                }).show();

               //超过一个二维码才有滚动
               if(data.qr_code_arr.length > 1){

                   var pic_view_scroll_obj = Scroll(dialog.$('[data-role=qr]'),{
                        lazyLoad: true
                   });

                   pic_view_scroll_obj.refresh();

               }
            }
        },

        /**
         * 事件安装
         * @private
         */
        _setup_events: function() {
            var self = this;

//            self.listenTo(self.collection, 'all', function() {
//                // debug 用
//            });
            self.listenTo(self.collection, 'reset', self._reset)
                .listenTo(self.collection, 'add', self._addOne)
                .listenTo(self.collection, 'before:fetch', function() {
                    tip.show('查询中...', 'loading', {
                        delay: -1
                    });
            })
            .listenTo(self.collection, 'success:fetch', function(response, xhrOptions)
                {
                    self._renderItem(response, xhrOptions);

                    if(response.result_data.length == 0 && !self.abnormal_view)
                    {
                        self.$security_list.html('');

                        self.abnormal_view = new abnormal({
                            templateModel:
                            {
                                content_height : utility.get_view_port_height('nav') - 20 //为空的时候还有个20的padding
                            },
                            parentNode:self.$container
                        }).render();
                    }

                    tip.hide();

            })
            .listenTo(self.collection, 'error:fetch', function(xhr, status) {
                    /*tip.show('查询失败请返回重试', 'error', {
                        delay: 800
                    });*/
                    tip.show('网络异常', 'error');

                    self._drop_reset();
            })
            .listenTo(self.collection, 'complete:fetch', function(xhr, status) {
            });


            // 数据刷新
            self.refresh();

            // 视图更新
            self.on('updateList', function(response, xhrOptions) {
                // 后加载才有此项，新发留言为nul
                /*if (!!response) {
                    if (!response.data.hasMore) {
                        self._hideLoadMorBar();
                    }
                }*/

                // 第一次载入时iScroll未生成
                if (!self.view_scroll_obj) {
                    self._setup_scroll();
                    self._drop_reset();
                }

                self._drop_reset();
                self.view_scroll_obj.refresh();
            });
        },

        _renderItem: function(response, xhrOptions) {
            var self = this;

            /*if (xhrOptions.reset === true) {
                method = 'html';
                if(!response.data.list[0]){
                    self.$loadMoreBar.addClass('fn-hide');
                    self.$commentList.removeClass('comment-stream').addClass('stream-empty').html('<p>暂无评论</p>');
                    return;
                }
            }*/

            var renderQueue = self._renderQueue;
            console.log(renderQueue);
            var htmlStr = itemTpl({
                securitys: renderQueue
            });



            self.$security_list.html(htmlStr);
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
                lazyLoad: true,
                is_hide_dropdown : false
            });

            self.view_scroll_obj = view_scroll_obj;

            self.view_scroll_obj.on('dropload',function(e)
            {
                self.refresh();

            });

            //self.view_scroll_obj.refresh();
        },
        _drop_reset : function()
        {
            var self = this;

            self.view_scroll_obj && self.view_scroll_obj.resetload();
        },

        setup : function()
        {

            var self = this;

            self._renderQueue = [];

            self.$container = self.$('[data-role=container]'); // 滚动容器
            self.$security_list = self.$('[data-role=action-security-list]');
            self.$password = self.$('[data-role=password]');


            // 安装事件
            self._setup_events();


        },
        refresh : function()
        {
            var self = this;

            self.collection.get_securitys();
        }

    })

    module.exports = action_security_view;
});