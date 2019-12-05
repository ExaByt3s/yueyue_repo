define(function(require, exports, module)
{
    var global_config = require('../../common/global_config');
    var model = require('./model');
    var collection = require('../../common/collection');

    module.exports = collection.extend
    ({
        model : model,
        url : global_config.ajax_url.model_card,
        /**
         * 事件安装
         * @private
         */
        _setup_events : function()
        {
            var self = this;

            self.on('complete:fetch', function(xhr, status)
            {
                //delete self._fetch_xhr;
            });

            self.fetch
            ({
                url : self.url,
                data : {},
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
        parse : function(response)
        {

            //console.log(response)
            if(response.result_data)
            {
                return response.result_data.data
            }

            return response;
        },
        initialize : function(options)
        {
            options = options || {};

            var self = this;

            self._setup_events();

        },
        get_list : function()
        {
            var self = this;

            // self.fetch
            // ({
            //     url : self.url,
            //     data : {},
            //     cache : false,
            //     beforeSend : function(xhr,options)
            //     {
            //         self.trigger('before:fetch',xhr,options);
            //     },
            //     success : function(collection, response, options)
            //     {
            //         self.trigger('success:fetch',response,options);
            //     },
            //     error : function(collection, response, options)
            //     {
            //         self.trigger('error:fetch',response,options)
            //     },
            //     complete : function(xhr,status)
            //     {
            //         self.trigger('complete:fetch',xhr,status);
            //     }
            // });

            //return self;
        }
    });

});