/**
 * Created by nolestLam on 2015/3/5.
 */
define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var coupon_view = require('./view');
    var model = require('../model');
    var utility = require('../../common/utility');
    var global_config = require('../../common/global_config');


    page_control.add_page([function()
    {
        return {
            title : '优惠券',
            route :
            {
                'coupon/choose/:params' : 'coupon/choose/:params'
            },
            transition_type : 'slide',
            page_init : function(page_view,route_params_arr,route_params_obj)
            {
                var self = this;

                var params = {};

                var coupon_model = new model
                ({
                    url : global_config.ajax_url.get_user_coupon_list_by_check
                });

                console.log(route_params_obj);

                if(route_params_arr[0])
                {
                    params = eval('('+decodeURIComponent(route_params_arr[0])+')');
                }

                if(route_params_obj)
                {
                    self.route_params_obj = route_params_obj;
                }

                coupon_model.set(params);

                var coupon_view_obj = new coupon_view
                ({
                    model : coupon_model,
                    parentNode : self.$el,
                    route_params_obj : self.route_params_obj
                }).render();
            },
            page_before_show : function ()
            {

            },
            page_show : function()
            {

            },
            page_before_hide : function()
            {
                var self = this;

            },
            page_hide : function()
            {

            }
        }
    }]);
});