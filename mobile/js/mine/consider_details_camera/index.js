/**
 * Created by nolest on 2014/9/10.
 *
 *
 *
 * 约拍订单页
 */
define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var consider_view = require('./view');
    var global_config = require('../../common/global_config');
    var utility = require('../../common/utility');

    page_control.add_page([function()
    {
        return{
            title : '约拍邀请',
            page_key : 'date_info',
            route :
            {
                'mine/consider_details_camera/:date_id(/:form)' : 'mine/consider_details_camera'
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

                var date_id = route_params_arr[0];

                var date_role_is_model = 0;

                if(utility.user.get('role') == 'model')
                {
                    // 约拍对象是摄影师

                    date_role_is_model = 0;
                }
                else
                {
                    // 约拍对象是模特

                    date_role_is_model = 1;
                }

                var is_form_cancel = false;

                if(route_params_arr[1] == 'form_cancel')
                {
                    is_form_cancel = true;
                }

                var from_app = utility.get_url_params(window.location.search,'from_app');

                var view = new consider_view
                ({
                    from_app : utility.int(from_app),
                    is_form_cancel : is_form_cancel,
                    parentNode : self.$el,
                    date_id : date_id,
                    date_role_is_model : date_role_is_model
                }).render();



                //templateModel : templateModel
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

