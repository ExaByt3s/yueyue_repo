/**
 * Created by nolest on 2014/9/13.
 */

define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../../../../frame/page_control');
    var enter_pw_view = require('./view');
    var enter_pw_model = require('./model');

    page_control.add_page([function()
    {
        return{
            title : '设置',
            route :
            {
                'account/setup/bind/enter_pw/:form' : 'account/setup/bind/enter_pw'
            },
            dom_not_cache : true,
            ignore_exist : true,
            transition_type : 'slide',
            initialize : function()
            {

            },
            page_init : function(page_view,route_params_arr,route_params_obj)
            {
                var self = this;

                var is_form_setup;

                var is_form_login;

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

                var data =
                {
                    is_form_setup : is_form_setup,
                    is_form_login : is_form_login
                };

                var model = new enter_pw_model();

                var enter_pw_view_obj = new enter_pw_view
                ({
                    submit_data : route_params_obj,
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

