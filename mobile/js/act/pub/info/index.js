/**
 * Created by nolest on 2014/8/30.
 */
/**
 * 首页 - 活动列表
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
                'act/pub_info' : 'act/pub_info'
            },
            transition_type : 'slide',

            page_init : function(page_view,route_params_arr,route_params_obj)
            {
                var self = this;

                var model = new this_model
                ({
                    url : global_config.ajax_url.get_club
                });

                self.view = new this_view
                ({
                    model : model,
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

