/**
 * 封面
 * hudw 2014.9.10
 * @param  {[type]} require
 * @param  {[type]} exports
 * @param  {[type]} module
 * @return {[type]}
 */
define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var utility = require('../../common/utility');
    var front_view = require('./view');

    page_control.add_page([function()
    {
        return{
            title : '约约',
            route :
            {
                'account/front' : 'account/front'
            },
            //transition_type : 'fade',
            dom_not_cache : true,
            ignore_exist : true,
            transition_type : 'slide',
            initialize : function()
            {

            },
            page_init : function(page_view,route_params_arr,route_params_obj)
            {
                var self = this;

                var data = route_params_obj;



                if(utility.is_empty(data))
                {
                    // 预留判断空参数
                }

                var view = new front_view
                ({
                    parentNode : self.$el,
                    data : data,
                    model : utility.user
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

