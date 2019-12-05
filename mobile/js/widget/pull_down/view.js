/**
 * 下拉刷新组件
 */
define(function(require, exports, module)
{
    var $ = require('$');
    var View = require('../../common/view');

    var utility = require('../../common/utility');
    var pull_down_tpl = require('./tpl/pull_down.handlebars');



    module.exports = View.extend
    ({

        attrs :
        {
            template : pull_down_tpl
        },
        events :
        {

        },
        /**
         * 设置下拉刷新的样式
         * @param type
         * @private
         */
        set_pull_down_style : function(type)
        {
            var self = this;

            var style = 'pull-down-container ';

            var text = '下拉刷新';

            // 重置样式
            self.$pull_down_container.attr('class',style);

            switch (type)
            {
                case 'release':
                    style += 'release';
                    text = '释放刷新';
                    self.$pull_down_container.find('i').removeClass(" ui-loading-animate");
                    break;
                case 'loading':
                    style += 'loading';
                    text = '正在加载';
                    self.$pull_down_container.find('i').addClass(" ui-loading-animate");
                    break;
                case 'loaded':
                    style += 'loaded';
                    text = '下拉刷新';
                    self.$pull_down_container.find('i').removeClass(" ui-loading-animate");
                    break
            }

            //icon-loading-40x40
            //icon-more-24x32

            self.$('[data-role=pull-down-text]').html(text);

            self.$pull_down_container.addClass(style);
        },

        setup : function()
        {
            var self = this;

            self.$pull_down_container = self.$el;

        },
        render : function()
        {
            var self = this;

            // 调用渲染函数
            View.prototype.render.apply(self);

            self.trigger('render');

            return self;
        }
    });
});