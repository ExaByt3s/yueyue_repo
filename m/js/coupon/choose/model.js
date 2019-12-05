/**
 * Created by nolestLam on 2015/3/5.
 */
define(function(require, exports, module)
{
    var global_config = require('../../common/global_config');
    var Backbone = require('backbone');

    module.exports = Backbone.Model.extend
    ({

        url : global_config.ajax_url.model_card,
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

            self.on('before:fetch', function(xhr, xhrOptions)
            {
                self._get_info_xhr = xhr;
            })
                .on('success:fetch', function(xhr, xhrOptions)
                {

                })
                .on('complete:fetch', function(xhr, status)
                {
                    delete self._get_info_xhr;
                });

        },
        /**
         * 转换数据格式
         * @param response
         * @returns {*}
         */
        parse : function(response)
        {

        },
        initialize : function(options)
        {

        },
        get_info : function()
        {
            var self = this;

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
        }
    });
});