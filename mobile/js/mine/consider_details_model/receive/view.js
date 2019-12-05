/**
 * Created by nolest on 2014/9/10.
 */

define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../../../frame/page_control');
    var View = require('../../../common/view');
    var receive_tpl = require('./tpl/main.handlebars');
    var utility = require('../../../common/utility');
    var Scroll = require('../../../common/scroll');
    var App = require('../../../common/I_APP');

    var m_alert = require('../../../ui/m_alert/view');


    var receive_view = View.extend
    ({
        attrs:
        {
            template: receive_tpl
        },
        events :
        {
            'tap [data-role=page-back]' : function (ev)
            {
                page_control.navigate_to_page('mine/consider/can_back_to_mine')
            },
            'tap [data-rol="nav-to-cameraman"]' : function(ev)
            {
                var $cur_btn = $(ev.currentTarget);

                var user_id = $cur_btn.attr('data-user-id');

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
                    page_control.navigate_to_page('zone/'+user_id+'/cameraman');
                }


            }

        },
        _setup_events : function()
        {
            var self = this;

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

            self.$container = self.$('[data-role="yuepai-receive-container"]');

            self.templateModel = self.get("templateModel");

            self._setup_events();

            self._setup_scroll();

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
            return utility.get_view_port_height('nav') -24;
        }
    });

    module.exports = receive_view;
});