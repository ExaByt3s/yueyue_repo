/**
 * 绑定支付宝
 * 汤圆 2014.11.20       
 */
define(function(require, exports, module) 
{
    var $ = require('$');
    var page_control = require('../../../frame/page_control');
    var utility = require('../../../common/utility');
    var m_alert = require('../../../ui/m_alert/view');
    var bind_alipay_view = require('../bind-alipay/view');
    var bind_alipay_model = require('./model');

	page_control.add_page([function()
    {
        return{
            title : '支付宝绑定',
            route :
            {
                'mine/money/bind_alipay' : 'mine/money/bind_alipay'
            },
            dom_not_cache : true,
            ignore_exist : true,
            transition_type : 'slide',
            page_init : function(page_view,route_params_arr,route_params_obj)
            {
                var self = this;

                if(!utility.login_id)
                {
                    m_alert.show('尚未登录','error',{delay:1000});

                    page_control.navigate_to_page('account/login');

                    return;
                }

                var model = new bind_alipay_model();

                self.bind_alipay_view_obj = new bind_alipay_view
                ({
                    model : model,
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

            },
            window_change : function(page_view)
            {

            }
        }
    }]);

})

