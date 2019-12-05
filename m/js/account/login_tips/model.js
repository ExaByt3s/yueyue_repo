/**
 * 基础页面框架
 * 汤圆
 * @param  {[type]} require
 * @param  {[type]} exports
 * @param  {[type]} module
 * @return {[type]}
 */
define(function(require, exports, module)
{
    //基础框架
    var global_config = require('../../common/global_config');
    var Backbone = require('backbone');

    module.exports = Backbone.Model.extend(
    {
        // idAttribute : 'location_id',
        default:
        {
            // "location_id": "",
            // "location_name": ""
        },

        /**
         * 安装事件
         * @private
         */
        setup_events: function() {

        },

        parse: function(response)
        {
            if (response.result_data)
            {
                return response.result_data
            }
            return response;
        },

        // 初始化，传递参数
        initialize: function(options)
        {
            var self = this;
            self.setup_events();

            // 传递参数
            // console.log(options);
        },

         //获取数据请求
        get_list: function()
        {
            var self = this;
            self.fetch(
            {
                url: self.url,
                data:{

                },
                cache: false,
                beforeSend: function(xhr, options)
                {
                    self.trigger('before:fetch', xhr, options);
                },
                success: function(collection, response, options)
                {
                    self.trigger('success:fetch', response, options);
                },
                error: function(collection, response, options)
                {
                    self.trigger('error:fetch', response, options)
                },
                complete: function(xhr, status)
                {
                    self.trigger('complete:fetch', xhr, status);
                }
            });
        }

    });
});