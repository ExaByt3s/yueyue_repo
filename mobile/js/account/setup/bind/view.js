/**
 * Created by nolest on 2014/9/13.
 */

define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../../../frame/page_control');
    var View = require('../../../common/view');
    var bind_tpl = require('./tpl/main.handlebars');
    var utility = require('../../../common/utility');
    var Scroll = require('../../../common/scroll');

    var bind_view = View.extend
    ({

        attrs:
        {
            template: bind_tpl
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
            'tap [data-role="change-phone"]' : function()
            {
                page_control.navigate_to_page("account/setup/bind/enter_phone/form_setup")
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
                .on('success:fetch_sms',function(response,options)
                {

                })
                .on('complete:fetch_sms',function(response,options)
                {

                })
                .on('success:fetch_submit',function(response,options)
                {

                })
                .on('complete:fetch_submit',function(response,options)
                {

                })
        },
        _setup_scroll : function()
        {
            var self = this;

            var view_scroll_obj = Scroll(self.$container);

            self.view_scroll_obj = view_scroll_obj;
        },
        setup : function()
        {
            var self = this;

            self.$container = self.$('[data-role="container"]');

            self.$phone_num = self.$('[data-role="phone_num"]');

            self.$phone_num.html("已绑定的手机号：" + utility.user.get("phone"));

            self._setup_events();
        },
        render : function()
        {
            var self = this;

            View.prototype.render.apply(self);

            self.trigger('render');

            return self;
        }
    });

    module.exports = bind_view;
});