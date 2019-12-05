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
    var checkins_view = require('../checkins/view');
    

	page_control.add_page([function()
    {
        return{
            title : '现场签到',
            route :
            {
                'mine/checkins' : 'mine/checkins'
            },
            dom_not_cache : false,
            ignore_exist: true,
            transition_type : 'slide',
            initialize : function()
            {


                var self = this;


                var checkins_view_obj = new checkins_view
                ({
                    parentNode : self.$el
                }).render();

            },
            page_init : function()
            {
                
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

