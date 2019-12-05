
define(function(require,exports,module){
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var model = require('../detail/model');
    var payment_view = require('./view');
    var utility = require('../../common/utility');

    page_control.add_page([function()
    {
        return{
            title:'活动支付',
            route:
            {
                'act/payment/:event_id/:enroll_id(/:event_params)' : 'act/payment'
            },
            dom_not_cache : true,
            ignore_exist : true,
            transition_type : 'slide',
            page_init : function(appsPageView, appsParams,applyInfo)
            {
                var self = this;

                // 设置该状态下Android页面返回禁止
                utility.set_no_page_back(utility.getHash());

                var event_id = appsParams[0];
                var enroll_id = appsParams[1];
                var event_params = appsParams[2];

                var model_obj = new model({event_id:event_id});

                if(event_params)
                {
                    event_params = decodeURIComponent(event_params);
                    event_params = eval('('+event_params+')');

                    console.log(event_params)

                    model_obj.set(event_params);
                }

                self.payment_view_obj = new payment_view
                ({
                    parentNode : self.$el,
                    model : model_obj,
                    enroll_id : enroll_id,
                    event_params : event_params
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