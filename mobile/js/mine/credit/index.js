/**
 * Created by nolest on 2014/11/6.
 *
 * 信用金
 */

define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var utility = require('../../common/utility');
    var credit_view = require('./view');


    page_control.add_page([function()
    {
        return{
            title : '信用金',
            route :
            {
                'mine/credit' : 'mine/credit'
            },
            dom_not_cache : true,
            ignore_exist : true,
            transition_type : 'slide',
            page_init : function(page_view,route_params_arr,route_params_obj)
            {
                var self = this;

                var credit_view_obj = new credit_view
                ({
                    model : utility.user,
                    parentNode : self.$el,
                    templateModel : route_params_obj
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
});

