define(function(require,exports,module){
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var view = require('../../common/view');
    var Utility = require('../../common/utility');
    var m_alert = require('../../ui/m_alert/view');



    var mainTpl = require('./tpl/main.handlebars');

    var model_style_card_view = view.extend({
        attrs:{
            template:mainTpl
        },
        events:{
            'tap [data-role=to-select-style]' : function(ev){
            }
        },

        _setup_events:function(){
            var self = this;

            //获取焦点取整
            self.$('[data-input="input"]').on('focus',function(ev){
                var $target = $(ev.currentTarget);
                self._value = $target.val();
            })
            //失焦取整
            //self.$('[data-input="input"]').on('change blur',function(ev)
            self.$('[data-input="input"]').on('change',function(ev)
            {
                var $target = $(ev.currentTarget);
                var value = self.to_number($target.val());
                console.log($target);
                console.log(value);
                //没填时不提示
                if(value || value == 0){
                    if(value<self.min_value){
                        m_alert.show('所填数字不得小于' + self.min_value,'error');
                        $target.val(self._value);
                    }else if(value>self.max_value){
                        m_alert.show('所填数字不得大于' + self.max_value,'error');
                        $target.val(self._value);
                    }
                }
            });

        },

        setup : function()
        {

            var self = this;

            self.$container = self.$('[data-role=container]'); // 滚动容器
            self.$model_style_continue_price = self.$('[data-role=model-style-continue-price]'); // 续钟价格
            self.$model_style_price = self.$('[data-role=model-style-price]'); // 价格
            self.$model_style = self.$('[data-role=model-style]'); // 风格
            self.$model_style_hour = self.$('[data-role=model-style-hour]'); // 小时


            console.log(self.get('templateModel'));

            self.min_value = 10;
            self.max_value = 10000;


            // 安装事件
            self._setup_events();

            //self.view_scroll_obj.refresh();


        },

        get_value : function(){
            var self = this;
            var all_value = {};
            all_value.has_empty_value = false;

            all_value.continue_price = self.$model_style_continue_price.val();
            all_value.price = self.$model_style_price.val();
            all_value.style = self.$model_style.html();
            all_value.hour = self.$model_style_hour.html();

            if(all_value.price == '' || all_value.style == '' || all_value.hour == '' || all_value.style == ''){
                all_value.has_empty_value = true;
            }


            return all_value;


        },

        /**
         * 销毁
         * @returns {abnormal}
         */
        destroy: function() {
            var self = this;

            view.prototype.remove.call(self);
            view.prototype.destroy.call(self);

            return self;
        },
        render : function(type)
        {
            var self = this;

            // 调用渲染函数
            view.prototype.render.apply(self,[type]);

            self.trigger('render');

            return self;
        },
        /**
         * 格式化数字
         * @returns Float
         */
        to_number : function(s) {
            return parseFloat(s, 10) || 0;
        }

    })

    module.exports = model_style_card_view;
});