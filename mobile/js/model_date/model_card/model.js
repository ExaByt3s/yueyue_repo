/**
 * 模特卡 模型
 * 汤圆 2014.8.21
 */
define(function(require, exports, module)
{
    var global_config = require('../../common/global_config');
    var Backbone = require('backbone');

    module.exports = Backbone.Model.extend
    ({

        idAttribute : 'user_id',
        url : global_config.ajax_url.model_card,
        default :
        {
            "user_id": "",
            "chest": "",
            "cup": "",
            "waist": "",
            "hip": "",
            "height": "",
            "weight": "",
            "intro": "",
            "honor": "",
            "cameraman_require": "",
            "location_id": "",
            "city_name": "",
            "user_name": "",
            "score": 0,
            "stars": 0,
            "take_photo_times": 0,
            "user_icon": "",
            model_pic : [],
            model_type : [],
            model_style : []
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
            
            if(response.result_data)
            {
                return response.result_data.data
            }

            return response;
        },
        initialize : function()
        {

            var self = this;
            var user_id = self.get('user_id');
            self.user_id = user_id ;
            self._setup_events();
        },
        get_info : function()
        {
            var self = this;

            if(self._get_info_xhr)
            {
                return
            }

            self.fetch
            ({
                
                url : self.url,
                data : 'user_id='+self.user_id,
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
        },

        /**
         * 获得模特卡基础数据
         */
        get_base_info : function()
        {
            var self = this;

            self.fetch
            ({

                url : global_config.ajax_url.get_model_card_base_info,
                //data : 'user_id='+self.user_id,
                //cache : false,
                beforeSend : function(xhr,options)
                {
                    self.trigger('before:get_base_info:fetch',xhr,options);
                },
                success : function(collection, response, options)
                {
                    self.trigger('success:get_base_info:fetch',response,options);
                },
                error : function(collection, response, options)
                {
                    self.trigger('error:get_base_info:fetch',response,options)
                },
                complete : function(xhr,status)
                {
                    self.trigger('complete:get_base_info:fetch',xhr,status);
                }
            });
        },

        /**
         * /更新头像
         * @param key
         * @param val
         * @param options
         * @return {*}
         */
        update_avater : function(key, val, options) {
                var self = this;

                var attrs;
                if (key == null || typeof key === 'object') {
                    attrs = key;
                    options = val;
                } else {
                    (attrs = {})[key] = val;
                }

                options || (options = {});

                options.url = global_config.ajax_url.save_model_card;
                var paramData = [];
                for (var key in attrs) {
                    paramData.push(key + '=' + attrs[key]);
                }
                options.data = paramData.join('&');
                options.beforeSend = function(xhr, options) {
                    self.trigger('before:update_avater:save', xhr, options);
                };
                options.success = function(model, response, options) {
                    self.trigger('success:update_avater:save', response, options);
                };
                options.error = function(model, xhr, options) {
                    self.trigger('error:update_avater:save', model, xhr, options);
                };
                options.complete = function(xhr, status) {
                    self.trigger('complete:update_avater:save', xhr, status);
                };

                self.save(attrs, options);

                return self;
            },


        /**
         * /保存模特信息
         * @param key
         * @param val
         * @param options
         * @return {*}
         */
        save_model_card_info: function(data) {

            var self = this;

            self.fetch
            ({

                url : global_config.ajax_url.save_model_card,
                data : data,
                type: 'POST',
                //cache : false,
                beforeSend : function(xhr,options)
                {
                    self.trigger('before:update_info:save',xhr,options);
                },
                success : function(collection, response, options)
                {
                    self.trigger('success:update_info:save',response,options);
                },
                error : function(collection, response, options)
                {
                    self.trigger('error:update_info:save',response,options)
                },
                complete : function(xhr,status)
                {
                    self.trigger('complete:update_info:save',xhr,status);
                }
            });

            return self;
        },

        /*save_model_card_info: function(key, val, options) {
            var self = this;

            var attrs;
            if (key == null || typeof key === 'object') {
                attrs = key;
                options = val;
            } else {
                (attrs = {})[key] = val;
            }

            options || (options = {});

            options.url = global_config.ajax_url.save_model_card;
            var paramData = [];
            for (var key in attrs) {
                paramData.push(key + '=' + attrs[key]);
            }
            options.data = paramData.join('&');
            options.beforeSend = function(xhr, options) {
                self.trigger('before:update_info:save', xhr, options);
            };
            options.success = function(model, response, options) {
                self.trigger('success:update_info:save', response, options);
            };
            options.error = function(model, xhr, options) {
                self.trigger('error:update_info:save', model, xhr, options);
            };
            options.complete = function(xhr, status) {
                self.trigger('complete:update_info:save', xhr, status);
            };


            self.save(attrs, options);

            return self;
        },*/

        follow_request : function(data)
        {
            var self = this;

            self.fetch
            ({
                url : global_config.ajax_url.follow_user,
                data : data,
                cache : false,
                beforeSend : function(xhr,options)
                {
                    self.trigger('before:fetch_follow',xhr,options);
                },
                success : function(collection, response, options)
                {
                    self.trigger('success:fetch_follow',response,options);
                },
                error : function(collection, response, options)
                {
                    self.trigger('error:fetch_follow',response,options)
                },
                complete : function(xhr,status)
                {
                    self.trigger('complete:fetch_follow',xhr,status);
                }
            })
        },
        unfollow_request : function(data)
        {
            var self = this;

            self.fetch
            ({
                url : global_config.ajax_url.follow_user,
                data : data,
                cache : false,
                beforeSend : function(xhr,options)
                {
                    self.trigger('before:fetch_unfollow',xhr,options);
                },
                success : function(collection, response, options)
                {
                    self.trigger('success:fetch_unfollow',response,options);
                },
                error : function(collection, response, options)
                {
                    self.trigger('error:fetch_unfollow',response,options)
                },
                complete : function(xhr,status)
                {
                    self.trigger('complete:fetch_unfollow',xhr,status);
                }
            })
        },
        judge_can_date : function(data)
        {
            var self = this;

            self.fetch
            ({
                url : global_config.ajax_url.check_cameraman_require,
                data : data,
                cache : false,
                beforeSend : function(xhr,options)
                {
                    self.trigger('before:judge_can_date',xhr,options);
                },
                success : function(collection, response, options)
                {
                    self.trigger('success:judge_can_date',response,options);
                },
                error : function(collection, response, options)
                {
                    self.trigger('error:judge_can_date',response,options)
                },
                complete : function(xhr,status)
                {
                    self.trigger('complete:judge_can_date',xhr,status);
                }
            })
        }
    });
});