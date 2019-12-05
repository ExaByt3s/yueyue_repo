define(function(require,exports,module){
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var view = require('../../common/view');
    var scroll = require('../../common/scroll');
    var utility = require('../../common/utility');
    var tip = require('../../ui/m_alert/view');
    var templateHelpers = require('../../common/template-helpers');
    var App = require('../../common/I_APP');


    var main_tpl = require('./tpl/main.handlebars');
    var item_tpl = require('./tpl/item.handlebars');

    var action_signin_view = view.extend({
        attrs : {
            template : main_tpl
        },
        events : {
            'tap [data-role=page-back]' : function (ev)
            {
                var self = this;

                // 支付完成后跳转到这里
                if(self.get('return_home'))
                {
                    if(App.isPaiApp)
                    {
                        App.switchtopage({page:'mine'});
                    }
                    else
                    {
                        page_control.navigate_to_page('act/list');
                    }

                }
                else
                {
                    if(App.isPaiApp)
                    {
                        App.app_back();
                    }
                    else
                    {
                        page_control.back();
                    }

                }


            },
            'tap [data-role=follow]' : function (ev)
            {
                var self = this;
                var $target = $(ev.currentTarget);
                var follow_act = self.toNumber($target.attr('data-follow'));

                $target.attr
                ({
                    'data-follow' : follow_act ? 0 : 1
                });
                $target.toggleClass('followed',follow_act);

                self.model.set('is_follow',follow_act);

                self.model.relation(follow_act);
            },
            'tap [data-role=dropdown]' : function (ev)
            {

                var self = this;
                var $target = $(ev.currentTarget);
                $target.toggleClass('open').next().toggleClass('fn-hide');

                var icon_obj = $target.toggleClass('open').find('.icon');

                if(icon_obj.hasClass("icon-new-sign-arr-down"))
                {
                    icon_obj.removeClass("icon-new-sign-arr-down");
                    icon_obj.addClass("icon-new-sign-arr-up");

                }
                else
                {
                    icon_obj.removeClass("icon-new-sign-arr-up");
                    icon_obj.addClass("icon-new-sign-arr-down");
                }

                self.view_scroll_obj.refresh();

            },
            'tap [data-role="item-dropdown"]' : function(ev)
            {
                var self = this;
                var $target = $(ev.currentTarget);
                $target.toggleClass('open').next().toggleClass('fn-hide');

                var icon_obj = $target.toggleClass('open').find('.icon');

                if(icon_obj.hasClass("icon-new-sign-arr-down"))
                {
                    icon_obj.removeClass("icon-new-sign-arr-down");
                    icon_obj.addClass("icon-new-sign-arr-up");

                }
                else
                {
                    icon_obj.removeClass("icon-new-sign-arr-up");
                    icon_obj.addClass("icon-new-sign-arr-down");
                }

                self.view_scroll_obj.refresh();
            },
            'tap [data-role="sign-scan"]' : function(ev)
            {
                console.log("扫一扫");
                if(App.isPaiApp)
                {
                    App.qrcodescan
                    ({
                        success : function(data)
                        {
                            console.log(data.type);

                            page_control.navigate_to_page(data.type);
                        }
                    });

                }
            },
            'tap [data-role=navigate]' : function (ev)
            {
                var self = this;
                var $target = $(ev.currentTarget)
                var target = $target.attr('data-target');
                var role = $target.attr('data-user-role');
                switch (target)
                {
                    case 'zone' :
                        var user_id = $target.attr('data-uid');
                        if(role == 'cameraman')
                        {
                            if(App.isPaiApp)
                            {
                                App.nav_to_app_page
                                ({
                                    page_type : 'cameraman_card',
                                    user_id : user_id
                                });
                            }
                            else
                            {
                                page_control.navigate_to_page('zone/' + user_id+'/'+role);
                            }

                        }
                        else if(role == 'model')
                        {
                            if(App.isPaiApp)
                            {
                                App.nav_to_app_page
                                ({
                                    page_type : 'model_card',
                                    user_id : user_id
                                });
                            }
                            else
                            {
                                page_control.navigate_to_page('model_card/' + user_id);
                            }

                        }
                        else
                        {
                            tip.show('该用户尚未开通个人空间','error');
                        }

                        break;
                    case 'apply' :

                        var loc = window.location;

                        var redirect_url = loc.origin+"/m/"+window._page_mode+loc.search+"#act/detail/"+self.model.get("event_id");

                        window._force_jump_link = redirect_url;

                        if(!utility.auth.is_login())
                        {
                            if(App.isPaiApp)
                            {
                                App.openloginpage(function(data)
                                {
                                    if(data.code == '0000')
                                    {
                                        utility.refresh_page();
                                    }
                                });
                            }
                            else
                            {
                                page_control.navigate_to_page('account/login');
                            }

                            return;
                        }



                        page_control.navigate_to_page('act/apply/' + self.model.get('event_id'),self.model);
                        break;
                    case 'comment' :
                        var comment_id = $target.attr('data-uid');
                        //page_control.navigate_to_page('comment/' + comment_id);
                        break;
                }

            },
            'tap [data-role="next"]' : function()
            {
                if(App.isPaiApp)
                {
                    App.switchtopage({page:'mine'});
                }
                else
                {
                    page_control.navigate_to_page('mine');
                }

            },
            'tap [data-role="notice-exp"]' : function()
            {
                console.log("聊天")
            },
            'tap [data-role="call-phone"]' : function(ev)
            {
                console.log("打电话");
                var self = this;
                var $target = $(ev.currentTarget);
                var phone = $target.attr('data-phone');

                if(App.isPaiApp)
                {
                    App.call_phone({number:phone});
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
                    tip.show('加载中...', 'loading', {
                        delay: -1
                    });
                })
                .listenTo(self.collection, 'success:fetch', function(response, xhrOptions) {

                    console.log(response);

                    self._render_item(response, xhrOptions);
                    self._render_follow(response.result_data.is_follow);


                    // modify by hudw 2015.2.10
                    if(response.result_data && response.result_data.event_status == 2)
                    {
                        self.$('[data-role="join-in"]').addClass('fn-hide');
                    }
                    else
                    {
                        self.$('[data-role="join-in"]').removeClass('fn-hide');
                    }


                    tip.hide();
                })
                .listenTo(self.collection, 'error:fetch', function(response, xhrOptions) {
                    tip.show('查询失败请返回重试', 'error', {
                        delay: 800
                    });
                })
                .listenTo(self.collection, 'complete:fetch', function(xhr, status) {
                }).listenTo(self.model, 'before:relation', self._before_relation)
                .listenTo(self.model, 'success:relation', self._success_relation)
                .listenTo(self.model, 'error:relation', self._error_relation)
                .listenTo(self.model, 'complete:relation',self._complete_relation)

            //获得报名网友列表
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

                    return;
                }


                self.view_scroll_obj.refresh();
                self.view_scroll_obj.reset_top();
                self._drop_reset();

            });
        },

        _set_event_id : function(event_id){
            var self = this;
            self.event_id = event_id;
            return self;
        },

        /**
         * 渲染模板
         * @param response
         * @param xhrOptions
         * @private
         */
        _render_item : function(response, xhrOptions) {
            var self = this;
            var render_queue = self._render_queue;

            console.log(response)
            console.log(render_queue);
            //设置图片宽高
            //render_queue.enroll_arr.max_size = (utility.get_view_port_width() - 75) / 4;

            var html_str = item_tpl({
                games : render_queue,
                data : render_queue,
                //max_size : parseInt(((utility.get_view_port_width() - 105) / 4))取整数
                max_size : Math.ceil(((utility.get_view_port_width() - 105) / 4))//向下取整
            },
                {
                    helpers : {if_equal:templateHelpers.if_equal}
                });


            self.$('[data-role="event_title"]').html(response.result_data.event_title);

            self.$signin_list.html(html_str);
            self.trigger('updateList', response, xhrOptions);

            self._render_queue = [];
            //组织者出现扫码按钮
            if(utility.user.get("user_id") == response.result_data.event_organizers)
            {
                self.$('[data-role="sign-scan"]').removeClass("fn-hide");

                //隐藏底部栏
                self.$('[data-role="footer"]').addClass("fn-hide");
            }
            self.view_scroll_obj.refresh();
        },
        _render_follow : function(is_follow)
        {
            var self = this;

            var class_name = is_follow ? 'followed' : ''

            self.$('[data-role="follow"]').addClass(class_name);
        },

        _reset: function() {
            var self = this;

            self.collection.each(self._addOne, self);
        },
        _drop_reset : function()
        {
            var self = this;

            self.view_scroll_obj && self.view_scroll_obj.resetload();
        },
        _addOne: function(dataModel) {
            var self = this;
            self._render_queue.push(dataModel.toJSON());
            return self;
        },

        _error_relation : function(){
            tip.show('操作失败请重试！', 'error',
                {
                    delay: 800
                });
        },

        /**
         * 安装滚动条
         * @private
         */

        _setup_scroll : function()
        {
            var self = this;
            var view_scroll_obj = scroll(self.$container,{
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


        setup : function()
        {

            var self = this;

            self._render_queue = [];

            self.$container = self.$('[data-role=container]'); // 滚动容器
            self.$signin_list = self.$('[data-role=action-signin-list]');

            // 安装事件
            self._setup_events();




        },

        /**
         * 格式化数字
         * @returns Float
         */
        toNumber : function (s)
        {
            console.log(s);

            console.log(parseFloat(s, 10))
        return parseFloat(s, 10) || 0;

        },
        refresh : function()
        {
            var self = this;

            self.collection.get_games(1,self.model.get('event_id'));
        }

    })

    module.exports = action_signin_view;
});