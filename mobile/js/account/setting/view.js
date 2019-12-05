define(function(require,exports,module){
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var view = require('../../common/view');
    var app = require('../../common/I_APP');
    var Scroll = require('../../common/scroll');
    var ua = require('../../frame/ua');
    var Utility = require('../../common/utility');
    var Tip = require('../../ui/m_alert/view');



    var mainTpl = require('./tpl/main.handlebars');
    var iphone_remote_notify_tpl = require('./tpl/iphone_remote_notify.handlebars');

    var action_apply_view = view.extend({
        attrs:{
            template:mainTpl
        },
        events:{
            'tap [data-role=page-back]' : function (ev)
            {
                page_control.back();
            },
            'tap [data-role=switch]': '_switch'
        },

        _setup_events:function(){
            var self = this;


            self.on('render',function(){

                /*var data =
                {
                    msgvibrate : 1,
                    msgsound : 0
                };*/



                app.isPaiApp && app.get_setting({},function(data){



                    self.shake = Utility.int(data.msgvibrate);
                    self.sound = Utility.int(data.msgsound);
                    self.iphone_remote_notify = Utility.int(data.remote_notify_setting);

                    ua.isIDevice && self.$('[data-role="ui-table-list"]').prepend(iphone_remote_notify_tpl({
                        iphone_remote_notify : self.iphone_remote_notify
                    }));

                    self._set_push_state('shake',self.shake,1);
                    self._set_push_state('sound',self.sound,1);
                });

            });


            if(!self.view_scroll_obj)
            {

                self._setup_scroll();

                self.view_scroll_obj.refresh();
                return;
            }

            self.view_scroll_obj.refresh();
        },

        /**
         * 安装滚动条
         * @private
         */

        _setup_scroll : function()
        {
            var self = this;
            var view_scroll_obj = Scroll(self.$container,
                {
                    is_hide_dropdown : true
                });

            self.view_scroll_obj = view_scroll_obj;


            //self.view_scroll_obj.refresh();
        },
        _set_push_state : function(type,switch_type,first){
            var self = this;
            //获得状态并设置
            switch_type = (switch_type ? 'on': 'off');
            //设置按钮样式
            var addClass = 'ui-switch-' + switch_type;
            var removeClass = 'ui-switch-' + (switch_type == 'on' ? 'off': 'on');

            //第一次进入页面样式设置
            first && (addClass = 'ui-switch '+ addClass);
            first && (removeClass += ' fn-hide');
            first && self.$('[data-role="ui-loading-' + type + '"]').addClass('fn-hide');

            self.$('[data-target="' + type + '"]').addClass(addClass).removeClass(removeClass);

        },
        setup : function()
        {

            var self = this;

            self.$container = self.$('[data-role=container]'); // 滚动容器

            // 安装事件
            self._setup_events();


        },
        render : function()
        {
            var self = this;

            view.prototype.render.apply(self);

            self.trigger('render');

            return self;
        },
        /**
         * 设置按钮事件
         * @param event
         * @private
         */
        _switch: function(event) {
            var self = this;

            var $target = $(event.currentTarget);
            var target = $target.attr('data-target');

            var switch_type = 0;

            self[target] = !self[target] ? 1 : 0;
            self[target] && (switch_type = 1);

            console.log({
                msgvibrate : Utility.int(self.shake),
                msgsound : Utility.int(self.sound)
            })

            clearTimeout(self._switchtimer);
            self._switchtimer = setTimeout(function() {
                app.set_setting({
                    msgvibrate : Utility.int(self.shake),
                    msgsound : Utility.int(self.sound)
                })
            }, 1000);

            setTimeout(function() {
                self._set_push_state(target,switch_type);
            }, 0);
        }



    })

    module.exports = action_apply_view;
});