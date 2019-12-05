/**
 * Created by nolest on 2014/11/6.
 *
 * 信用金view
 */
define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var View = require('../../common/view');
    var credit_tpl = require('./tpl/main.handlebars');
    var utility = require('../../common/utility');
    var Scroll = require('../../common/scroll');
    var m_alert = require('../../ui/m_alert/view');
    var abnormal = require('../../widget/abnormal/view');

    var credit_view = View.extend
    ({

        attrs:
        {
            template: credit_tpl
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
            'tap [data-role="add-value-btn"]' : function()
            {
                page_control.navigate_to_page('mine/money/recharge')
            },
            'tap [data-role="get-available-money-btn"]' : function()
            {
                page_control.navigate_to_page('mine/money/withdrawal/0');
            }

        },
        _setup_events : function()
        {
            var self = this;

            self
                .on('render',function()
                {
                    self._setup_scroll();
                });
            self.model
                .on('before:fetch:get_bail_available_balance',function(response,options)
                {
                    m_alert.show('加载中...','loading');

                })
                .on('success:fetch:get_bail_available_balance',function(response,options)
                {
                    m_alert.hide();

                    var response = response.result_data;

                    var msg = response.msg;

                    if(response.code == 1)
                    {
                        self.$available_balance.html(response.data);
                    }
                    else
                    {
                        m_alert.show(msg,'error');
                    }
                })
                .on('error:fetch:get_bail_available_balance',function(response,options)
                {
                    m_alert.show('网络异常','error');
                })
        },
        _setup_scroll : function()
        {
            var self = this;

            var view_scroll_obj = Scroll(self.$container);

            self.$container.height(self.reset_viewport_height());

            self.view_scroll_obj = view_scroll_obj;
        },
        setup : function()
        {
            var self = this;

            self.$container = self.$('[data-role="container"]');

            self.$inside = self.$('[data-role="credit-inside"]');

            self.$available_balance = self.$('[data-role="available_balance"]');

            self._setup_events();

            self.refresh();

        },
        render : function()
        {
            var self = this;

            View.prototype.render.apply(self);

            self.trigger('render');

            return self;
        },
        reset_viewport_height : function()
        {
            return utility.get_view_port_height('nav');
        },
        refresh : function()
        {
            var self = this;

            self.model.get_bail_available_balance();
        }
    });

    module.exports = credit_view ;
});