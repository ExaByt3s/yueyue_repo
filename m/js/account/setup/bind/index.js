/**
 * Created by nolest on 2014/9/13.
 */

define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../../../frame/page_control');
    var bind_view = require('./view');
    var bind_model = require('./model');

    page_control.add_page([function()
    {
        return{
            title : '设置',
            route :
            {
                'account/setup/bind' : 'account/setup/bind'
            },
            dom_not_cache : false,
            transition_type : 'slide',
            initialize : function()
            {
                var self = this;

                var model = new bind_model();

                var bind_view_obj = new bind_view
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

