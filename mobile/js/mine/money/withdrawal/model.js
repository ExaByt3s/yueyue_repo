/**
 * 提现
 * 汤圆 2014.9.12
 */
define(function(require, exports, module)
{
    var global_config = require('../../../common/global_config');
    var Backbone = require('backbone');

    module.exports = Backbone.Model.extend
    ({
        url : global_config.ajax_url.withdraw,
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
        send_sms : function()
        {
            var self = this;

            self.fetch
            ({
                url : self.url,
                data :
                {
                    type : 'sms'
                },
                cache : false,
                beforeSend : function(xhr,options)
                {
                    self.trigger('before:fetch_sms',xhr,options);
                },
                success : function(collection, response, options)
                {
                    self.trigger('success:fetch_sms',response,options);
                },
                error : function(collection, response, options)
                {
                    self.trigger('error:fetch_sms',response,options)
                },
                complete : function(xhr,status)
                {
                    self.trigger('complete:fetch_sms',xhr,status);
                }
            });
        },
        submit_details : function(data)
        {
            var self = this;

            self.fetch
            ({
                url : self.url,
                data : data,
                cache : false,
                beforeSend : function(xhr,options)
                {
                    self.trigger('before:fetch_submit',xhr,options);
                },
                success : function(collection, response, options)
                {
                    self.trigger('success:fetch_submit',response,options);
                },
                error : function(collection, response, options)
                {
                    self.trigger('error:fetch_submit',response,options)
                },
                complete : function(xhr,status)
                {
                    self.trigger('complete:fetch_submit',xhr,status);
                }
            });
        }
        
        //检查是否绑定支付宝
        // check_bind : function(data)
        // {
        //     var self = this;
        //     self.fetch
        //     ({
        //         url : self.url,
        //         data : data,
        //         cache : false,
        //         beforeSend : function(xhr,options)
        //         {
        //             self.trigger('before:check_bind:fetch',xhr,options);
        //         },
        //         success : function(model, response, options)
        //         {
        //             self.trigger('success:check_bind:fetch',response,options);
        //         },
        //         error : function(model, response, options)
        //         {
        //             self.trigger('error:check_bind:fetch',response,options)
        //         },
        //         complete : function(xhr,status)
        //         {
        //             self.trigger('complete:check_bind:fetch',xhr,status);
        //         }
        //     });
        // }

    });
});