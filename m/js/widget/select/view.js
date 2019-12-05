/**
 * 下拉表单
 * hdw 2014.8.27
 */
define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var View = require('../../common/view');

    var utility = require('../../common/utility');
    var item_tpl = require('./tpl/select.handlebars');
    var options_tpl = require('./tpl/options.handlebars');

    module.exports = View.extend
    ({
        attrs :
        {
            template : item_tpl

        },
        events :
        {
            'change [data-role="select"]' : function()
            {
                var self = this;

            }
        },
        _parseElement : function()
        {
            var self = this;

            var template_model = self.get('templateModel');

            var defaulut_key = template_model.defaulut_key || false;

            console.log(defaulut_key)

            // 设置默认选中选

            if(template_model.options && template_model.options.length>0)
            {

                for(var i=0;i< template_model.options.length;i++)
                {

                    if(!defaulut_key)
                    {
                        template_model.options[0].selected = true;

                        console.log(template_model.options[i].value)

                        break;
                    }

                    if(defaulut_key == template_model.options[i].value)
                    {
                        template_model.options[i].selected = true;

                        break;
                    }
                    /*else if(defaulut_key)
                    {
                        template_model.options[0].selected = true;

                        console.log(defaulut_key,template_model.options[i].value)

                        break;
                    }*/


                }
            }

            self.set('templateModel', template_model);

            View.prototype._parseElement.apply(self);


        },
        _setup_events : function()
        {
            var self= this;

            self.$select_control.on('change',function(ev)
            {
                var $selected_options = self.$select_control.find('[data-role="options"]');

                var value = self.$select_control.val();
                var index = self.$select_control.get(0).selectedIndex;
                var text  = self.$select_control.find('[data-role="options"]').eq(index).text();
                var id  = self.$select_control.find('[data-role="options"]').eq(index).attr('data-id');

                $selected_options.removeAttr('selected');

                $selected_options.eq(index).attr('selected',true);

                var data =
                {
                    value : value,
                    text  : text,
                    index : index,
                    id    : id
                }

                self.set_value(data);

                console.log(data)

                self.trigger('change:options',data);

            });
        },
        set_value : function(data)
        {
            var self = this;

            self.selected_data = data
        },
        get_value : function()
        {
            var self = this;

            return self.selected_data;
        },
        get_default_value : function()
        {
            var self = this;

            var $selected_options = self.$select_control.find('[selected]');

            var id = $selected_options.attr('data-id');
            var value = $selected_options.val();
            var text = $selected_options.text();
            var index = self.$select_control.get(0).selectedIndex;

            var data =
            {
                value : value,
                text  : text,
                index : index,
                id    : id
            };

            self.set_value(data);

            return data;
        },
        set_options : function(data)
        {
            var self = this;

            var html_str = options_tpl(data);

            self.$select_control.html(html_str);

        },
        setup : function()
        {
            var self = this;

            var template_model = self.get('templateModel');

            self.$select_control = self.$el;

            // 安装事件
            self._setup_events();

            self.set_options(template_model);

            self.get_default_value();

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
