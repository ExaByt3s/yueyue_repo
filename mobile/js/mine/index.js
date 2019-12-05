/**
 * 我的
 * hudw 2014.8.30
 * @param  {[type]} require 
 * @param  {[type]} exports 
 * @param  {[type]} module  
 * @return {[type]}         
 */
define(function(require, exports, module) 
{
    var $ = require('$');
    var page_control = require('../frame/page_control');
    var mine = require('../mine/model');
    var mine_view = require('../mine/view');
    var utility = require('../common/utility');
    var App = require('../common/I_APP');

	page_control.add_page([function()
    {
        return{
            title : '我的',
            route :
            {
                'mine(/:payment_no)' : 'mine'
            },
            transition_type : 'none',
            initialize : function()
            {

            },
            page_init : function(page_view,route_params_arr,route_params_obj)
            {
                var self = this;

                var user_id = route_params_arr[0];

                if(App.isPaiApp)
                {
                    App.switchtopage({page : 'mine'});

                    return;
                }

                //角色分支
                if(utility.user.get("role") == 'model')
                {
                    utility.user.set("is_model",true);
                    utility.user.set("is_cameraman",false);
                    utility.user.set("notice_list","model_notice_list")
                }
                else
                {
                    utility.user.set("is_model",false);
                    utility.user.set("is_cameraman",true);
                    utility.user.set("notice_list","cameraman_notice_list")
                }

                self.mine_view_obj = new mine_view
                ({
                    model : utility.user,
                    parentNode : self.$el,
                    templateModel : utility.user
                }).render();

                console.log(1)
            },
            page_before_show : function()
            {

                var self = this;

                if(self.mine_view_obj && !App.isPaiApp)
                {
                    self.mine_view_obj.refresh(true);
                }
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

