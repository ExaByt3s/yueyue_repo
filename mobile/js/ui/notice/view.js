/**
 * Created by nolest on 2014/9/12.
 *
 *  传入 text - 文本内容         '默认文本'
 *  传入 top - 控制高度          '80px'
 *  传入 parentNode - 父节点     滚动条的容器
 *
 *
 */

define(function(require, exports, module)
{
    var $ = require('$');
    var View = require('../../common/view');
    var utility = require('../../common/utility');
    var notice_tpl = require('./tpl/notice.handlebars');
    var page_control = require('../../frame/page_control');

    module.exports = View.extend
    ({
        attrs :
        {
            template : notice_tpl
        },
        events :
        {

        },
        _setup_event : function()
        {
            var self = this;

        },
        _text_init : function()
        {
            var self = this;

            self.set_text(self.get("text"))
        },
        setup : function()
        {
            var self = this;

            self.top = self.get("top") || '45px';

            self.$notice = self.$('[data-role="absolute_notice_con"]');

            self.$text = self.$('[data-role="text"]');

            self._setup_event();

            self._text_init();

            self._top_init(self.top);
        },
        set_text : function(text)
        {
            //设置提示
            var self = this;

            self.$text.html(text)
        },
        get_text : function()
        {
            //获取提示
            var self = this;

            return self.$text.html();
        },
        _top_init : function(top)
        {
            var self = this;

            self.$notice.css('top',top)
        },
        set_top : function(top)
        {
            //设置高度
            var self = this;

            self.$notice.css('top',top)
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