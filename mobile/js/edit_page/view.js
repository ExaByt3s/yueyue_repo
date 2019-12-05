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

            // 设置了字数限制
            if(utility.int(self.get('word_limit'))>=0)
            {
                if(utility.int(self.$('[data-role="word-count"]').html())<0)
                {
                    tip.show('已经超出上限字数', 'error');

                    return;
                }
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
            self.inputter = self.$('[data-role=inputter]');
            self.word_count = self.$('[data-role=word-count]');
            self.word_count_wrap = self.$('[data-role=word-count-wrap]');
            // 安装事件
            self._setup_events();
        },

        _setup_events:function()
        {
            var self = this;

            if(utility.int(self.get('word_limit'))>0)
            {
                //获取焦点
                self.inputter.on('focus',function(ev)
                {
                    self._toggle_tip($(this).val());
                })
                //输入实时监听
                self.inputter.on('input',function(ev)
                {
                    self._toggle_tip($(this).val());
                })
            }


        },

        _count_word: function (count)
        {
            if (!count)
            {
                return 0;
            }

            var test_count = count.match(/[^\x00-\xff]/g);

            var ret = count.length + (test_count ? test_count.length : 0);

            return Math.ceil((ret) / 2);
        },

        _toggle_tip:function(value)
        {
            var self = this;

            var last_txt = utility.int(self.get('word_limit')) - self._count_word(value);

            self.can_submit = true;

            if(last_txt<0)
            {
                self.can_submit = false;
            }
            else
            {
                self.can_submit = true;
            }

            if(last_txt < 10){
                self.word_count_wrap.removeClass('fn-hide');
                self.word_count.html(last_txt);
            }

            if(last_txt > 10){
                self.word_count_wrap.addClass('fn-hide');
                self.word_count.html(last_txt);
            }

            console.log(value.length);
            console.log("=================================");
            console.log(self._count_word(value));

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