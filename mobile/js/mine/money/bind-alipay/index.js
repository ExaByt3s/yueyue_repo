/**
 * 绑定支付宝
 * 汤圆 2014.11.20       
 */
define(function(require, exports, module) 
{
    var $ = require('$');
    var page_control = require('../../../frame/page_control');
    var utility = require('../../../common/utility');
    var APP = require('../../../common/I_APP');
    var m_alert = require('../../../ui/m_alert/view');
    var bind_alipay_view = require('../bind-alipay/view');
    var bind_alipay_model = require('./model');

    var limit_model = require('../limit_money/model');
    var limit_view = require('../limit_money/view');

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

                if(!utility.auth.is_login())
                {
                    if(App.isPaiApp)
                    {
                        App.openloginpage(function(data)
                        {
                            if(data.code == '0000')
                            {
                                utility.refresh_page();
                            }
                        });
                    }
                    else
                    {
                        page_control.navigate_to_page('account/login');
                    }

                    return;
                }

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
                            var model = new bind_alipay_model();

                            self.bind_alipay_view_obj = new bind_alipay_view
                            ({
                                model : model,
                                parentNode : self.$el
                            }).render();
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
                var self = this;

                if(self.bind_alipay_view_obj)
                {
                    setTimeout(function()
                    {
                        self.bind_alipay_view_obj.view_scroll_obj.refresh();
                        self.bind_alipay_view_obj.view_scroll_obj.force_load_img();

                    }, 500);
                }


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

