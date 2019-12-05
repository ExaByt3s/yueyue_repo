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
        bind_phone_code : function(data)
        {
            var self = this;

            self.fetch
            ({
                url : self.url,
                data :data,
                cache : false,
                beforeSend : function(xhr,options)
                {
                    self.trigger('before:code',xhr,options);
                },
                success : function(collection, response, options)
                {
                    self.trigger('success:code',response,options);
                },
                error : function(collection, response, options)
                {
                    self.trigger('error:code',response,options)
                },
                complete : function(xhr,status)
                {
                    self.trigger('complete:code',xhr,status);
                }
            });
        },
        bind_phone_request : function(data)
        {
            var self = this;

            self.fetch
            ({
                url : self.url,
                data :data,
                cache : false,
                beforeSend : function(xhr,options)
                {
                    self.trigger('before:bind_phone',xhr,options);
                },
                success : function(collection, response, options)
                {
                    self.trigger('success:bind_phone',response,options);
                },
                error : function(collection, response, options)
                {
                    self.trigger('error:bind_phone',response,options)
                },
                complete : function(xhr,status)
                {
                    self.trigger('complete:bind_phone',xhr,status);
                }
            });
        }
    });
});
