/**
 * Created by nolest on 2014/9/10.
 */

define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../../../frame/page_control');
    var consider_view = require('./view');
    var global_config = require('../../../common/global_config');
    var utility = require('../../../common/utility');

    page_control.add_page([function()
    {
        return{
            title : '约拍邀请',
            route :
            {
                'mine/consider_details_model/receive' : 'mine/consider_details_model/receive'
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

                console.log(route_params_obj)
                //data = $.extend(true,{},data,{total_price : data.date_price * data.date_hour})
                var view = new consider_view
                ({
                    parentNode : self.$el,
                    templateModel : route_params_obj
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

