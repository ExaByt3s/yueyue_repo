/**
 * Created by nolestLam on 2015/3/31.
 */
define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../../../frame/page_control');
    var level_3_status_view = require('./view');
    var model = require('./model');
    var utility = require('../../../common/utility');
    var global_config = require('../../../common/global_config');


    page_control.add_page([function()
    {
        return {
            title : 'V3 达人认证',
            route :
            {
                'mine/level_3/status' : 'mine/level_3/status'
            },
            transition_type : 'slide',
            dom_not_cache : true,
            ignore_exist : true,
            page_init : function(page_view,route_params_arr,route_params_obj)
            {
                var self = this;

                var params = {};

                var level_3_status_model = new model
                ({
                    url : global_config.ajax_url.get_user_coupon_list_by_check
                });

                self.level_3_status_view = new level_3_status_view
                ({
                    model : level_3_status_model,
                    parentNode : self.$el,
                    route_params_obj : self.route_params_obj
                }).render();
            },
            page_before_show : function ()
            {

            },
            page_show : function()
            {

            },
            page_before_hide : function()
            {
                var self = this;

            },
            page_hide : function()
            {

            }
        }
    }]);
});