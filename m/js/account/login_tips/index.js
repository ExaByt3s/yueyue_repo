/**
 * 基础页面框架
 * 汤圆  
 * @param  {[type]} require
 * @param  {[type]} exports
 * @param  {[type]} module
 * @return {[type]}
 */
define(function(require, exports, module)
{
    //基础框架
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var I_App = require('../../common/I_APP');
    var utility = require('../../common/utility');

    //当前页引用
    var current_page_view = require('./view');

    var model = require('./model');
    
    //var current_page_collection = require('./collection');

    page_control.add_page([function()
    {
        return {
            title: '登陆提示',
            route:
            {
                'account/login_tips': 'account/login_tips'
            },
            dom_not_cache: false,
            transition_type: 'slide',
            initialize: function() {},
            page_init: function(page_view, route_params_arr, route_params_obj)
            {
                var self = this;

                // 传递参数
                // var param_a = route_params_arr[0];

                self.model = new model({
                    param_a : "test"
                });

                self.view = new current_page_view(
                {
                    model: self.model,
                    parentNode: self.$el

                }).render();
            },
            page_before_show: function()
            {
                var self = this;
            },
            page_show: function() {},
            page_before_hide: function() {}
        }
    }]);
})
