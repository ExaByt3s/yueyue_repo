/**
 * 数字表单按钮
 * hdw 2014.8.26
 */
define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var View = require('../../common/view');

    var utility = require('../../common/utility');
    var m_alert = require('../../ui/m_alert/view');
    var item_tpl = require('./tpl/number_btn.handlebars');


    module.exports = View.extend
    ({
        attrs :
        {
            template : item_tpl,
            min_value : 0,
            max_value : 999999999999,
            step : 100,
            disable : false
        },
        events :
        {
            'tap [data-role=add]' : function()
            {
                var self = this;

                self.add_num();
            },
            'tap [data-role=minus]' : function()
            {
                var self = this;

                self.minus_num();
            }
        },
        _parseElement : function()
        {
            var self = this;

            var template_model = self.get('templateModel');

            self.set('templateModel', template_model);

            View.prototype._parseElement.apply(self);
        },
        _setup_events : function()
        {
            var self= this;
            //获取焦点取整
            self.$('[data-role="input-num-btn"]').on('focus',function(){
                self._value = self.$number_input.val();
            })
            //失焦取整
            self.$('[data-role="input-num-btn"]').on('change blur',function()
            {
                var value = self.to_number(self.$number_input.val());
                if(value<self.min_value){
                    m_alert.show('所填数字不得小于' + self.min_value,'error');
                    self.$number_input.val(self._value);
                    return
                }else if(value>self.max_value){
                    m_alert.show('所填数字不得大于' + self.max_value,'error');
                    self.$number_input.val(self._value);

                }
                if(self.is_floor)
                {
                    self.$number_input.val(Math.floor(value));
                }
            });
        },
        add_num : function()
        {
            var self = this;

            if(self.get('disable'))
            {
                return;
            }

            var step = self.step;

            var cur_value = utility.int(self.$number_input.val());

            cur_value = cur_value+step;

            if(cur_value>self.max_value)
            {
                m_alert.show('所填数字不得大于' + self.max_value,'error');

                return;
            }

            self.set_vaule(cur_value);

            self.trigger('add_value:success',cur_value);
        },
        minus_num : function()
        {
            var self = this;

            if(self.get('disable'))
            {
                return;
            }

            var step = self.step;

            var cur_value = utility.int(self.$number_input.val());

            cur_value = cur_value-step;

            if(cur_value<self.min_value)
            {
                m_alert.show('所填数字不得小于' + self.min_value,'error');
                return;
            }

            self.set_vaule(cur_value);

            self.trigger('minus:success',cur_value);
        },
        set_vaule : function(num)
        {
            var self = this;

            self.$number_input && self.$number_input.val(utility.int(num));
        },
        get_value : function()
        {
            var self = this;

            return self.$number_input.val()
        },
        setup : function()
        {
            var self = this;

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

            // 安装事件
            self._setup_events();

            // 初始化参数
            self.set_vaule(self.value);

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
