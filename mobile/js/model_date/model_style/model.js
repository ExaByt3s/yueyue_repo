define(function(require, exports, module)
{
    var global_config = require('../../common/global_config');
    var Backbone = require('backbone');

    module.exports = Backbone.Model.extend
    ({

        idAttribute : 'user_id',
        url : global_config.ajax_url.model_style,
        default :
        {
            "user_id": ""
        },
        _setup_events : function()
        {

        },
        parse : function(response)
        {
            return response;
        },
        initialize : function(options)
        {
            var self = this;

            self.user_id = options.user_id;

        },
        get_info : function()
        {
            var self = this;

            self.fetch
            ({

                url : self.url,
                data :
                {
                    user_id :self.user_id
                },
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
        }
    });
});