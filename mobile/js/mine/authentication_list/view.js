define(function(require,exports,module){
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var view = require('../../common/view');
    var scroll = require('../../common/scroll');
    var utility = require('../../common/utility');
    var tip = require('../../ui/m_alert/view');



    var main_tpl = require('./tpl/main.handlebars');
    var item_tpl = require('./tpl/item.handlebars');

    var authentication_list_view = view.extend({
        attrs:{
            template:main_tpl
        },
        events:{
            'tap [data-role=page-back]' : function (ev)
            {
                page_control.back();
            },
            'tap [data-role=nav]' : '_navigate_to_page'
        },

        _setup_events:function(){
            var self = this;

            // 模型事件
            // --------------------
            self.model.on('before:authentication_list:fetch', self._authentication_list_before, self)
                .on('success:authentication_list:fetch', self._authentication_list_success, self)
                .on('error:authentication_list:fetch', self._authentication_list_error, self)
                .on('complete:authentication_list:fetch', self._authentication_list_complete, self);


            if(!self.view_scroll_obj)
            {
                self._setup_scroll();
            }

            self.on('render',function(){
                self.model.get_level_list()
            });

            self.view_scroll_obj.refresh();
        },

        /**
         * 安装滚动条
         * @private
         */

        _setup_scroll : function()
        {
            var self = this;
            var view_scroll_obj = scroll(self.$container,
                {
                    is_hide_dropdown : true
                });

            self.view_scroll_obj = view_scroll_obj;

            //self.view_scroll_obj.refresh();
        },



        setup : function()
        {

            var self = this;

            self.$container = self.$('[data-role=container]'); // 滚动容器
            self.$authentication_list = self.$('[data-role=authentication-list]');

            // 安装事件
            self._setup_events();

            //self.view_scroll_obj.refresh();


        },

        render : function()
        {
            var self = this;

            self._visible = true;

            view.prototype.render.apply(self);

            self.trigger('render');

            return self;
        },

        /**
         * 导航到目的页面
         * @param event
         * @private
         */
        _navigate_to_page: function(ev) {
            var self = this;

            var $target = $(ev.currentTarget);

            var target_nav = $target.attr('data-target');
            switch (target_nav) {
                case 'V2':
                    //根据audit_status来跳转页面 v2 有 notice ready status 页 nolest 2015-4-7
                    //if(self.pass_list[1].audit_status == "0"){page_control.navigate_to_page('mine/level_2/notice');}
                    //else{page_control.navigate_to_page('mine/level_2/status');}
                    page_control.navigate_to_page('mine/authentication/v2',self.pass_list);
                    break;
                case 'V3':
                    //根据balance_status来跳转页面 v3 有 ready status 页 nolest 2015-4-7
                    if(self.pass_list[1].audit_status == "0"){tip.show('请先验证V2','error');break;}
                    //if(self.pass_list[2].balance_status == "0"){page_control.navigate_to_page('mine/level_3/ready');}
                    //else{page_control.navigate_to_page('mine/level_3/status');}
                    page_control.navigate_to_page('mine/authentication/v3',self.pass_list);
                    break;
            }
        },

        _render_item: function(authentication_list) {
            var self = this;

            var html_str = item_tpl({
                authentication_list: authentication_list
            });

            self.$authentication_list.html(html_str);
        },

        _authentication_list_before:function(){
//            tip.show('加载中...','loading',{
//             delay:-1
//             });
        },

        _authentication_list_success:function(response, xhrOptions){

            tip.hide();

            var self = this;

            self.pass_list = response.result_data.list;

            utility.storage.set("level_data",response.result_data);

            var authentication_list = response.result_data.list;

            self._render_item(authentication_list);

        },

        _authentication_list_error:function(){
            tip.show('网络异常','loading');
        },

        _authentication_list_complete:function(){

        }

    })

    module.exports = authentication_list_view;
});