define(function(require, exports, module) {
    var $ = require('$');
    var View = require('../../common/view');

    var templateHelpers = require('../../common/template-helpers');

    var dialogTpl = require('./dialog.handlebars');

    var Dialog = View.extend({
        attrs: {
            content: '',
            style: null,
            buttons: [],
            hasMask: true,
            template: dialogTpl
        },

        templateHelpers: {
            formatString: templateHelpers.formatString
        },

        events: {
            'tap [data-role=button]': '_buttons',
            'tap [data-role=close]': '_close_this_view'
        },

        _buttons: function(event) {
            var self = this;

            var $target = $(event.currentTarget);
            var buttonName = $target.attr('data-name');
            self.trigger('tap:button:' + buttonName, $target, event);
            self.trigger('tap:button', $target, event);
        },

        /**
         * 构建element
         * @private
         */
        _parseElement: function() {
            var self = this;

            var buttons = self.get('buttons');
            self.set('templateModel', {
                content: self.get('content'),
                hasMask: self.get('hasMask'),
                hasButtons: $.isArray(buttons) && buttons.length,
                buttons: buttons
            });

            View.prototype._parseElement.apply(self);

            self.$container = self.$('[data-role=container]');
            self.$content = self.$('[data-role=content]');
            self.$footer = self.$('[data-role=footer]');

            var style = self.get('style');
            style && self.$container.css(style);
        },

        /**
         * 隐藏按钮区域
         * @returns {Dialog}
         */
        hideButtons: function() {
            var self = this;

            if (self.$footer) {
                self.$footer.remove();
                self.$footer = null;
            }

            return self;
        },

        setContent: function(str) {
            var self = this;

            self.$content.html(str);

            self.trigger('change:content', str);

            return self;
        },

        /**
         * 显示
         * @returns {Dialog}
         */
        show: function() {
            var self = this;

            if (self.trigger('before:show') === false) {
                return self;
            }

            if (!self.rendered) {
                self.render();
            }

            self.$el.removeClass('fn-hide');

            self.trigger('after:show');

            return self;
        },

        /**
         * 隐藏
         * @returns {Dialog}
         */
        hide: function() {
            var self = this;

            if (self.trigger('before:hide') === false) {
                return self;
            }

            self._visible = false;

            self.$el.addClass('fn-hide');

            self.trigger('after:hide');

            return self;
        },

        /**
         * 销毁
         * @returns {Dialog}
         */
        destroy: function() {
            var self = this;


            View.prototype.remove.call(self);
            View.prototype.destroy.call(self);

            return self;
        },
        _close_this_view : function(){
            var self =this;
            //self.hide().destroy();
            self.hide();
        }
    });

    module.exports = Dialog;
});