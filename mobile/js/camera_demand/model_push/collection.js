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
    var collection = require('../../common/collection');

    //当前页引用
    var model = require('./model');

    module.exports = collection.extend(
    {
        model: model,
        url: global_config.ajax_url.requirement_model_list,
        setup_events: function()
        {
            var self = this;

            self.on('before:fetch', function(xhr, options)
                {
                    if (options.reset)
                    {
                        // 设置页数
                        self.set_current_page(1);
                    }

                    self._fetch_xhr = xhr;
                    self.set_state('sending');

                })
                .on('success:fetch', function(response, options)
                {
                    /// 更新当前页数
                    if (!response.code && !options.reset)
                    {
                        var current_page = self.get_current_page();
                        self.set_current_page(++current_page);
                    }
                    self.has_next_page = response.result_data.has_next_page;
                })
                .on('complete:fetch', function(xhr, status)
                {
                    self.set_state('complete');
                    delete self._fetch_xhr;
                });
        },
        parse: function(response)
        {
            if (response.result_data)
            {
                return response.result_data.list
            }
            return response;
        },
        initialize: function(options)
        {
            var self = this;

            self.setup_events();
            self.set_attrs(options);

            // 初始化，传递参数
            // console.log(options);
        },
        get_attrs: function()
        {
            var self = this;
            return self.attrs;
        },
        set_attrs: function(data)
        {
            var self = this;
            self.attrs = $.extend({}, self.attrs, data);
        },
        /**
         * 获取状态
         */
        get_state: function()
        {
            var self = this;
            return self._state || false;
        },
        /**
         * 设置状态
         * @param state
         */
        set_state: function(state)
        {
            var self = this;
            self._state = state;
        },
        /**
         * 获取当前页数
         */
        get_current_page: function()
        {
            var self = this;
            return this._curren_page || 1;
        },
        /**
         * 设置当前页数
         * @param page
         */
        set_current_page: function(page)
        {
            var self = this;
            this._curren_page = page;
        },
        /**
         * 获取请求参数
         */
        get_rqs_params: function()
        {
            var self = this;
            return self._rqs_params ||{};
        },
        /**
         * 设置请求参数
         * @param data
         */
        set_rqs_params: function(data)
        {
            var self = this;
            self._rqs_params = $.extend({}, self._rqs_params, data);
        },
        
        //获取数据请求
        get_list: function(page)
        {
            var self = this;
            var self = this;
            if (self.get_state() == 'sending')
            {
                // 发送中状态不做重复请求
                return;
            }
            if (page < 1)
            {
                // 非法页数
                throw new Error('page is invalid');
            }

            self.fetch(
            {
                url: self.url,
                remove: page == 1,
                reset: page == 1,
                data: self.get_rqs_params(),
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