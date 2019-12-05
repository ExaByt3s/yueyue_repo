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
    var App = require('../common/I_APP');
    var utility = require('../common/utility');

    //当前页引用
    var current_page_view = require('./view');

    var model = require('./model');
    
    //var current_page_collection = require('./collection');

    page_control.add_page([function()
    {
        return {
            title: '发起约拍',
            route:
            {
                'demand(/:id)': 'demand'
            },
            dom_not_cache: true,
            ignore_exist: true,
            transition_type: 'slide',
            initialize: function() {},
            page_init: function(page_view, route_params_arr, route_params_obj)
            {
                var self = this;

                // 设置该状态下Android页面返回禁止
                utility.set_no_page_back(utility.getHash());

                // 传递参数
                self.id = route_params_arr[0];

                self.model = new model({
                    // param_a : "test"
                });

                self.view = new current_page_view(
                {
                    id : self.id,
                    model: self.model,
                    parentNode: self.$el

                }).render();
            },
            page_before_show : function()
            {
                var self = this;

                if(App.isPaiApp)
                {
                    App.check_login(function(ret)
                    {
                        console.log('====== act list check_login ======');
                        console.log(ret);

                        var local_location_id = utility.int(utility.storage.get("location_id"));

                        var client_location_id = utility.int(ret.locationid);

                        if(local_location_id != client_location_id)
                        {
                            utility.storage.set('location_id',client_location_id);
                        }
                    })
                }

            },
            page_show: function() {},
            page_before_hide: function() {}
        }
    }]);
})
