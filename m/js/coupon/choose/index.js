/**
 * Created by nolestLam on 2015/3/5.
 */
define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var coupon_view = require('./view');
    var model = require('./model');
    var utility = require('../../common/utility');


    page_control.add_page([function()
    {
        return {
            title : '优惠券',
            route :
            {
                'coupon' : 'coupon'
            },
            initialize : function()
            {

            },
            events :
            {

            },
            page_init : function()
            {
                var self = this;

                var coupon_view_obj = new coupon_view
                ({
                    model : model,
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