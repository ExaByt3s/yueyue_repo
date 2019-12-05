/**
 * 加载更多按钮
 * hdw 2014.8.18
 */
define(function(require, exports, module)
{
    var $ = require('$');
    var View = require('../../common/view');

    var utility = require('../../common/utility');
    var load_more_tpl = require('./tpl/load_more.handlebars');


    module.exports = View.extend
    ({
        attrs :
        {
            template : load_more_tpl
        },
        events :
        {

        },
        setup : function()
        {
            var self = this;

            self.hide();

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