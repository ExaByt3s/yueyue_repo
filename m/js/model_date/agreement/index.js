/**
 * 首页 - 城市选择 基础页面框架
 * 汤圆 2014.8.18
 * @param  {[type]} require 
 * @param  {[type]} exports 
 * @param  {[type]} module  
 * @return {[type]}         
 */
define(function(require, exports, module) 
{
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var agreement_view = require('../agreement/view');
    var utility = require('../../common/utility');

	page_control.add_page([function()
    {
        return{
            title : '拍摄协议',
            route :
            {
                'model_date/agreement' : 'agreement'
            },
            dom_not_cache : false,
            transition_type : 'slide',
            initialize : function()
            {

            },
            page_init : function(page_view,route_params_arr,route_params_obj)
            {   
         
                var self = this;
                self.agreement_view_obj = new agreement_view
                ({
                    parentNode : self.$el
                }).render();
            },
            page_before_show : function()
            {
               
                var self = this;

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

