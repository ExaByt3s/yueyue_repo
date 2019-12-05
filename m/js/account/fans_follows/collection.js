define(function(require, exports, module)
{
    var global_config = require('../../common/global_config');
    var model = require('./model');
    var collection = require('../../common/collection');
    var m_alert = require('../../ui/m_alert/view');

    module.exports = collection.extend
    ({
        model : model,
        url : global_config.ajax_url.fans_or_follow,
        has_next_page : false,
        /**
         * 事件安装
         * @private
         */
        _setup_events : function()
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
            }).on('success:fetch', function(response, options)
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
        parse : function(response)
        {

            if(response.result_data.list)
            {
                
                return response.result_data.list;
            }

            return response;
        },
        initialize : function(options)
        {
            options = options || {};

            var self = this;
            
            self._setup_events();

            self.set_attrs(options);

            type = options.type;

            user_id = options.user_id;




        },
        get_attrs : function()
        {
            var self = this;

            return self.attrs;
        },
        set_attrs : function(data)
        {
            var self = this;

            self.attrs = $.extend({},self.attrs,data);
        },
        /**
         * 获取状态
         */
        get_state : function()
        {
            var self = this;

            return self._state || false;
        },
        /**
         * 设置状态
         * @param state
         */
        set_state : function(state)
        {
            var self = this;

            self._state = state;
        },
        /**
         * 获取当前页数
         */
        get_current_page : function()
        {
            var self = this;

            return this._curren_page || 1;
        },
        /**
         * 设置当前页数
         * @param page
         */
        set_current_page : function(page)
        {
            var self = this;

            this._curren_page = page;
        },
        /**
         * 获取请求参数
         */
        get_rqs_params : function()
        {
            var self = this;

            return self._rqs_params || {};
        },
        /**
         * 设置请求参数
         * @param data
         */
        set_rqs_params : function(data)
        {
            var self = this;

            self._rqs_params = $.extend({},self._rqs_params,data);
        },

        get_list : function(page)
        {

            var self = this;

            if(self.get_state() == 'sending')
            {
                // 发送中状态不做重复请求
                return;
            }

            if(page < 1)
            {
                // 非法页数
                throw new Error('page is invalid');
            }


            self.set_rqs_params
            ({
                type : type,
                page : page,
                user_id : user_id
            });


            self.fetch
            ({
                url : self.url,
                remove : page == 1,
                reset :  page == 1,
                data : self.get_rqs_params(),
                cache : false,
                beforeSend : function(xhr,options)
                {
                    self.trigger('before:fetch',xhr,options);
                },
                success : function(collection, response, options)
                {
                    var data_len = response.result_data.list.length ;
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

            return self;
        }
    });

});