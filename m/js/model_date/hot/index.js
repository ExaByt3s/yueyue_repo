/**
 * 首页 - 热门 基础页面框架
 * hudw 2014.8.4
 * @param  {[type]} require 
 * @param  {[type]} exports 
 * @param  {[type]} module  
 * @return {[type]}         
 */
define(function(require, exports, module) 
{
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var hot_view = require('./view');
    var hot_collection = require('./collection');
    var utility = require('../../common/utility');
    var guide = require('../../ui/guide/view');
    var first_guide_tpl = require('../../ui/guide/tpl/guide_hot.handlebars');
    var model = require('./model');
    var ua = require('../../frame/ua');
    var App = require('../../common/I_APP');

	page_control.add_page([function()
    {
        return{
            title : '约yue',
            route :
            {
                'hot' : 'hot'
            },
            page_init : function(page_view,route_params_arr,route_params_obj)
            {
                var self = this;

                var location = utility.storage.get('location');

                var collection = new hot_collection
                ({
                    location : location
                });

                //  引导图
                // if( !utility.storage.get('is_frist_time_open_app_hot') )
                // {
                //     self.guide_page = new guide
                //     ({
                //         page_tpl: first_guide_tpl,
                //         parentNode : self.$el
                //     }).render();

                //     self.guide_page.on('guide_hide',function()
                //     {
                //         self.guide_page.hide();

                //         self.hot_view_obj = new hot_view
                //         ({
                //             collection : collection,
                //             model : new model,
                //             parentNode : self.$el
                //         });

                //         utility.storage.set('is_frist_time_open_app_hot',1);

                //         self.hot_view_obj.render();
                //     });

                // }
                // else
                // {

                //     self.hot_view_obj = new hot_view
                //     ({
                //         collection : collection,
                //         model : new model,
                //         parentNode : self.$el
                //     });

                //     self.hot_view_obj.render();
                // }

                self.hot_view_obj = new hot_view
                ({
                    templateModel:{
                        is_android : ua.isAndroid
                    },
                    collection : collection,
                    model : new model,
                    parentNode : self.$el
                });

                self.hot_view_obj.render();

                self.hot_view_obj && self.hot_view_obj.set('location',utility.storage.get('location'));

            },
            page_before_show : function()
            {
                var self = this;

                // 切换地区
                if(self.hot_view_obj && self.hot_view_obj.location && utility.storage.get('location') && self.hot_view_obj.location.location_id != utility.storage.get('location').location_id)
                {
                    self.hot_view_obj.set_location(utility.storage.get('location'));

                    self.hot_view_obj.refresh();
                }



            },
            page_show : function()
            {
                var self = this;
                //返回时重启自动轮播
                if(self.hot_view_obj.iscroll_slide_view){
                    var iscroll_slide_view = self.hot_view_obj.iscroll_slide_view;
                    (!iscroll_slide_view.interval_running) && iscroll_slide_view.setup_auto_slider();
                }
            },
            page_before_show : function()
            {
                var self = this;

                setTimeout(function()
                {
                    self.hot_view_obj.show();
                },50);

            },
            page_before_hide : function()
            {
                var self = this;

                self.hot_view_obj.hide();
                //离开时关闭自动轮播
                (self.hot_view_obj.iscroll_slide_view) && self.hot_view_obj.iscroll_slide_view.clear_interval();
            },
            page_hide : function()
            {
                var self = this;
                //换页前清理下拉菜单
                self.hot_view_obj.select_drop_obj.stay();
            },
            window_change : function()
            {
                var self = this;

                console.log(self)
            }
        }
    }]);

})

