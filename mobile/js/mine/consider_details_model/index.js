/**
 * Created by nolest on 2014/9/10.
 *
 *
 *
 * 邀请详情
 */
define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var consider_view = require('./view');
    var global_config = require('../../common/global_config');
    var utility = require('../../common/utility');
    var App = require('../../common/I_APP');
    var m_alert = require('../../ui/m_alert/view');
    var details_model = require('./model');

    page_control.add_page([function()
    {
        return{
            title : '约拍邀请',
            route :
            {
                'mine/consider_details_model/:date_id' : 'mine/consider_details_model'
            },
            transition_type : 'slide',
            ignore_exist : true,
            dom_not_cache : true,
            initialize : function()
            {

            },
            page_init : function(page_view,route_params_arr,route_params_obj)
            {
                var self = this;

                if(!utility.auth.is_login())
                {
                    if(App.isPaiApp)
                    {
                        App.openloginpage(function(data)
                        {
                            if(data.code == '0000')
                            {
                                utility.refresh_page();
                            }
                        });
                    }
                    else
                    {
                        page_control.navigate_to_page('account/login');
                    }

                    return;
                }

                var model = new details_model({});

                var date_id = route_params_arr[0];

                var view = new consider_view
                ({
                    model : model,
                    parentNode : self.$el,
                    templateModel : route_params_obj,
                    date_id : date_id
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

