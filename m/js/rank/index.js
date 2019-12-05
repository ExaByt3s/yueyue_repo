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
    var page_control = require('../frame/page_control');
    var I_App = require('../common/I_APP');
    var utility = require('../common/utility');

    //当前页引用
    var current_page_view = require('./view');

    // var model = require('./model');
    
    var current_page_collection = require('./collection');

    page_control.add_page([function()
    {
        return {
            title: '模特榜',
            route:
            {
                'rank/:type': 'rank'
            },
            dom_not_cache: false,
            transition_type: 'slide',
            initialize: function() {},
            page_init: function(page_view, route_params_arr, route_params_obj)
            {
                var self = this;

                // 传递参数
                var type = route_params_arr[0];

                self.collection = new current_page_collection({
                    type : type
                });

                self.view = new current_page_view(
                {
                    type : type,
                    collection: self.collection,
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
