/**
 * 登录协议
 */

define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var agreement_login_view = require('./view');


    page_control.add_page([function()
    {
        return{
            title : '设置',
            route :
            {
                'account/agreement_login' : 'account/agreement_login'
            },
            dom_not_cache : true,
            ignore_exist : true,
            transition_type : 'slide',
            initialize : function()
            {
                var self = this;

                var agreement_login_view_obj = new agreement_login_view
                ({
                    parentNode : self.$el
                }).render();
            },
            page_init : function()
            {

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

