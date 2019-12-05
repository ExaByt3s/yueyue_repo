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
    var page_control = require('../../frame/page_control');
    var fans_view = require('../fans_follows/view');
    var fans_collection = require('../fans_follows/collection');

	page_control.add_page([function()
    {
        return{
            title : '粉丝',
            route :
            {
                'account/fans_follows/:user_id/:type' : 'account/fans_follows'
            },
            dom_not_cache : false,
            ignore_exist : true,
            transition_type : 'slide',
            page_init : function(page_view,route_params_arr,route_params_obj)
            {
                var self = this;
                var user_id = route_params_arr[0];
                var type = route_params_arr[1];

                var collection = new fans_collection({
                    user_id : user_id,
                    type : type.toString()
                });

                //标题处理
                if( type == 'follow')
                {
                    self.header_title = '关注';
                }
                else
                {
                    self.header_title = '粉丝';
                };


                var fans_view_obj = new fans_view
                ({
                    templateModel :
                    {
                        header_title : self.header_title
                    },

                    collection : collection,
                    parentNode : self.$el,
                    type : type.toString()
                }).render();

            },
            page_before_show : function()
            {

            },
            page_show : function()
            {

            },
            page_before_hide : function()
            {

            }
        }
    }]);

})

