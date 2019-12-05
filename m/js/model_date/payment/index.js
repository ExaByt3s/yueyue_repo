/**
 * 支付页面
 * hdw 20148.28
 * @param  {[type]} require 
 * @param  {[type]} exports 
 * @param  {[type]} module  
 * @return {[type]}         
 */
define(function(require, exports, module) 
{
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var payment_view = require('../payment/view');
    var payment_model = require('../payment/model');
    var utility = require('../../common/utility');
    var m_alert = require('../../ui/m_alert/view');
    var model_card = require('../model_card/model');

	page_control.add_page([function()
    {
        return{
            title : '支付',
            route :
            {
                'model_date/payment' : 'model_date/payment'
            },
            dom_not_cache : true,
            ignore_exist : true,
            transition_type : 'slide',
            initialize : function()
            {

            },
            page_init : function(page_view,route_params_arr,route_params_obj)
            {
                var self = this;

                var model_obj = null;

                var pay_ment_model_obj = null;

                var model = route_params_obj;

                //判断是否从约拍风格到达此页面
                if(model && model.cid)
                {
                    model_obj = route_params_obj;

                    // 实例化支付模型
                    pay_ment_model_obj = new payment_model();

                }
                else
                {
                    // 做容错机制交互
                    m_alert.show('订单不存在','error',{delay:1000});

                    setTimeout(function()
                    {
                        page_control.navigate_to_page('hot');
                    },1000);

                    return;


                    /*var date_info = utility.date_info.get();

                    model_obj = new model_card(date_info.model);

                    pay_ment_model_obj = new payment_model(date_info.pay_ment_model);*/


                }

                var payment_view_obj = new payment_view
                ({
                    templateModel : model_obj,
                    model : model_obj,
                    pay_model : pay_ment_model_obj,
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

            }
        }
    }]);

})

