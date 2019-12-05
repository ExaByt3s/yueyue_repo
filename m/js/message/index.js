/**
 * 消息过渡页面
 * hudw 2014.11.17
 */
define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../frame/page_control');
    var I_App = require('../common/I_APP');
    var utility = require('../common/utility');
    var footer = require('../widget/footer/index');

    page_control.add_page([function()
    {
        return{
            title : '消息',
            route :
            {
                'message' : 'message'
            },
            dom_not_cache : false,
            transition_type : 'none',
            page_init : function(page_view,route_params_arr,route_params_obj)
            {
                var self = this;

                self.$el.css({'background-color':'#fff'});   

                var footer_obj = new footer
                ({
                    // 元素插入位置
                    parentNode: self.$el,
                    // 模板参数对象
                    templateModel :
                    {
                        // 高亮设置参数
                        is_msg : true
                    }
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

