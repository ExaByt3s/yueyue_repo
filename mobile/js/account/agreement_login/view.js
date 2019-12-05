/**
 * Created by nolest on 2014/9/13.
 */

define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var View = require('../../common/view');
    var agreement_login_tpl = require('./tpl/main.handlebars');
    var utility = require('../../common/utility');
    var Scroll = require('../../common/scroll');
    var global_config = require('../../common/global_config');
    var m_alert = require('../../ui/m_alert/view');

    var agreement_login_view = View.extend
    ({

        attrs:
        {
            template: agreement_login_tpl
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
            }

        },
        _setup_events : function()
        {
            var self = this;

            self.on('render',function()
            {
                self._setup_scroll();
            });


        },
        _setup_scroll : function()
        {
            var self = this;

            var view_scroll_obj = Scroll(self.$container,
                {
                    is_hide_dropdown : true
                });

            self.view_scroll_obj = view_scroll_obj;

            self.view_scroll_obj.refresh();


        },
        setup : function()
        {
            var self = this;

            self.$container = self.$('[data-role="container"]');

            self.$container.height(utility.get_view_port_height()-45);

            self._setup_events();

            // modify by hudw 2015.1.11
            // 有关介绍的内容最好读取服务器
            utility.ajax_request
            ({
                url : global_config.ajax_url.get_intro_text,
                cache : true,
                data :
                {
                    type : 'agreement'
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
                        self.$('[data-role="text"]').html(res.result_data.data);
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

    module.exports = agreement_login_view;
});