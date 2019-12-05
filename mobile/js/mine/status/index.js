/**
 * Created by Administrator on 2014/9/24.
 *
 * 我的 - 活动列表
 */

define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var status_view = require('./view');
    var global_config = require('../../common/global_config');
    var utility = require('../../common/utility');
    var status_model = require('./model');
    var m_alert = require('../../ui/m_alert/view');

    page_control.add_page([function()
    {
        return{
            title : '外拍活动',
            route :
            {
                'mine/status(/:tap_type)' : 'mine/status'
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

                var model = new status_model({});

                var view = new status_view
                ({
                    tap_type : route_params_arr[0],
                    model : model,
                    parentNode : self.$el
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
