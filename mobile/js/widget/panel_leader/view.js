/**
 * Created by nolest on 2014/9/3.
 *
 *
 *
 *
 *
 *  介绍页'添加领队'模块
 */

define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var View = require('../../common/view');
    var utility = require('../../common/utility');
    var tpl = require('./tpl/main.handlebars');

    module.exports = View.extend
    ({
        attrs :
        {
            template : tpl
        },
        events :
        {


        },/*
         _parseElement : function()
         {
         var self = this;

         var template_model = self.get('templateModel');

         self.set('templateModel', template_model);

         View.prototype._parseElement.apply(self);

         },*/
        _setup_events : function()
        {

        },
        get_selected_obj : function()
        {
            var self = this;

            var name = self.$leader_name.val();

            var mobile = self.$leader_tel.val();


            var obj =
            {
                name : name,
                mobile : mobile
            };

            return obj
        },
        _set_leader_panel_count : function()
        {
            var self = this;

            self.$('[data-role="leader-close-panel"]').attr("leader_panel_count",self.leader_panel_count);

            self.set("leader_panel_count",self.leader_panel_count);

        },
        setup : function()
        {
            var self = this;

            self.$leader_name = self.$('[data-role="name-input"]');

            self.$leader_tel = self.$('[data-role="tel-input"]');

            self.leader_panel_count = self.get("leader_panel_count");

            self._set_leader_panel_count();
            // 安装事件
            self._setup_events();

        },
        show : function()
        {
            var self = this;

            self.$el.removeClass('fn-hide');
        },
        hide : function()
        {
            var self = this;

            self.$el.addClass('fn-hide');
        }
    });
});

