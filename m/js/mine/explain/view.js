/**
 * Created by nolest on 2015/1/6.
 */

define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var View = require('../../common/view');
    var explain_tpl = require('./tpl/main.handlebars');
    var utility = require('../../common/utility');
    var m_alert = require('../../ui/m_alert/view');
    var global_config = require('../../common/global_config');
    var Scroll = require('../../common/scroll');

    var explain_view = View.extend
    ({
        attrs:
        {
            template: explain_tpl
        },
        events :
        {
            'swiperight' : function ()
            {
                if(window._IS_NEW_WEB_VIEW)
                {
                    return;
                }

                page_control.back();
            },
            'tap [data-role=page-back]' : function (ev)
            {
                if(window._IS_NEW_WEB_VIEW)
                {
                    App.remove_front();

                    return;
                }

                var self = this;

                page_control.back();

            }
        },
        _setup_events : function()
        {

        },
        setup : function()
        {
            var self = this;

            self.$container = self.$('[data-role="container"]');

            var view_scroll_obj = Scroll(self.$container,
                {
                    is_hide_dropdown : true
                });

            self.view_scroll_obj = view_scroll_obj;

            // modify by hudw 2015.1.11
            // 有关介绍的内容最好读取服务器
            utility.ajax_request
            ({
                url : global_config.ajax_url.get_intro_text,
                cache : true,
                data :
                {
                    type : self.get('type')
                },
                beforeSend : function()
                {
                    m_alert.show('加载中','loading',{delay:-1});
                },
                success : function(res)
                {
                    m_alert.hide();

                    if(res.result_data)
                    {
                        self.$container.html(res.result_data.data);
                    }

                },
                error : function()
                {
                    m_alert.show('网络异常','error');
                }
            });
        },
        render : function()
        {
            var self = this;

            View.prototype.render.apply(self);

            self.trigger('render');

            return self;
        }
    });

    module.exports = explain_view;
});