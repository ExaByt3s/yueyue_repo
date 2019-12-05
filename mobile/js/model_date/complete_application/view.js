/**
 * 约拍成功
 * hudw 2014.9.21
 */
define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var View = require('../../common/view');
    var complete_application = require('../complete_application/tpl/main.handlebars');
    var utility = require('../../common/utility');
    var m_alert = require('../../ui/m_alert/view');
    var Scroll = require('../../common/scroll');
    var info_tpl = require('../complete_application/tpl/info.handlebars');

    var complete_application_view = View.extend
    ({

        attrs:
        {
            template: complete_application
        },
        events :
        {
            'tap [data-role="nav-to-model"]' : function(ev)
            {
                var self = this;

                var $cur_btn = $(ev.currentTarget);

                var model_id = $cur_btn.attr('data-model-id');

                var date_id = $cur_btn.attr('data-date_id');

                page_control.navigate_to_page('mine/consider_details_camera/'+date_id);
            },
            'tap [data-role="page-back"]' : function()
            {
                page_control.back();
            }
        },

        /**
         * 安装事件
         * @private
         */
        _setup_events : function()
        {

            var self = this;

            utility.user.on('before:get_date_by_date_id:fetch',function(response,options)
            {
                m_alert.show('加载中...','loading');
            })
            .on('success:get_date_by_date_id:fetch',function(response,options)
            {
                m_alert.hide();

                self._render_info(response);
            })
            .on('error:get_date_by_date_id:fetch',function(response,options)
            {
                m_alert.hide();
            })
            .on('complete:get_date_by_date_id:fetch',function(response,options)
            {

            });

            self.on('update_info',function()
            {
                if(!self.view_scroll_obj)
                {
                    self._setup_scroll();

                    self.view_scroll_obj.refresh();

                    return;
                }

                self.view_scroll_obj.refresh();

            });

            /*self._setup_scroll();
            self.view_scroll_obj.refresh();*/
        },


        _render_info : function(response)
        {
            var self = this;

            var responses = response.result_data;

            var html_str = info_tpl(responses.data);

            self.$srcoll_wrapper.html(html_str);

            self.trigger('update_info');
        },
        /**
         * 安装滚动条
         * @private
         */
        _setup_scroll : function()
        {
            var self = this;


            var view_scroll_obj = Scroll(self.$container,
                {
                    is_hide_dropdown : true
                });

            self.view_scroll_obj = view_scroll_obj;
        },
        /**
         * 视图初始化入口
         */
        setup : function()
        {

            var self = this;

            // 配置交互对象
            self.$container = self.$('[data-role=container]'); // 滚动容器
            self.$srcoll_wrapper = self.$('[data-role="scroll_wrapper"]')

            self.date_id = self.get('date_id');

            // 安装事件
            self._setup_events();

            self.refresh(self.date_id);
        },
        render : function()
        {
            var self = this;

            // 调用渲染函数
            View.prototype.render.apply(self);

            self.trigger('render');

            return self;
        },
        refresh : function(date_id)
        {
            var self = this;

            utility.user.get_date_by_date_id(date_id);
        }

    });

    module.exports = complete_application_view;
});