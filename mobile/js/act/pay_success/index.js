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
            title: '支付成功',
            route:
            {
                'act/pay_success/:event_id(/:payment_no)': 'act/pay_success'
            },
            dom_not_cache: true,
            ignore_exist : true,
            transition_type: 'slide',
            initialize: function() {},
            page_init: function(page_view, route_params_arr, route_params_obj)
            {
                var self = this;

                // 传递参数
                var event_id_params = parseInt( route_params_arr[0] );

                self.model = new model({
                    event_id : event_id_params
                });

                self.view = new current_page_view(
                {
                    event_id : event_id_params,
                    model: self.model,
                    parentNode: self.$el

                }).render();

                // 设置该状态下Android页面返回禁止
                utility.set_no_page_back(utility.getHash());
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
