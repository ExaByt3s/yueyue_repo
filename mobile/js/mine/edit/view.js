define(function(require,exports,module){
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var view = require('../../common/view');
    var scroll = require('../../common/scroll');
    var utility = require('../../common/utility');
    var tip = require('../../ui/m_alert/view');



    var mainTpl = require('./tpl/main.handlebars');

    var mine_edit_view = view.extend({
        attrs:{
            template:mainTpl
        },
        /**
         * 事件
         */
        events: {
            'tap [data-role=back]' : '_back',
            'tap [data-role=save]' : '_save'
        },


        _back: function() {
            page_control.back();
        },


        _save: function() {
            var self = this;
            var inputterVal = self.inputter.val();
            var inputterTrimVal = $.trim(inputterVal);
            if (!inputterTrimVal) {
                tip.show('请输入内容', 'error', {
                    delay: 1000
                });
                return;
            }
            switch (self.editObj.type) {
                case 'edit-nickname':
                    self.editObj.nickname = inputterTrimVal;
                    page_control.back();
                    break;
                case 'edit-phone':
                    self.editObj.phone = inputterTrimVal;
                    page_control.back();
                    break;
                case 'edit-signature':
                    self.editObj.signature = inputterVal;
                    page_control.back();
                    break;
            }
        },

        /**
         * 光标定到文字最后
         * @private
         */
        _inputFocus: function() {
            var self = this;
            self.inputter = self.$('[data-role=inputter]');
            var len = self.inputter.val().length;
            if(len > 0){
                self.inputter[0].setSelectionRange(len,len);
            }
            self.inputter.focus();
        },

        setup: function() {
            var self = this;
            self.inputter = self.$('[data-role=inputter]')
        },

        /**
         * 渲染模板
         * @returns {Editview}
         */
        render: function() {
            var self = this;

            view.prototype.render.apply(self);

            self.inputter = self.$('[data-role=inputter]')
            self.trigger('render');

            return self;
        },

        /**
         * 设置编辑对象
         * @param EditObj
         * @return {*}
         */
        setEditObj : function(EditObj){
            var self = this;
            self.editObj = EditObj;
            return self;
        }

    })

    module.exports = mine_edit_view;
});