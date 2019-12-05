/**
 * Created by nolest on 2014/9/13.
 *
 *
 * 账单
 */
define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../../../frame/page_control');
    var bill_view = require('./view');
    var bill_model = require('./model');

    page_control.add_page([function()
    {
        return{
            title : '账单',
            route :
            {
                'mine/money/bill' : 'mine/money/bill'
            },
            dom_not_cache : false,
            transition_type : 'slide',
            initialize : function()
            {
                var self = this;

                var model = new bill_model();

                var bill_view_obj = new bill_view
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

