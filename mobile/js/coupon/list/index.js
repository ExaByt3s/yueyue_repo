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
                'coupon/list/:tap' : 'coupon/list'
            },
            transition_type : 'slide',
            dom_not_cache : true,
            ignore_exist : true,
            initialize : function()
            {

            },
            events :
            {

            },
            page_init : function(page_view,route_params_arr,route_params_obj)
            {
                var self = this;

                var type;

                // 路由参数为其他值时，修正为available
                if(route_params_arr[0] == 'used') type = route_params_arr[0];
                else if(route_params_arr[0] == 'expired') type = route_params_arr[0];
                else type = 'available';
                console.log(type);


                var coupon_model = new model
                ({
                    url : global_config.ajax_url.get_user_coupon_list_by_tab
                });

                var coupon_view_obj = new coupon_view
                ({
                    type : type,
                    model : coupon_model,
                    parentNode : self.$el
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