/**
 * Created by nolest on 2014/9/2.
 *
 *
 *
 * 首页 - 活动介绍
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
                'act/pub_intro' : 'act/pub_intro'
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

