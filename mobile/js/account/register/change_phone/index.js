/**
 * Created by nolest on 2014/9/18.
 */

define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../../../frame/page_control');
    var change_phone_view = require('./view');
    var change_phone_model = require('./model');

    page_control.add_page([function()
    {
        return{
            title : '设置 - 更换手机',
            route :
            {
                'account/register/change_phone' : 'account/register/change_phone'
            },
            dom_not_cache : true,
            ignore_exist : true,
            transition_type : 'slide',
            initialize : function()
            {
                var self = this;

                var model = new change_phone_model();

                var change_phone_view_obj = new change_phone_view
                ({
                    model : model,
                    parentNode : self.$el
                }).render();
            },
            page_init : function(page_view,route_params_arr,route_params_obj)
            {

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

