/**
 * Created by nolest on 2014/9/18.
 */
define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../../../frame/page_control');
    var login_phone_view = require('./view');
    var login_phone_model = require('./model');

    page_control.add_page([function()
    {
        return{
            title : '注册 - 流程1',
            route :
            {
                'account/register/login_phone' : 'account/register/login_phone'
            },
            dom_not_cache : true,
            ignore_exist : true,
            transition_type : 'slide',
            page_init : function(page_view,route_params_arr,route_params_obj)
            {
                var self = this;

                var model = new login_phone_model();


                var login_phone_view_obj = new login_phone_view
                ({
                    model : model,
                    parentNode : self.$el
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

