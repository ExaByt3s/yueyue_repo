/**
 * 支付 模型
 * hudw 2014.8.28
 */
define(function(require, exports, module)
{
    var global_config = require('../../common/global_config');
    var utility = require('../../common/utility');
    var Backbone = require('backbone');

    module.exports = Backbone.Model.extend
    ({

        idAttribute: 'user_id',
        url: global_config.ajax_url.pay,
        default: {
            "user_id": ""
        },

        /**
         * 安装事件
         * @private
         */
        _setup_events: function () {
            var self = this;

            self.on('before:fetch', function (xhr, xhrOptions) {
                self._get_info_xhr = xhr;
            })
                .on('success:fetch', function (xhr, xhrOptions) {

                })
                .on('error:fetch', function (xhr, xhrOptions) {

                })
                .on('complete:fetch', function (xhr, status) {
                    delete self._get_info_xhr;
                });

        },
        /**
         * 转换数据格式
         * @param response
         * @returns {*}
         */
        parse: function (response) {

            if (response.result_data) {
                return response.result_data
            }

            return response;
        },
        initialize: function (options)
        {
            var self = this;

            self._setup_events();

        },
        pay: function (data)
        {
            var self = this;

            if(self._get_info_xhr)
            {
                return;
            }

            var location = window.location;

            var from_app = utility.get_url_params(window.location.search,'from_app');

            if(from_app)
            {
                var redirect_url = location.origin+"/mobile/"+window._page_mode+"?from_app=1#model_date/submit_success";
            }
            else
            {
                var redirect_url = location.origin+"/mobile/"+window._page_mode+"#model_date/submit_success";
            }

            redirect_url = encodeURIComponent(redirect_url);

            data = $.extend(data,
            {
                redirect_url : redirect_url
            });

            self.fetch
            ({

                url : self.url,
                data : data,
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