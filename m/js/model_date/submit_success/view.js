/**
 * 首页 - 模特卡
 * 汤圆 2014.8.21
 */
define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var View = require('../../common/view');
    var submit_success = require('../submit_success/tpl/main.handlebars');
    var utility = require('../../common/utility');
    var templateHelpers = require('../../common/template-helpers');
    var Scroll = require('../../common/scroll');
    var App = require('../../common/I_APP');
    var grid = require('../../widget/model_pic/view');
    var global_config = require('../../common/global_config');
    var WeixinApi = require('../../common/I_WX');

    var submit_success_view = View.extend
    ({

        attrs:
        {
            template: submit_success
        },
        events :
        {
            'tap [data-role=date-city]' : function (ev)
            {

            },
            'tap [data-role=page-back]' : function (ev)
            {
                page_control.navigate_to_page('hot');
            },
            'tap [data-role="to_date_list"]' : function()
            {
                page_control.navigate_to_page('mine/consider/can_back_to_mine')
            },
            'tap [data-role="talk"]' : function()
            {
                var self = this;

                if(!utility.auth.is_login())
                {
                    page_control.navigate_to_page('account/login');

                    return;
                }

                var date_model = utility.date_info && utility.date_info.get().model;

                var data =
                {
                    senderid : utility.login_id,
                    receiverid : utility.int(date_model.user_id),
                    sendername : utility.user.get('nickname'),
                    receivername : date_model.user_name,
                    sendericon : utility.user.get('user_icon'),
                    receivericon : date_model.user_icon
                };

                if(App.isPaiApp)
                {
                    App.chat(data);
                }

            },
            'tap [data-role="to_message"]' : function()
            {
                var self = this;

                page_control.navigate_to_page('hot');

                console.log(self.from_app);

                if(self.from_app)
                {
                    console.log("back");
                    App.isPaiApp && App.app_back();
                }
                else
                {
                    console.log("show_chat_list");
                    App.isPaiApp && App.show_chat_list();
                }
            },
            'tap [data-name="wx_card_download_btn"]' : function()
            {
                window.location.href = 'http://app.yueus.com/';
            },
            'tap [data-role="wx_return"]' : function()
            {
                page_control.navigate_to_page(global_config.default_index_route);
            },
            'tap [data-role=btn-role-down-app]': function(ev)
            {
                var self = this;
                window.location.href = 'http://app.yueus.com/';
            },
            'tap [data-role=btn-v2-validation]': function(ev)
            {
                var self = this;
                page_control.navigate_to_page('model_date/v2_validation');
            }
          
        },

        /**
         * 安装事件
         * @private
         */
        _setup_events : function()
        {

            var self = this;

            self.model
            .on('before:fetch', function()
            {

            }).on('success:fetch', function()
            {
                self._render_model_card();

            }).on('complete:fetch', function (xhr, status)
            {

            });
 
            self.on('update_info',function(response,xhr)
            {

                // 区分当前对象
                var _self = this;

                // 头部滚动条
                // self._setup_scroll_top();
                // self.view_scroll_obj_top.refresh();


                //主要滚动条
                self._setup_scroll();
                self.view_scroll_obj.refresh();


            });

            self._setup_scroll();
            self.view_scroll_obj.refresh();
        },
        /**
         * 安装滚动条
         * @private
         */

        _setup_scroll : function()
        {
            var self = this;
            var view_scroll_obj = Scroll(self.$procedure_wrap_page,
            {
                lazyLoad : true,
                is_hide_dropdown : true
            });
            self.view_scroll_obj = view_scroll_obj;
        },

        /**
         * 渲染模特卡
         * @param response
         * @param options
         * @private
         */
        _render_model_card : function()
        {
            var self = this;
            var model_data = self.model.toJSON(); 

            var html_str = model_card
            ({
                model_data_mod : model_data

            });    

            var $container_op = self.$container;

            $container_op.html(html_str);//塞内容

            self.trigger('update_info');
        },

        /**
         * 视图初始化入口
         */
        setup : function()
        {

            var self = this;

            // 配置交互对象
            self.$container = self.$('[data-role=container]'); // 滚动容器
            self.$container_img_list = self.$('[data-role=container-img-list]'); // 头部图片容器
            self.$procedure_wrap = self.$('[data-role=procedure_wrap]'); // 头部图片容器

            self.$procedure_wrap_page = self.$('[data-role="procedure_wrap_page"]'); // 滚动容器
            self.from_app = self.get("from_app");
            self.$v2_level = self.$('[data-role="v2-level"]');

            //self.$container_top = self.$('[data-role=container-top]'); // 滚动容器

            // 安装事件
            self._setup_events();

            // v2显示
            self.v2_validation_show();


        },

        v2_validation_show : function ()
        {
            var self = this;

            if (utility.user.get('user_level') <=1 )
            {
                self.$v2_level.removeClass('fn-hide')
            };

        },

        reset_viewport_height : function()
        {
            return utility.get_view_port_height('nav');
        },

        render : function()
        {
            var self = this;

            var view_port_height = self.reset_viewport_height();

            self.$procedure_wrap_page.height(view_port_height);

            //self.view_scroll_obj.refresh()

            // 调用渲染函数
            View.prototype.render.apply(self);

            self.trigger('render');

            return self;
        }

    });

    module.exports = submit_success_view;
});