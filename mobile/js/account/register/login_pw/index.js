/**
 * Created by nolest on 2014/9/18.
 */

define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../../../frame/page_control');
    var login_pw_view = require('./view');

    var utility = require('../../../common/utility');

    page_control.add_page([function()
    {
        return{
            title : '注册 - 流程2',
            route :
            {
                'account/register/login_pw' : 'account/register/login_pw'
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



                var login_pw_view_obj = new login_pw_view
                ({
                    model : utility.user,
                    parentNode : self.$el,
                    data : route_params_obj
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

