/**
 *
 */
define(function(require, exports, module)
{
    var global_config = require('../../common/global_config');
    var collection = require('../../common/collection');
    var utility = require('../../common/utility');
    var tip = require('../../ui/m_alert/view');

    module.exports = collection.extend
    ({
        url : global_config.ajax_url.hot,
        initialize : function(options)
        {
            var self = this;

            self._setup_events();

            //页码设置
            self.pages = {
                'home_page' : 0,//这个只拿一次，下面的第一次时已近是已所以初始为1
                'hot_model' : 1,
                'comment_score_top' : 1
            }

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
                self._fetch_xhr = xhr;
                self.set_state('sending');
            });
            self.on('complete:fetch', function(response, xhrOptions) {
                (self.get_state !='stopRequest') && self.set_state('free');
                delete self._fetch_xhr;
            });

            self.on('error:add:fetch', function(response, xhrOptions) {

            });
            self.on('success:fetch', function(response, xhrOptions) {
                if(response.result_data.code){
                    if (xhrOptions.reset) {
                        self.set_current_page(1,response.result_data.type);
                    } else {
                        var current_page = self.get_current_page(response.result_data.type);
                        self.set_current_page(++current_page,response.result_data.type); // 更新当前页数
                    }
                }
            });

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

        get_datas: function(options) {
            var self = this;
//            if(utility.storage.get('location')){
//                tip.show('数据异常','loading',{
//                    delay:1000
//                });
//                return;
//            }
            var location_id = utility.storage.get('location') && utility.storage.get('location').location_id
            var data = {
                location_id : location_id,
                type : options.type || 'home_page',
                page : options.page || 1
            }

            self.fetch({
                remove: options.page === 1,
                reset: options.page === 1,
                data : data,
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
         * @returns {exports}
         */
        set_state: function(s) {
            var self = this;

            self._send_state = s;

            return self;
        },

        /**
         * 初始化页码
         */
        default_page : function(){
            //页码设置
            this.pages = {
                'home_page' : 0,//这个只拿一次，下面的第一次时已近是已所以初始为1
                'hot_model' : 1,
                'comment_score_top' : 1
            }
        },

        /**
         * 获取当前页码
         * @returns {*|number}
         */
        get_current_page : function(style) {
            return this.pages[style] || 0;
        },


        /**
         * 设置当前页码
         * @param page
         * @param style
         */
        set_current_page : function(page,style) {
            page = utility.int(page);
            page && (this.pages[style] = page);
        }
    });
});

