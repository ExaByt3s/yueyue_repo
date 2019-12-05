/**
 * Created by nolest on 2014/9/28.
 *
 * 模特榜view
 */
define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../frame/page_control');
    var View = require('../common/view');
    var main_tpl = require('./tpl/main.handlebars');
    var one_two_three_tpl = require('./tpl/one-two-three.handlebars');
    var more_tpl = require('./tpl/more.handlebars');
    var utility = require('../common/utility');
    var Scroll = require('../common/new_iscroll');
    var m_alert = require('../ui/m_alert/view');

    var rank_view = View.extend
    ({
        attrs :
        {
            template : main_tpl
        },
        events :
        {

            'swiperight' : function ()
            {
                page_control.back();
            },

            'tap [data-role=page-back]' : function (ev)
            {
                page_control.back();
            },
            'tap [data-role="load_more"]' : function()
            {
                var self = this;

                self.get_more();
            },
            'tap [data-role="li-tap"]' : function(ev)
            {
                var self = this;

                page_control.navigate_to_page("model_card/" + $(ev.currentTarget).attr('data-id'));
            }
        },
        /**
         * 安装事件
         * @private
         */
        _setup_events : function()
        {
            var self = this;

            self.model
                .on('before:date:fetch',function()
                {
                    m_alert.show('加载中...','loading');
                })
                .on('success:date:fetch',function(response)
                {
                    m_alert.hide({delay:-1});

                    self.render_info(response);
                })
                .on('error:date:fetch',function()
                {
                    m_alert.show('获取信息失败','error',{delay:1000});
                })
                .on('complete:date:fetch',function()
                {
                    //m_alert.hide();
                });

            self.model
                .on('before:rank:fetch',function()
                {
                    m_alert.show('加载中...','loading');
                })
                .on('success:rank:fetch',function(response)
                {
                    m_alert.hide({delay:-1});
                })
                .on('error:rank:fetch',function()
                {
                    m_alert.show('获取信息失败','error',{delay:1000});
                })
                .on('complete:rank:fetch',function()
                {
                    //m_alert.hide();
                });

            self.once('render',function()
            {
                if(!self.view_scroll_obj)
                {
                    //主要滚动条
                    self._setup_scroll();

                    self.view_scroll_obj.refresh();

                    return;
                }

                self.view_scroll_obj.refresh();
            });
        },
        render_info : function(response)
        {
            var self = this;

            self.page_count++;

            if(response.result_data.has_next_page)
            {
                self.more_btn.removeClass("fn-hide");
            }
            if(!response.result_data.has_next_page)
            {
                self.more_btn.addClass("fn-hide");
            }

            var length = response.result_data.list.length;

            var list = response.result_data.list;
            //是否首次渲染
            if(self.is_first_load)
            {
                var list_insert = list.slice(0,3);

                var list_render = list.slice(3,length);
                //渲染前三个
                $.each(list_insert,function(i,obj)
                {
                    obj.is_date = self.get("templateModel").is_date;

                    obj.is_score = self.get("templateModel").is_score;

                    obj.index_count = i+1;

                    var one_html = one_two_three_tpl
                    ({
                        data : obj
                    });

                    self.$one_two_three.append(one_html)
                });
                //渲染更多
                $.each(list_render,function(i,obj)
                {
                    obj.is_date = self.get("templateModel").is_date;

                    obj.is_score = self.get("templateModel").is_score;

                    obj.index_count = self.last_index++;

                    var more_html = more_tpl
                    ({
                        data : obj
                    });

                    self.$more.append(more_html)
                });

                self.is_first_load = false;
            }
            else
            {   //渲染更多
                $.each(list,function(i,obj)
                {
                    obj.is_date = self.get("templateModel").is_date;

                    obj.is_score = self.get("templateModel").is_score;

                    obj.index_count = self.last_index++;

                    var more_html = more_tpl
                    ({
                        data : obj
                    });

                    self.$more.append(more_html)
                });

            }
            self.view_scroll_obj.refresh()

        },
        /**
         * 安装滚动条
         * @private
         */
        _setup_scroll : function()
        {
            var self = this;
            var view_scroll_obj = Scroll(self.$container);

            self.view_scroll_obj = view_scroll_obj;


        },
        /**
         * 视图初始化入口
         */
        setup : function()
        {
            var self = this;
            //安装事件
            self._setup_events();
            //滚动容器
            self.$container = self.$('[data-role="data-role=container"]');
            //前三
            self.$one_two_three = self.$('[data-role="one-two-three-list"]');
            //更多
            self.$more = self.$('[data-role="more-list"]');
            //更多按钮
            self.more_btn = self.$('[data-role="load_more"]');
            //首次加载标记
            self.is_first_load = true;
            //初始化更多序号
            self.last_index = 4;

            self.page_count = 1;

            self.model.get_rank({page : self.page_count})
        },
        render : function()
        {
            var self = this;

            // 调用渲染函数
            View.prototype.render.apply(self);

            self.trigger('render');

            return self;
        },
        get_more : function()
        {
            var self = this;

            self.model.get_rank(self.page_count);

        },
        /* 刷新方法
         *
         */
        refresh : function()
        {
            var self = this;

            self.$one_two_three.html("");

            self.$more.html("");

            self.last_index = 4;

            self.page_count = 1;

            self.model.get_rank({page : self.page_count});

            self.view_scroll_obj.scrollTo(0,0);

        }

    });

    module.exports = rank_view;
});