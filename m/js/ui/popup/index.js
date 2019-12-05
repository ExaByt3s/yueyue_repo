define(function(require, exports, module) {
    var $ = require('$');
    var UA = require('../../frame/ua');
    var View = require('../../common/view');
    var templateHelpers = require('../../common/template-helpers');
    var popupTpl = require('./popup.handlebars');

    var Popup = View.extend({
        attrs: {
            style: null,
            content: '',
            start: 'bottom',
            closeButton: true,
            template: popupTpl
        },

        templateHelpers: {
            formatString: templateHelpers.formatString
        },

        events: {
            'tap': function(event) {
                var self = this;

                if (!event.target.getAttribute('data-role') &&
                    event.currentTarget === event.target) {

                    setTimeout(function() {
                        self.hide();
                    }, 100);
//                    self.hide();
                }
            },

            'webkitAnimationEnd': '_animationEnd',
            'animationend': '_animationEnd',
            'tap [data-role=close-button]': 'hide'
        },

        /**
         * 构建element
         * @private
         */
        _parseElement: function() {
            var self = this;

            self.set('templateModel', {
                content: self.get('content'),
                closeButton: self.get('closeButton'),
                start: self.get('start')
            });

            View.prototype._parseElement.apply(self);

            self.$container = self.$('[data-role=container]');
            self.$content = self.$('[data-role=content]');

            var style = self.get('style');
            style && self.$container.css(style);
        },

        _animationEnd: function(event) {
            var self = this;

            var animationName = event.animationName;

            var start = self.get('start');
            if (animationName === 'popup-' + start + '-slidedown' ||
                animationName === 'popup-' + start + '-slideup') {

                if (!self._visible) {
                    self.$el.addClass('fn-hide')
                        .removeClass(start + '-slideup-pin ' + start + '-slidedown');
                } else if (self._visible) {
                    self.$el.addClass(start + '-slideup-pin')
                        .removeClass(start + '-slideup');
                }

                self._animationRunning = false;
                self.trigger('animationEnd', event);
            }
        },

        setup: function() {
            var self = this;

            self._animationRunning = false;

            View.prototype.setup.call(self);
        },

        setContent: function(str) {
            var self = this;

            self.$content.html(str);

            return self;
        },

        show: function() {
            var self = this;

            if (self._animationRunning) {
                return;
            }

            if (!self.rendered) {
                self.render();
            }

            self._visible = true;

            if (UA.isAndroid) {
                self.$el.addClass(self.get('start') + '-slideup-pin')
                    .removeClass('fn-hide');

                self.trigger('change', self._visible);
            } else {
                setTimeout(function() {
                    self._animationRunning = true;

                    self.$el.removeClass('fn-hide')
                        .addClass(self.get('start') + '-slideup');

                    self.trigger('change', self._visible);
                }, 0);
            }

            return self;
        },

        hide: function() {
            var self = this;

            if (self._animationRunning) {
                return;
            }

            self._visible = false;

            if (UA.isAndroid) {
                self.$el.addClass('fn-hide');

                self.trigger('change', self._visible);
            } else {
                setTimeout(function() {
                    self._animationRunning = true;

                    self.$el.addClass(self.get('start') + '-slidedown');

                    self.trigger('change', self._visible);
                }, 0);
            }

            return self;
        },

        toggle: function() {
            var self = this;

            self._visible ? self.hide() : self.show();

            return self;
        },

        destroy: function() {
            var self = this;

            self.remove();

            View.prototype.destroy.apply(self);

            return self;
        }
    });

    module.exports = Popup;
});