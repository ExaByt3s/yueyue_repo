/**
 * 主题 模型
 * hudw 2014.10.27
 */
define(function(require, exports, module)
{
    var global_config = require('../common/global_config');
    var Backbone = require('backbone');

    module.exports = Backbone.Model.extend
    ({

        default :
        {

        },

        /**
         * 安装事件
         * @private
         */
        _setup_events : function()
        {
            var self = this;



        },
        /**
         * 转换数据格式
         * @param response
         * @returns {*}
         */
        parse : function(response)
        {
            
            if(response.result_data)
            {
                return response.result_data.data
            }

            return response;
        },
        initialize : function()
        {

            var self = this;

            self._setup_events();
        },
        /**
         * 获取主题详细页
         */
        get_info : function(topic_id)
        {
            var self = this;

            self.fetch
            ({
                
                url : global_config.ajax_url.get_topic_info,
                data :
                {
                    id : topic_id
                },
                cache : true,
                beforeSend : function(xhr,options)
                {
                    self.trigger('before:info:fetch',xhr,options);
                },
                success : function(collection, response, options)
                {
                    self.trigger('success:info:fetch',response,options);
                },
                error : function(collection, response, options)
                {
                    self.trigger('error:info:fetch',response,options)
                },
                complete : function(xhr,status)
                {
                    self.trigger('complete:info:fetch',xhr,status);
                }
            });
        }


    });
});