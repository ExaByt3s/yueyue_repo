/**
 * hudw 2014.11.05
 * 注册页面
 */
define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../../../frame/page_control');
    var ua = require('../../../frame/ua');
    var utility = require('../../../common/utility');
    var reg_view = require('./view');


    page_control.add_page([function()
    {
        return{
            title : '注册 - 流程1',
            route :
            {
                'account/register/reg(/:is_can_back)' : 'account/register/reg'
            },
            dom_not_cache : true,
            ignore_exist : true,
            transition_type : 'slide',
            page_init : function(page_view,route_params_arr,route_params_obj)
            {
                var self = this;

                var is_can_back = utility.int(route_params_arr[0]);

                // ios系统增加 手机号码输入
                var is_iphone = ua.isIDevice?1 : 0;

                //if(0)
                if(utility.int(is_can_back) == 1 && !utility.storage.get('is_frist_time_open_app') )
                {
                    // 设置该状态下Android页面返回禁止
                    utility.set_no_page_back(utility.getHash());

                    var reg_view_obj = new reg_view
                    ({
                        templateModel :
                        {
                            is_iphone : is_iphone
                        },
                        model : utility.user,
                        parentNode : self.$el,
                        is_can_back : is_can_back
                    }).render();

                    utility.storage.set('is_frist_time_open_app',1);
                }
                else
                {
                    var reg_view_obj = new reg_view
                    ({
                        templateModel :
                        {
                            is_iphone : is_iphone
                        },
                        model : utility.user,
                        parentNode : self.$el,
                        is_can_back : is_can_back
                    }).render();
                }


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

