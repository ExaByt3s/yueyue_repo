/**
 * 绑定支付宝
 * 汤圆 2014.11.20       
 */
define(function(require, exports, module)
{
    var global_config = require('../../../common/global_config');
    var Backbone = require('backbone');

    module.exports = Backbone.Model.extend
    ({
        url : global_config.ajax_url.bind_alipay,
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
        check_bind : function(data)
        {
            var self = this;
            self.fetch
            ({
                url : self.url,
                data : data,
                cache : false,
                beforeSend : function(xhr,options)
                {
                    self.trigger('before:check_bind:fetch',xhr,options);
                },
                success : function(model, response, options)
                {
                    self.trigger('success:check_bind:fetch',response,options);
                },
                error : function(model, response, options)
                {
                    self.trigger('error:check_bind:fetch',response,options)
                },
                complete : function(xhr,status)
                {
                    self.trigger('complete:check_bind:fetch',xhr,status);
                }
            });
        },

        //发送绑定
        send_bind : function(data)
        {
            var self = this;
            self.fetch
            ({
                url : self.url,
                data : data ,
                cache : false,
                beforeSend : function(xhr,options)
                {
                    self.trigger('before:send_bind:fetch',xhr,options);
                },
                success : function(model, response, options)
                {
                    self.trigger('success:send_bind:fetch',response,options);
                },
                error : function(model, response, options)
                {
                    self.trigger('error:send_bind:fetch',response,options)
                },
                complete : function(xhr,status)
                {
                    self.trigger('complete:send_bind:fetch',xhr,status);
                }
            });
        }

    });
});