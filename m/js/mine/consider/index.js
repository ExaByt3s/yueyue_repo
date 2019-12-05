/**
 * Created by nolest on 2014/9/23.
 *
 * 约拍邀请列表
 *
 */

define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var consider_view = require('./view');
    var consider_model = require('./model');
    var global_config = require('../../common/global_config');
    var utility = require('../../common/utility');
    var App = require('../../common/I_APP');
    var m_alert = require('../../ui/m_alert/view');

    page_control.add_page([function()
    {
        return{
            title : '约拍邀请',
            route :
            {
                'mine/consider(/:type)' : 'mine/consider(/:type)'
            },
            dom_not_cache : true,
            ignore_exist : true,
            transition_type : 'slide',
            page_init : function(page_view,route_params_arr,route_params_obj)
            {
                var self = this;

                //获取用户类型
                var role = utility.user.get("role");
                //融合对象
                var templateModel = [];
                //配置分支信息
                if(role == 'cameraman')
                {
                    templateModel = {role:role,is_cameraman : true};
                }
                else
                {
                    templateModel = {role:role,is_cameraman : false};
                }

                var model = new consider_model();

                var view_type;

                var can_back_to_mine = false;

                if(route_params_arr[0] == 'consider')
                {
                    view_type = 'consider';

                    can_back_to_mine = true;
                }
                else if(route_params_arr[0] == 'confirm')
                {
                    view_type = 'confirm';

                    can_back_to_mine = true;
                }
                else if (route_params_arr[0] == 'can_back_to_mine')
                {
                    view_type = 'consider';

                    can_back_to_mine = false;
                }
                else
                {
                    view_type = 'consider';

                    can_back_to_mine = true;

                }

                var view = new consider_view
                ({
                    model : model,
                    parentNode : self.$el,
                    type : view_type,
                    templateModel : templateModel,
                    can_back_to_mine : can_back_to_mine
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

