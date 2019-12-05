/**
 * Created by nolest on 2014/8/30.
 *
 *
 *活动列表 - 列表子元素
 */
define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var View = require('../../common/view');
    var utility = require('../../common/utility');
    var item_tpl = require('./tpl/act_list.handlebars');
    var templateHelpers = require('../../common/template-helpers');

    module.exports = View.extend
    ({
        attrs :
        {
            template : item_tpl
        },
        events :
        {
            /*
            'tap' : function()
            {
                var self = this;

                page_control.navigate_to_page('act/detail/'+ self.model.event_id);

            }
            */
        },
        setup : function()
        {
            var self = this;

            self.model.event_id = self.model.event_id;

        },
        _parseElement : function()
        {
            var self = this;

            var template_model = self.get('templateModel');

            //已结束活动 显示遮罩层 nolest 2015-2-6
            if(template_model.event_status == '2' || template_model.event_status == 2)
            {
                template_model = $.extend(true,{},template_model,{act_is_finish:1});
            }
            console.log(template_model)
            self.set('templateModel', template_model);

            View.prototype._parseElement.apply(self);
        },
        _setup_events : function()
        {
            var self= this;

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
        },
        list : function()
        {
            var self = this;

            return self.$el;
        }

    });
});
