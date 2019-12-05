/**
 *
 */
define(function(require, exports, module)
{
    var global_config = require('../../common/global_config');
    var collection = require('../../common/collection');
    var Utility = require('../../common/utility');

    module.exports = collection.extend
    ({
        url : global_config.ajax_url.search_result,
        initialize : function(options)
        {
            var self = this;

            self._setup_events();

        },
        parse : function(response)
        {
            if (response.result_data) {
                if (response.result_data.list) {
                    return response.result_data.list;
                }
                return response.result_data;
            }
        },
        _setup_events : function ()
        {
            var self = this;

            self.on('before:fetch', function(xhr, xhrOptions) {
                self.set_state('sending');
            }).on('success:fetch', function(response, xhrOptions) {
                if (!response.code) {
                    //已加载完所有数据时用户再滚动停止加载数据
                    if (!response.result_data.has_next_page) {
                        // modify by hudw 2014.11.24
                        //self.set_state('stopRequest');
                    }
                    var current_page = self.get_current_page();
                    self.set_current_page(++current_page); // 更新当前页数
                }

            }).on('complete:fetch', function(xhr, status) {
                self.get_state() !='stopRequest' && self.set_state('free');

            });

        },
        get_search_result_list: function(data) {
            var self = this;


            if (self.get_state() == 'sending') {
                return self;
            }

            var param_data = {};
            param_data.nickname = data.tag;

            param_data.page = Utility.int(data.page);

            if (param_data.page < 1) {
                throw new Error('search_result page is invalid');
            }

            self.fetch({
                remove: param_data.page === 1,
                reset: param_data.page === 1,
                data: param_data,
                cache: false,
                beforeSend: function(xhr, xhrOptions) {
                    self.trigger('before:fetch',xhr, xhrOptions);
                },
                complete: function(xhr, status) {
                    self.trigger('complete:fetch', xhr, status);
                },
                success: function(collection, response, options) {
                    self.trigger('success:fetch', response, options);
                },
                error: function(collection, response, options) {
                    self.trigger('error:fetch', response, options);
                }
            });

            return self;
        },

        /**
         * 获取当前状态
         * @returns {*}
         */
        get_state: function() {
            return this._send_state;
        },

        /**
         * 设置当前状态
         * @param s
         * @returns {search_result_collection}
         */
        set_state: function(s) {
            var self = this;

            self._send_state = s;

            return self;
        },

        /**
         * 获取当前页码
         * @returns {*|number}
         */
        get_current_page: function() {
            return this._current_page || 0;
        },

        /**
         * 设置当前页码
         * @param page
         */
        set_current_page: function(page) {
            page = Utility.int(page);
            page && (this._current_page = page);
        }
    });
});

