/**
 * 主题列表视图
 *
 * hdw 2014.10.27
 */
define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var View = require('../../common/view');
    var topic_list_tpl = require('../tpl/list.handlebars');
    var main_tpl = require('../tpl/main.handlebars');

    var utility = require('../../common/utility');
    var Scroll = require('../../common/scroll');
    var m_alert = require('../../ui/m_alert/view');

    var topic_list_view = View.extend
    ({
        attrs :
        {
            template : main_tpl
        },
        className : 'topic-list-scroll-wrapper',
        events :
        {
            'tap [data-role=page-back]' : function (ev)
            {
                page_control.back();
            },
            'tap [data-nav-page]' : function(ev)
            {
                var $cur_btn = $(ev.currentTarget);

                var address = $cur_btn.attr('data-address');
                var link_type = $cur_btn.attr('data-link_type');

                switch (link_type)
                {
                    case 'inside':
                        if(address)
                        {
                            page_control.navigate_to_page(address);
                        }
                        break;
                    case 'outside':
                        if(address)
                        {
                            windw.location.href = address;
                        }
                        break;
                    case 'other':
                        break;
                }
            },
            'tap [data-role="nav-to-info"]' : function(ev)
            {
                var self = this;

                var $cur_btn = $(ev.currentTarget);

                var topic_id = $cur_btn.attr('data-topic-id');

                if(topic_id)
                {
                    page_control.navigate_to_page('topic/'+topic_id);
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

            self.collection.on('before:list:fetch',function(response)
            {
                //m_alert.show('加载中...','loading',{delay:-1});
            }).on('success:list:fetch',function(response)
            {
                m_alert.hide();

                self._render_topic_list(response);
            }).on('error:list:fetch',function(response)
            {
                m_alert.hide();
            });

            self.on('update_list',function()
            {
                if(!self.$scroll_view_obj)
                {
                    self._setup_scroll();

                    self.$scroll_view_obj.resetLazyLoad();
                    self.$scroll_view_obj.reset_top();
                    self.$scroll_view_obj.refresh();


                    return;
                }


                self.$scroll_view_obj.resetLazyLoad();
                self.$scroll_view_obj.reset_top();
                self.$scroll_view_obj.refresh();
            });

            self.once('render',self._render_after,self);
        },
        _render_after : function()
        {
            var self = this;
        },
        _render_topic_list : function(data)
        {
            var self = this;

            var list = data.result_data.list;

            var html_str = topic_list_tpl
            ({
                list : list
            });
			
            // 根据reset判断是否分页
            var method = self.collection.reset ? 'html' : 'append';					

            self.$('[data-role="list-container"]')[method](html_str);

			if(self.get('route_params_arr')[0])
			{
				self.$('[data-role="list-container"]').find('.img').css('height','320px');													
			}


            self._drop_reset();

            self.trigger('update_list');


        },
        _setup_scroll : function()
        {
            var self = this;
            var view_scroll_obj = Scroll(self.$('[data-role="scroll_content"]'),
                {
                    is_hide_dropdown : false,
                    lazyLoad : true
                });
            self.$scroll_view_obj = view_scroll_obj;

            self.$scroll_view_obj.on('dropload',function(e)
            {
                self.refresh();


            });
        },
        _drop_reset : function()
        {
            var self = this;

            self.$scroll_view_obj && self.$scroll_view_obj.resetload();
        },
        /**
         * 视图初始化入口
         */
        setup : function()
        {
            var self = this;

            //滚动容器
            self.$container = self.$el;

            //安装事件
            self._setup_events();

            self.$('[data-role="scroll_content"]').height(self.reset_viewport_height());
            self.$('[data-role="list-container"]').height(self.reset_viewport_height());

        },
        render : function()
        {
            var self = this;

            // 调用渲染函数
            View.prototype.render.apply(self);

            self.trigger('render');

            return self;
        },
        get_more : function(page)
        {
            var self = this;

            self.collection.get_list(page,{issue_id : self.get('route_params_arr')[0] || 0});
        },
        /* 刷新方法
         *
         */
        refresh : function()
        {
            var self = this;

            self.collection.get_list(1,{issue_id : self.get('route_params_arr')[0] || 0});

        },
        reset_viewport_height : function()
        {
            return utility.get_view_port_height('nav');
        }

    });

    module.exports = topic_list_view;
});