/**
 * 登录
 * hudw 2014.9.3
 * @param  {[type]} require
 * @param  {[type]} exports
 * @param  {[type]} module
 * @return {[type]}
 */
define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var ua = require('../../frame/ua');
    var utility = require('../../common/utility');
    var login_view = require('./view');
    var mine = require('../../mine/model');
    var guide = require('../../ui/guide/view');
    var first_guide_tpl = require('../../ui/guide/tpl/guide_model.handlebars');

    page_control.add_page([function()
    {
        return{
            title : '登录',
            route :
            {
                'account/login(/:type)' : 'account/login'
            },
            transition_type : 'slide',
            dom_not_cache : true,
            ignore_exist : true,
            page_init : function(page_view,route_params_arr,route_params_obj)
            {
                var self = this;

                var type_params = route_params_arr[0];

                var mine_obj = new mine();

                // ios系统增加 手机号码输入
                var is_iphone = ua.isIDevice?1 : 0;

                // 选择的角色是模特
                if(window._role == 'model' )
                {
                    var login_view_obj = new login_view
                    ({
                        templateModel :
                        {
                            is_iphone : is_iphone
                        },
                        model : mine_obj,
                        parentNode : self.$el,
                        type_params : type_params
                    }).render();

                    utility.storage.set('is_frist_time_open_app',1);
                }
                else
                {
                    var login_view_obj = new login_view
                    ({
                        templateModel :
                        {
                            is_iphone : is_iphone
                        },
                        model : mine_obj,
                        parentNode : self.$el,
                        type_params : type_params
                    }).render();
                }





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

