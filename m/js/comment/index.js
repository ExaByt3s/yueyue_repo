/**
 * 首页 - 城市选择 基础页面框架
 * 汤圆 2014.8.18
 * @param  {[type]} require 
 * @param  {[type]} exports 
 * @param  {[type]} module  
 * @return {[type]}         
 */
define(function(require, exports, module) 
{
    var $ = require('$');
    var page_control = require('../frame/page_control');
    var comment_view = require('../comment/view');


	page_control.add_page([function()
    {
        return{
            title : '评价',
            route :
            {
                'comment/:role/:date_id/:role_id(/:custom)' : 'comment'
            },
            dom_not_cache : true,
            ignore_exist : true,
            transition_type : 'slide',
            initialize : function()
            {
                
            },
            page_init : function(page_view,route_params_arr,route_params_obj)
            {

                // modify by hudw 2015.2.9
                var custom_obj = {};

                if(route_params_arr[3])
                {
                    custom_obj = decodeURIComponent(route_params_arr[3]);

                    custom_obj = eval("(" + custom_obj + ")");
                }

                console.log(custom_obj)

                var self = this;
                var role = route_params_arr[0];
                var date_id = route_params_arr[1];
                var role_id = route_params_arr[2];
                var table_id = (route_params_obj && route_params_obj.table_id) ||(custom_obj && custom_obj.table_id) || 0;


                self.comment_view_obj = new comment_view
                ({
                    role : role,
                    date_id : date_id ,
                    role_id : role_id ,
                    table_id : table_id ,
                    parentNode : self.$el

                }).render();

                page_view.view = self.comment_view_obj;
            },
            page_before_show : function()
            {

            },
            page_show : function()
            {

            },
            page_before_hide : function()
            {

            },
            window_change : function(page_view)
            {
                page_view.view.reset_scroll_height()
                //self.view.reset_scroll_height()
            }
        }
    }]);

})

