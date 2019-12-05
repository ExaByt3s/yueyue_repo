define(function(require,exports,module){
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var view = require('../../common/view');
    var Utility = require('../../common/utility');
    var Tip = require('../../ui/m_alert/view');



    var mainTpl = require('./tpl/main.handlebars');

    var abnormal_view = view.extend({
        attrs:{
            template:mainTpl
        },
        events:{
            'tap [data-role="tap-screen"]' : function (ev)
            {
                var self = this;
                self.trigger('tap:broken_network');
            }
        },

        _setup_events:function(){
            var self = this;


        },

        /**
         * 安装滚动条
         * @private
         */

        _setup_scroll : function()
        {
            var self = this;
            var view_scroll_obj = Scroll(self.$container);

            self.view_scroll_obj = view_scroll_obj;

            //self.view_scroll_obj.refresh();
        },



        setup : function()
        {

            var self = this;

            self.$container = self.$('[data-role=container]'); // 滚动容器

            // 安装事件
            self._setup_events();

            //self.view_scroll_obj.refresh();


        },

        /**
         * 销毁
         * @returns {abnormal}
         */
        destroy: function() {
            var self = this;


            view.prototype.remove.call(self);
            view.prototype.destroy.call(self);

            return self;
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

    })

    module.exports = abnormal_view;
});