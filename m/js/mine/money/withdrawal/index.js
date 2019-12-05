/**
 * 提现
 * 汤圆 2014.9.12
 * @param  {[type]} require 
 * @param  {[type]} exports 
 * @param  {[type]} module  
 * @return {[type]}         
 */
define(function(require, exports, module) 
{
    var $ = require('$');
    var page_control = require('../../../frame/page_control');
    var utility = require('../../../common/utility');
    var m_alert = require('../../../ui/m_alert/view');
    var withdrawal_view = require('../withdrawal/view');
    var withdrawal_model = require('./model');

	page_control.add_page([function()
    {
        return{
            title : '提现',
            route :
            {
                /**
                 * type = 1 代表是提现钱包
                 * type = 0 代表是提现信用金
                 */
                'mine/money/withdrawal/:type' : 'mine/money/withdrawal'
            },
            dom_not_cache : true,
            ignore_exist : true,
            transition_type : 'slide',
            page_init : function(page_view,route_params_arr,route_params_obj)
            {
                var self = this;

                if(!utility.login_id)
                {
                    m_alert.show('尚未登录','right',{delay:1000});

                    page_control.navigate_to_page('account/login');

                    return;
                }

                var alipay_account = route_params_obj.alipay_account;


                var model = new withdrawal_model();

                var is_money = utility.int(route_params_arr[0])?1:0;

                model.set('is_money',is_money);

                self.withdrawal_view_obj = new withdrawal_view
                ({
                    model : model,
                    parentNode : self.$el,
                    templateModel : {is_money:is_money} ,
                    alipay_account : alipay_account
                }).render();

                page_view.view = self.withdrawal_view_obj
            },
            page_before_show : function()
            {

            },
            page_show : function()
            {

            },
            page_before_hide : function()
            {

            },
            window_change : function(page_view)
            {
                //page_view.view.reset_scroll_height()
                //self.view.reset_scroll_height()
            }
        }
    }]);

})

