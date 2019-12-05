define(function(require, exports, module)
{
    var global_config = require('../common/global_config');
    var model = require('./model');
    var collection = require('../common/collection');

    module.exports = collection.extend
    ({
        model : model,
        url : global_config.ajax_url.get_topic_list,
        /**
         * 事件安装
         * @private
         */
        _setup_events : function()
        {
            var self = this;



        },
        parse : function(response)
        {

            //console.log(response)
            if(response.result_data)
            {
                return response.result_data.list
            }

            return response;
        },
        initialize : function(options)
        {
            options = options || {};

            var self = this;

            self._setup_events();

        },
        /**
         * 获取主题列表
         */
        get_list : function(page)
        {
            var self = this;

            self.fetch
            ({

                url : self.url,
                data :
                {
                    page : page,
                    mode : window._action_mode
                },
                reset : page === 1,
                cache : false,
                beforeSend : function(xhr,options)
                {
                    self.trigger('before:list:fetch',xhr,options);
                },
                success : function(collection, response, options)
                {
                    self.trigger('success:list:fetch',response,options);
                },
                error : function(collection, response, options)
                {
                    self.trigger('error:list:fetch',response,options)
                },
                complete : function(xhr,status)
                {
                    self.trigger('complete:list:fetch',xhr,status);
                }
            });
        }
    });

});