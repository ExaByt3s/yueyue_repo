/**
 * Created by nolestLam on 2015/3/6.
 */

define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var View = require('../../common/view');

    var utility = require('../../common/utility');
    var m_alert = require('../../ui/m_alert/view');
    var item_tpl = require('./tpl/coupon.handlebars');


    module.exports = View.extend
    ({
        attrs :
        {
            template : item_tpl
        },
        events :
        {

        },
        _parseElement : function()
        {
            var self = this;

            var template_model = self.get('templateModel');

            var real_data = self._add_status(template_model);

            self.set('templateModel', real_data);

            View.prototype._parseElement.apply(self);
        },
        _setup_events : function()
        {

        },
        _add_status : function(data)
        {
            var self = this;

            $.each(data.data,function(i,obj)
            {
                console.log(obj.tab);
                if(obj.tab == 'available')
                {
                    data.data[i] = $.extend(true,{},obj,{_class_for_available:true})
                }
                else if (obj.tab == 'used'){
                    data.data[i] = $.extend(true,{},obj,{_class_for_used:true});
                }
                else if (obj.tab == 'expired') {
                    data.data[i] = $.extend(true,{},obj,{_class_for_expired:true});
                }
            });

            return data
        },
        setup : function()
        {
            var self = this;

            /*
            self.min_value = utility.int(self.get('min_value'));
            self.max_value = utility.int(self.get('max_value'));
            self.step = utility.int(self.get('step'));
            self.disable = self.get('disable');
            self.value = utility.int(self.get('value'));
            self.is_floor = self.get('is_floor');

            // 设置加减按钮
            self.$add_btn = self.$('[data-role=add]');
            self.$minus = self.$('[data-role=minus]');
            self.$number_input = self.$('[data-role=input-num-btn]');

            */
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
        },
        /**
         * 格式化数字
         * @returns Float
         */
        to_number :function (s) {
            return parseFloat(s, 10) || 0;
        }

    });
});
