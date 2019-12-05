define(function(require,exports,module){
    var $ = require('$');
    var page_control = require('../frame/page_control');
    var view = require('../common/view');
    var scroll = require('../common/scroll');
    var utility = require('../common/utility');
    var tip = require('../ui/m_alert/view');
    var templateHelpers = require('../common/template-helpers');


    var tpl = require('./tpl/main.handlebars');

    var edit_view = view.extend
    ({
        attrs:
        {
            template:tpl
        },
        templateHelpers :
        {
            if_equal : templateHelpers.if_equal
        },
        /**
         * 事件
         */
        events:
        {
            'tap [data-role=back]' : '_back',
            'tap [data-role=save]' : '_save'
        },


        _back: function()
        {
            page_control.back();
        },


        _save: function()
        {
            var self = this;

            var inputterVal = self.inputter.val();

            var inputterTrimVal = $.trim(inputterVal);

            if (!inputterTrimVal && !self.get('is_empty'))
            {
                tip.show('请输入内容', 'error',
                {
                    delay: 1000
                });
                return;
            }

            if(self.get('edit_obj'))
            {
                var input_type = self.get('edit_obj').prop('tagName');

                switch (input_type)
                {
                    case 'DIV':
                        self.get('edit_obj').html(inputterVal);
                        break
                    case 'INPUT':
                    case 'TEXTAREA':
                        self.get('edit_obj').val(inputterVal);
                        break
                }

                page_control.back();
            }

        },

        /**
         * 光标定到文字最后
         * @private
         */
        _inputFocus: function()
        {
            var self = this;
            self.inputter = self.$('[data-role=inputter]');
            var len = self.inputter.val().length;
            if(len > 0){
                self.inputter[0].setSelectionRange(len,len);
            }
            self.inputter.focus();
        },

        setup: function()
        {
            var self = this;
            self.inputter = self.$('[data-role=inputter]')
        },

        /**
         * 渲染模板
         * @returns {Editview}
         */
        render: function()
        {
            var self = this;

            view.prototype.render.apply(self);

            self.inputter = self.$('[data-role=inputter]');
            self.trigger('render');

            return self;
        }

    });

    module.exports = edit_view;
});