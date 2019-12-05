/**
 * 绑定支付宝
 * 汤圆 2014.11.20       
 */
define(function(require, exports, module)
{
    var global_config = require('../common/global_config');
    var Backbone = require('backbone');

    module.exports = Backbone.Model.extend
    ({
        url : global_config.ajax_url.report_model,
        default :
        {

        },
        /**
         * 安装事件
         * @private
         */
        _setup_events : function()
        {

        },
        parse : function(response)
        {
            if(response.result_data)
            {
                return response.result_data
            }

            return response;
        },
        initialize : function()
        {
            var self = this;

            self._setup_events();
        },

        //检查是否绑定
        send_report : function(data)
        {
            var self = this;
            self.fetch
            ({
                url : self.url,
                data : data,
                cache : false,
                beforeSend : function(xhr,options)
                {
                    self.trigger('before:fetch',xhr,options);
                },
                success : function(model, response, options)
                {
                    self.trigger('success:fetch',response,options);
                },
                error : function(model, response, options)
                {
                    self.trigger('error:fetch',response,options)
                },
                complete : function(xhr,status)
                {
                    self.trigger('complete:fetch',xhr,status);
                }
            });
        }

    });
});