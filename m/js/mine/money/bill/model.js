/**
 * Created by nolest on 2014/9/13.
 */

define(function(require, exports, module)
{
    var global_config = require('../../../common/global_config');
    var Backbone = require('backbone');

    module.exports = Backbone.Model.extend
    ({
        url : global_config.ajax_url.bill_act,
        default :
        {

        },
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
        get_bills : function(data)
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
                success : function(collection, response, options)
                {
                    self.trigger('success:fetch',response,options);
                },
                error : function(collection, response, options)
                {
                    self.trigger('error:fetch',response,options)
                },
                complete : function(xhr,status)
                {
                    self.trigger('complete:fetch',xhr,status);
                }
            });
        },


        //检查是否绑定支付宝
        check_bind : function(data)
        {
            var self = this;
            self.fetch
            ({
                url : global_config.ajax_url.withdraw,
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
        }

        
    });
});
