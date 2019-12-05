/**
 * 首页 - 模特卡
 * 汤圆 2014.8.21
 * @param  {[type]} require 
 * @param  {[type]} exports 
 * @param  {[type]} module  
 * @return {[type]}         
 */
define(function(require, exports, module) 
{
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var submit_success_view = require('../submit_success/view');
    var model = require('../submit_success/model');
    var utility = require('../../common/utility');

	page_control.add_page([function()
    {
        return{
            title : '提交成功',
            page_key : 'date_submit_success',
            route :
            {
                'model_date/submit_success(/:pay_ment_id)' : 'model_date/submit_success'
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

                var from_app = utility.get_url_params(window.location.search,'from_app');

                // 设置该状态下Android页面返回禁止
                utility.set_no_page_back(utility.getHash());

                var model_obj = new model
                ({
               
                });

                var submit_success_view_obj = new submit_success_view
                ({
                    from_app : utility.int(from_app),
                    model : model_obj,
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

