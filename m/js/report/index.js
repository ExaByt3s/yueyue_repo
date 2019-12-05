/**
 * 举报页
 * hdw 2014.10.28
 */


define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../frame/page_control');
    var report_view = require('./view');
    var global_config = require('../common/global_config');
    var utility = require('../common/utility');
    var report_model = require('./model');
    var m_alert = require('../ui/m_alert/view');

    page_control.add_page([function()
    {
        return{
            title : '约拍邀请',
            route :
            {
                'report/:report_id' : 'report'
            },
            transition_type : 'slide',
            initialize : function()
            {

            },
            page_init : function(page_view,route_params_arr,route_params_obj)
            {
                var self = this;
                var model = new report_model();
                var view = new report_view
                ({
     
                    report_id : route_params_arr[0],
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

