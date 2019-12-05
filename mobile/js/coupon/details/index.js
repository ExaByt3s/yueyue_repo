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


    page_control.add_page([function()
    {
        return {
            title : '优惠券详情',
            route :
            {
                'coupon/details/:sn(/:get_coupon)' : 'coupon/details'
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

                var coupon_model = new model();

                //根据传入参数的不同做分支，当有第二个参数时，第一个参数为supply_id，用于查询优惠券状态
                //只有一个参数sn时，为查询该sn的优惠券详细信息
                var coupon_view_obj = new coupon_view
                ({
                    hide_btn : route_params_obj && route_params_obj.hide_btn,
                    sn : route_params_arr[0],
                    get_coupon : route_params_arr[1]?true:false,
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