/**
 * Created by nolest on 2014/9/28.
 *
 * 积分榜 次数榜
 */


define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../frame/page_control');
    var global_config = require('../common/global_config');
    var utility = require('../common/utility');
    var App = require('../common/I_APP');

    page_control.add_page([function()
    {
        return{
            title : '约yue',
            route :
            {
                'init' : 'init'
            },
            dom_not_cache : false,
            ignore_exist : true,
            transition_type : 'none',
            page_init : function(page_view,route_params_arr,route_params_obj)
            {
                var self = this;

                if(App.isPaiApp)
                {
                    App.show_bottom_bar('');
                }
                else
                {
                    setTimeout(function()
                    {
                        page_control.navigate_to_page('mine');
                    },1000);
                }



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
