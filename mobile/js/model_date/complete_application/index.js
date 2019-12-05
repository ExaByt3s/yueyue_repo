/**
 * 约拍完成
 * hudw 2014.9.21
 * @param  {[type]} require 
 * @param  {[type]} exports 
 * @param  {[type]} module  
 * @return {[type]}         
 */
define(function(require, exports, module) 
{
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var complete_application_view = require('../complete_application/view');


	page_control.add_page([function()
    {
        return{
            title : '约拍完成',
            route :
            {
                'model_date/complete_application/:date_id' : 'model_date/complete_application'
            },
            dom_not_cache : true,
            ignore_exist : true,
            transition_type : 'slide',
            page_init : function(page_view,route_params_arr,route_params_obj)
            {
                var self = this;

                var date_id = route_params_arr[0];

                var complete_application_view_obj = new complete_application_view
                ({
                    parentNode : self.$el,
                    date_id : date_id
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

