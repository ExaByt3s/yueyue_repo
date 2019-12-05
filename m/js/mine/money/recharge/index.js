/**
 * 充值
 * 汤圆 
 * @param  {[type]} require 
 * @param  {[type]} exports 
 * @param  {[type]} module  
 * @return {[type]}         
 */
define(function(require, exports, module) 
{
    var $ = require('$');
    var utility = require('../../../common/utility');
    var page_control = require('../../../frame/page_control')
    var recharge_view = require('../recharge/view');


	page_control.add_page([function()
    {
        return{
            title : '信用金',
            route :
            {
                'mine/money/recharge(/:redirect)' : 'mine/money/recharge'
            },
            dom_not_cache : true,
            ignore_exist : true,
            transition_type : 'slide',
            page_init : function(page_view,route_params_arr,route_params_obj)
            {
                var self = this;

                // 回链
                var redirect = decodeURIComponent(route_params_arr[0]);

                var location = window.location;

                var redirect_url = location.origin+"/mobile/"+window._page_mode+"#"+redirect;

                console.log('redirect_url:'+redirect_url)

                var recharge_view_obj = new recharge_view
                ({
                    parentNode : self.$el,
                    redirect : redirect,
                    templateModel :
                    {
                        from_new : 0
                    }
                }).render();

                //page_view.view = recharge_view_obj;
            },
            page_before_show : function()
            {

            },
            page_show : function()
            {

            },
            page_before_hide : function()
            {
                if(utility.user)
                {
                    utility.user.off('before:send_rechare:fetch');
                    utility.user.off('success:send_rechare:fetch');
                    utility.user.off('error:send_rechare:fetch');
                }
            },
            window_change : function(page_view)
            {
                //page_view.view.reset_scroll_height()
                //self.view.reset_scroll_height()
            }
        }
    }]);

})

