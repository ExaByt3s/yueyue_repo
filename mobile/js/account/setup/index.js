/**
 * Created by nolest on 2014/9/13.
 */

define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var setup_view = require('./view');
    var setup_model = require('./model');

    page_control.add_page([function()
    {
        return{
            title : '设置',
            route :
            {
                'account/setup' : 'account/setup'
            },
            dom_not_cache : true,
            ignore_exist : true,
            transition_type : 'slide',
            initialize : function()
            {
                var self = this;

                var model = new setup_model();

                var setup_view_obj = new setup_view
                ({
                    model : model,
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

