/**
 * 专题最终页
 *
 * hdw 2014.10.27
 */


define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var topic_view = require('./view');
    var global_config = require('../../common/global_config');
    var utility = require('../../common/utility');
    var model = require('../model');
    var m_alert = require('../../ui/m_alert/view');

    page_control.add_page([function()
    {
        return{
            title : '约约专题',
            page_key : 'topic_info',
            route :
            {
                'topic/:topic_id(/:is_preview)' : 'topic'
            },
            dom_not_cache : true,
            ignore_exist : true,
            transition_type : 'slide',

            page_init : function(page_view,route_params_arr,route_params_obj)
            {
                var self = this;
                var topic_id = route_params_arr[0];
                var is_preview = route_params_arr[1] ? true : false;

                if(topic_id)
                {
                    var model_obj = new model();
                }
                else
                {
                    var model_obj = route_params_obj.model;
                }


                var view = new topic_view
                ({
                    model : model_obj,
                    parentNode : self.$el,
                    topic_id : topic_id,
                    is_preview : is_preview
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
