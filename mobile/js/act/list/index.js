/**
 * Created by nolest on 2014/8/30.
 */
/**
 * 首页 - 活动列表
 */
define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var this_view = require('./view');
    var this_model = require('../model');
    var this_collection = require('../collection');
    var App = require('../../common/I_APP');
    var utility = require('../../common/utility');

    page_control.add_page([function()
    {
        return{
            title : '活动列表',
            route :
            {
                'act/list' : 'act/list'
            },
            transition_type : 'none',
            initialize : function()
            {
                var self = this;

                //默认选项
                self.default_options =
                {
                    page : 1,
                    time_querys : "",
                    price_querys : "",
                    start_querys : ""
                };

                var model = new this_model
                ({

                });

                var collection = new this_collection
                ({
                    model : model,
                    default_options : self.default_options
                });


                self.view = new this_view
                ({
                    model : model,
                    collection : collection,
                    parentNode : self.$el

                }).render();

            },
            page_init : function(page_view,route_params_arr,route_params_obj)
            {
                console.log(route_params_arr)
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

                            self.view && self.view.collection.get_list();
                        }
                    })
                }

            },
            page_show : function()
            {

            },
            page_before_hide : function()
            {

            },
            page_hide : function()
            {
                var self = this;
                //换页前清理下拉菜单
                self.view.select_drop_obj.stay();
            }
        }
    }]);

})

