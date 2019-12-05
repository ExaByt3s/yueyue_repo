/**
 * Created by nolest on 2014/9/18.
 */

define(function(require, exports, module)
{
    var global_config = require('../../../common/global_config');
    var Backbone = require('backbone');

    module.exports = Backbone.Model.extend
    ({
        url : global_config.ajax_url.bind_act,
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
        send_code_request : function(data)
        {
            var self = this;

            self.fetch
            ({
                url : self.url,
                data :data,
                cache : false,
                beforeSend : function(xhr,options)
                {
                    self.trigger('before:fetch_code',xhr,options);
                },
                success : function(collection, response, options)
                {
                    self.trigger('success:fetch_code',response,options);
                },
                error : function(collection, response, options)
                {
                    self.trigger('error:fetch_code',response,options)
                },
                complete : function(xhr,status)
                {
                    self.trigger('complete:fetch_code',xhr,status);
                }
            });
        },
        send_phone_and_code : function(data)
        {
            var self = this;

            self.fetch
            ({
                url : self.url,
                data :data,
                cache : false,
                beforeSend : function(xhr,options)
                {
                    self.trigger('before:phone_and_code',xhr,options);
                },
                success : function(collection, response, options)
                {
                    self.trigger('success:phone_and_code',response,options);
                },
                error : function(collection, response, options)
                {
                    self.trigger('error:phone_and_code',response,options)
                },
                complete : function(xhr,status)
                {
                    self.trigger('complete:phone_and_code',xhr,status);
                }
            });
        }
    });
});
