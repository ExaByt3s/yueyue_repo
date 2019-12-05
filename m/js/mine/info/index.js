/**
 * Created by nolest on 2014/10/27.
 *
 * 活动状态详情
 */

define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var info_view = require('./view');
    var global_config = require('../../common/global_config');
    var utility = require('../../common/utility');
    var info_model = require('./model');
    var m_alert = require('../../ui/m_alert/view');

    page_control.add_page([function()
    {
        return{
            title : '外拍活动',
            route :
            {
                'mine/info/:type/:enroll_id' : 'mine/info'
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

                var model = new info_model({});

                var view = new info_view
                ({
                    model : model,
                    parentNode : self.$el,
                    params_arr : route_params_arr
                    //templateModel : route_params_obj
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

});
