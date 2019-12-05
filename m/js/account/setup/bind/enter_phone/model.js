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
        send_bind_request : function(data)
        {
            var self = this;

            self.fetch
            ({
                url : self.url,
                data :data,
                cache : false,
                beforeSend : function(xhr,options)
                {
                    self.trigger('before:fetch_bind',xhr,options);
                },
                success : function(collection, response, options)
                {
                    self.trigger('success:fetch_bind',response,options);
                },
                error : function(collection, response, options)
                {
                    self.trigger('error:fetch_bind',response,options)
                },
                complete : function(xhr,status)
                {
                    self.trigger('complete:fetch_bind',xhr,status);
                }
            });
        },
        send_login_setp_one : function(data)
        {
            var self = this;

            self.fetch
            ({
                url : self.url,
                data :data,
                cache : false,
                beforeSend : function(xhr,options)
                {
                    self.trigger('before:fetch_send_login_setp',xhr,options);
                },
                success : function(collection, response, options)
                {
                    self.trigger('success:fetch_send_login_setp',response,options);
                },
                error : function(collection, response, options)
                {
                    self.trigger('error:fetch_send_login_setp',response,options)
                },
                complete : function(xhr,status)
                {
                    self.trigger('complete:fetch_send_login_setp',xhr,status);
                }
            });
        },
        send_setup_bind_phone : function(data)
        {
            //个人中心的设置中绑定手机、不需要提交密码
            var self = this;

            self.fetch
            ({
                url : self.url,
                data :data,
                cache : false,
                beforeSend : function(xhr,options)
                {
                    self.trigger('before:send_setup_bind_phone',xhr,options);
                },
                success : function(collection, response, options)
                {
                    self.trigger('success:send_setup_bind_phone',response,options);
                },
                error : function(collection, response, options)
                {
                    self.trigger('error:send_setup_bind_phone',response,options)
                },
                complete : function(xhr,status)
                {
                    self.trigger('complete:send_setup_bind_phone',xhr,status);
                }
            });
        }
    });
});
