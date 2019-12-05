/**
 * Created by nolestLam on 2015/3/31.
 */
define(function(require, exports, module)
{
    var global_config = require('../../../common/global_config');
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

            self.on('before:fetch', function(xhr, xhrOptions)
            {

            })
                .on('success:fetch', function(xhr, xhrOptions)
                {

                })
                .on('complete:fetch', function(xhr, status)
                {

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
        get_level_detail : function()
        {
            var self = this;

            self.fetch
            ({
                url : global_config.ajax_url.level_detail,
                cache : false,
                beforeSend : function(xhr,options)
                {
                    self.trigger('before:authentication_detail:fetch',xhr,options);
                },
                success : function(collection, response, options)
                {
                    self.trigger('success:authentication_detail:fetch',response,options);
                },
                error : function(collection, response, options)
                {
                    self.trigger('error:authentication_detail:fetch',response,options)
                },
                complete : function(xhr,status)
                {
                    self.trigger('complete:authentication_detail:fetch',xhr,status);
                }
            });
        },
        add_authentication_pic : function(data)
        {
            var self = this;

            self.fetch
            ({
                url : global_config.ajax_url.add_id,
                data : data,
                cache : false,
                beforeSend : function(xhr,options)
                {
                    self.trigger('before:authentication_pic:fetch',xhr,options);
                },
                success : function(collection, response, options)
                {
                    self.trigger('success:authentication_pic:fetch',response,options);
                },
                error : function(collection, response, options)
                {
                    self.trigger('error:authentication_pic:fetch',response,options)
                },
                complete : function(xhr,status)
                {
                    self.trigger('complete:authentication_pic:fetch',xhr,status);
                }
            });
        }
    });
});