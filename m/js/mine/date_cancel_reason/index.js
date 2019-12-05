/**
 * Created by nolest on 2014/11/21.
 *
 * 取消理由
 */

define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var cancel_view = require('./view');
    var utility = require('../../common/utility');

    page_control.add_page([function()
    {
        return{
            title : '取消理由',
            route :
            {
                'mine/date_cancel_reason/:date_id(/:type)' : 'mine/date_cancel_reason'
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

                var from_app = utility.get_url_params(window.location.search,'from_app');

                var cancel_view_obj = new cancel_view
                ({
                    from_app : utility.int(from_app),
                    cancel_wait : route_params_arr[1],
                    date_id :route_params_arr[0],
                    parentNode : self.$el
            }).render();
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

