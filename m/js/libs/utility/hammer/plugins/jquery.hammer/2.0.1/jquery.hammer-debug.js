define("utility/hammer/plugins/jquery.hammer/2.0.1/jquery.hammer-debug", ["$-debug", "utility/hammer/2.0.1/hammer-debug"], function(require) {

    var $ = require("$-debug");
    var Hammer = require("utility/hammer/2.0.1/hammer-debug");

    (function($, Hammer, dataAttr) {
        function hammerify(el, options) {
            var $el = $(el);
            if(!$el.data(dataAttr)) {
                $el.data(dataAttr, new Hammer($el[0], options));
            }
        }

        $.fn.hammer = function(options) {
            return this.each(function() {
                hammerify(this, options);
            });
        };

        // extend the emit method to also trigger jQuery events
        Hammer.Manager.prototype.emit = (function(originalEmit) {
            return function(type, data) {
                originalEmit.call(this, type, data);
                $(this.element).triggerHandler({
                    type: type,
                    gesture: data
                });
            };
        })(Hammer.Manager.prototype.emit);
    })($, Hammer, "hammer");
});