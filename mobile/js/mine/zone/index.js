/**
 * 我的zone
 * hudw 2014.9.8
 * @param  {[type]} require 
 * @param  {[type]} exports 
 * @param  {[type]} module  
 * @return {[type]}         
 */
define(function(require, exports, module) 
{
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var ua = require('../../frame/ua');
    var utility = require('../../common/utility');
    //var zone_cameraman_view = require('./cameraman_view');
    var zone_cameraman_view = require('./cameraman_view_v2');
    var zone_model_view = require('./model_view');
    var model = require('../model');


	page_control.add_page([function()
    {
        return{
            title : '我的空间',
            page_key : 'cameraman_card',
            route :
            {
                'zone/:user_id/:role' : 'zone'
            },
            dom_not_cache : true,
            ignore_exist : true,
            transition_type : 'slide',
            page_init : function(page_view,route_params_arr,route_params_obj)
            {
                var self = this;

                var user_id = route_params_arr[0];

                if( route_params_arr[1] == 'model')
                {

                    self.zone_model_view_obj = new zone_model_view
                    ({
                        parentNode : self.$el,
                        //model : model_obj,
                        model : utility.user,
                        user_id : user_id
                    }).render();
                }
                else
                {

                    self.zone_cameraman_view_obj = new zone_cameraman_view
                    ({
                        parentNode : self.$el,
                        //model : model_obj,
                        user_id : user_id,
                        templateModel :{
                            is_android : ua.isAndroid,
                            is_myself : utility.user.get('user_id') == user_id
                        },
                        is_myself : utility.user.get('user_id') == user_id

                    }).render();


                }


            },
            page_before_show : function()
            {
                var self = this;

                /*if(self.zone_cameraman_view_obj)
                {
                    self.zone_cameraman_view_obj.refresh();
                }*/

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

