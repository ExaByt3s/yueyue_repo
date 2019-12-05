/**
 * Created by nolest on 2014/9/3.
 *
 * 发布页 - 活动安排
 */
define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../../../frame/page_control');
    var this_view = require('./view');
    var this_model = require('../model');
    var global_config = require('../../../common/global_config');

    page_control.add_page([function()
    {
        return{
            title : '发布活动',
            route :
            {
                'act/pub_arrange' : 'act/pub_arrange'
            },
            transition_type : 'slide',
            initialize : function()
            {


            },
            page_init : function(page_view,route_params_arr,route_params_obj)
            {
                var self = this;

                var model = new this_model
                ({
                    url : global_config.ajax_url.add_act //--
                });

                self.view = new this_view
                ({
                    model : model,
                    selected_obj :route_params_obj,
                    parentNode : self.$el
                }).render();

                page_view.view = self.view;
            },
            page_before_show : function()
            {

            },
            page_show : function()
            {

            },
            page_before_hide : function()
            {

            },
            window_change : function(page_view)
            {
                page_view.view.reset_scroll_height()
                //self.view.reset_scroll_height()
            }
        }
    }]);

})

