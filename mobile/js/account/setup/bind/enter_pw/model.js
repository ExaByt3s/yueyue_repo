/**
 * Created by nolest on 2014/9/13.
 */

define(function(require, exports, module)
{
    var global_config = require('../../../../common/global_config');
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
        send_data : function(data)
        {
            var self = this;

            self.fetch
            ({
                url : self.url,
                data :data,
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
        get_reset_code : function(data)
        {
            var self = this;

            self.fetch
            ({
                url : global_config.ajax_url.reg,
                data :data,
                cache : false,
                beforeSend : function(xhr,options)
                {
                    self.trigger('before:get_reset_code',xhr,options);
                },
                success : function(collection, response, options)
                {
                    self.trigger('success:get_reset_code',response,options);
                },
                error : function(collection, response, options)
                {
                    self.trigger('error:get_reset_code',response,options)
                },
                complete : function(xhr,status)
                {
                    self.trigger('complete:get_reset_code',xhr,status);
                }
            });
        },
        send_change_pw : function(data)
        {
            var self = this;

            self.fetch
            ({
                url : global_config.ajax_url.reg,
                data :data,
                cache : false,
                beforeSend : function(xhr,options)
                {
                    self.trigger('before:fetch_change_pw',xhr,options);
                },
                success : function(collection, response, options)
                {
                    self.trigger('success:fetch_change_pw',response,options);
                },
                error : function(collection, response, options)
                {
                    self.trigger('error:fetch_change_pw',response,options)
                },
                complete : function(xhr,status)
                {
                    self.trigger('complete:fetch_change_pw',xhr,status);
                }
            });
        }
    });
});
