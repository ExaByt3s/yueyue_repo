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


    var limit_model = require('../limit_money/model');
    var limit_view = require('../limit_money/view');

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

                

                // 检查是否机构模特
                var check_is_limit_model =  new limit_model();

                check_is_limit_model
                    .on('before:check_limit_money:fetch',function(response,options)
                    {
                        m_alert.show('加载中...','loading',{delay:1000});
                    })
                    .on('success:check_limit_money:fetch',function(response,options)
                    {

                        self.check_limit_data = response.result_data;

                        // 限制机构模特提现 view
                        if ( self.check_limit_data.limit && self.check_limit_data.limit == 1 ) 
                        {
                            self.limit_view_obj = new limit_view
                            ({
                                model : check_is_limit_model,
                                parentNode : self.$el
                            }).render();
                        }
                        else
                        {
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

                        }
                        
                    })
                    .on('error:check_limit_money:fetch',function()
                    {
                        m_alert.show('网络不给力,请返回重试！','error')
                    })
                    .on('complete:check_limit_money:fetch',function(response,options)
                    {
                        //m_alert.hide();
                    })

                //查询请求
                check_is_limit_model.get_list({
                    type : 'check_bind'
                });



               
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

