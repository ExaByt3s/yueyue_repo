/**
 * 提示层
 * hdw 2014.8.29
 */
define(function(require, exports, module)
{
    var $ = require('$');
    var View = require('../../common/view');
    var utility = require('../../common/utility');
    var tpl = require('./tpl/m_alert.handlebars');

    module.exports = new(View.extend
    ({
        attrs :
        {
            className: 'ui-alert'
        },
        events:
        {
            /*'webkitTransitionEnd': '_transition_end',
            'transitionEnd': '_transition_end'*/
            'webkitTransitionEnd' : '_transition_end',
            'transitionend' : '_transition_end'
        },
        _setup_event : function()
        {
            var self = this;

        },
        setup : function()
        {
            var self = this;

            self._setup_event();
        },
        /**
         * 显示浮层
         * @param text
         * @param type
         * @param options
         * @returns {*}
         */
        show: function(text, type, options) {
            var self = this;

            text = String(text);

            if (!text) {
                return;
            }

            if (typeof type !== 'string') {
                options = type;
                type = '';
            }

            if (!self.rendered) {
                self.render();
            }

            options || (options = {});

            var iconClass = 'icon ';
            switch (type) {
                case 'right':
                    iconClass += 'icon-check-circle';
                    break;
                case 'error':
                    iconClass += 'icon-info-circle';
                    break;
                case 'loading':
                    iconClass += 'icon-spinner ui-loading-animate';
                    break;
                default:
                    iconClass += '';
                    break;
            }

            console.log(type)

            var htmlStr = tpl({
                text: text, iconClass: iconClass
            });
            self.$el.html(htmlStr).removeClass('fn-hide');

            clearTimeout(self._clearTimer);
            clearTimeout(self._triggerHideTimer);

            var delay_time = options.delay || 0;

            if (utility.int(delay_time) > -1) {
                self._triggerHideTimer = setTimeout(function() {
                    self.hide();
                }, options.delay || 2400);
            }

            self._visible = true;

            return self;
        },

        /**
         * 隐藏浮层
         * @returns {*}
         */
        hide: function(options) {
            options || (options = {});

            var self = this;

            clearTimeout(self._clearTimer);
            clearTimeout(self._triggerHideTimer);
            if (utility.int(options.delay) === -1)
            {
                self.$el.addClass('fn-hide');
            }
            else
            {
                self._clearTimer = setTimeout(function()
                {
                    if (self._visible)
                    {
                        //self.$el.addClass('fadeout');

                        self.$el.addClass('fn-hide').removeClass('fadeout');

                        self._visible = false;
                    }
                }, options.delay);
            }

            return self;
        },
        _transition_end : function(event)
        {
            var self = this;


            if (event.propertyName == 'opacity' && !self._visible)
            {
                self.$el.addClass('fn-hide').removeClass('fadeout');
            }
        }
    }));
});