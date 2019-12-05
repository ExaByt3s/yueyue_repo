/**
 * 首页 - 留言列表
 * 汤圆 
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
    var list_view = require('../list/view');
    var list_collection = require('../list/collection');

	page_control.add_page([function()
    {
        return{
            title : '用户评价',
            route :
            {
                'comment/list/:role/:id(/:is_can_back)' : 'comment/list'
            },
            dom_not_cache : true,
            ignore_exist : true,
            transition_type : 'slide',
            initialize : function()
            {

            },
            page_init : function(page_view,route_params_arr,route_params_obj)
            {
                var self = this;
                var role = route_params_arr[0]; 
                var id = route_params_arr[1]; 
                var is_can_back = utility.int(route_params_arr[2]);

                if(is_can_back)
                {
                    // 设置该状态下Android页面返回禁止
                    utility.set_no_page_back(utility.getHash());
                }

                var from_app = utility.get_url_params(window.location.search,'from_app');

                var collection = new list_collection({

                    role : role,
                    id : id 
                });

                var list_view_obj = new list_view
                ({
                    from_app : utility.int(from_app),
                    is_can_back : is_can_back,
                    collection : collection,
                    parentNode : self.$el
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

