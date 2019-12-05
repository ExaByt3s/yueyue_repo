/**
 * 首页 - 模特卡
 * 汤圆 2014.8.21
 * @param  {[type]} require 
 * @param  {[type]} exports 
 * @param  {[type]} module  
 * @return {[type]}         
 */
define(function(require, exports, module) 
{
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var utility = require('../../common/utility');
    var ua = require('../../frame/ua');
    var model_card_view = require('../model_card/view');
    var model = require('../model_card/model');

	page_control.add_page([function()
    {
        return{
            title : '模特卡',
            page_key : 'model_card',
            route :
            {
                'model_card/:user_id' : 'model_card'
            },
            transition_type : 'slide',
            dom_not_cache : true,
            ignore_exist : true,
            initialize : function()
            {

            },
            page_init : function(page_view,route_params_arr,route_params_obj)
            {
                var self = this;

                var user_id = route_params_arr[0];

                var preview_data = null;

                var is_preview = !utility.is_empty(route_params_obj);

                if(is_preview)
                {
                    route_params_obj.data.is_preview = true;
                    preview_data = route_params_obj.data
                }


                var model_obj = new model
                ({
                    user_id : user_id
                });

                self.model_card_view_obj = new model_card_view
                ({
                    templateModel:{
                        is_preview : is_preview,
                        is_android : ua.isAndroid
                    },
                    model : model_obj,
                    parentNode : self.$el,
                    user_id : user_id
                }).set_preview_data(preview_data).render();

//                if(is_preview)
//                {
//                    route_params_obj.data.is_preview = true;
//                    model_card_view_obj.set_preview_data(route_params_obj.data)
//                }




            },
            page_before_show : function()
            {
                var self = this;

                // 每次进来模特卡获取最新的数据


            },
            page_show : function()
            {

            },
            page_before_hide : function()
            {
                var self = this;


            }
        }
    }]);

})

