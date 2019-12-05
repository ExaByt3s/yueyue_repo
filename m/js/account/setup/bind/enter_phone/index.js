/**
 * Created by nolest on 2014/9/13.
 */

define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../../../../frame/page_control');
    var enter_phone_view = require('./view');
    var enter_phone_model = require('./model');

    page_control.add_page([function()
    {
        return{
            title : '设置',
            route :
            {
                'account/setup/bind/enter_phone/:form(/:form_setup_bind)' : 'account/setup/bind/enter_phone'
            },
            dom_not_cache : false,
            transition_type : 'slide',
            initialize : function()
            {

            },
            page_init : function(page_view,route_params_arr,route_params_obj)
            {
                var self = this;

                var is_form_setup;

                var is_form_login;

                var form_setup_bind = '';

                if(route_params_arr[0] == 'form_setup')
                {
                    is_form_setup = true;
                    is_form_login = false;
                }
                else if(route_params_arr[0] == 'form_login')
                {
                    is_form_setup = false;
                    is_form_login = true;
                }

                if(route_params_arr[1])
                {
                    form_setup_bind = route_params_arr[1];
                }



                var data =
                {
                    is_form_setup : is_form_setup,
                    is_form_login : is_form_login,
                    form_setup_bind : form_setup_bind
                };

                var model = new enter_phone_model();

                var enter_phone_obj = new enter_phone_view
                ({
                    model : model,
                    parentNode : self.$el,
                    templateModel : data
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

