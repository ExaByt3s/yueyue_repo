/**
 * 我的 模型
 * hdw 2014.8.29
 */
define(function(require, exports, module)
{
    var global_config = require('../common/global_config');
    var Backbone = require('backbone');
    var utility = require('../common/utility');

    module.exports = Backbone.Model.extend
    ({
        idAttribute: 'user_id',
        url: global_config.ajax_url.get_user_info,
        default: {
            "user_icon": "",
            "user_id": "",
            "nickname": "",
            "sex": "",
            "birthday": "",
            "location_id": "",
            "phone": "",
            "attendance": "0",
            "user_level": "",
            "organizer_level": "0",
            "model_level": "0",
            "cameraman_level": "0",
            "add_time": "",
            "last_login_time": "0",
            "last_login_location": "0",
            "code": "",
            "role": "",
            "set_top": "0",
            "pv": "0",
            "available_balance": "0.00",
            "balance": "0.00",
            "cover_img":"",
            "intro":"",
            "is_login" : false,
            "zone_info" : {}
        },

        /**
         * 安装事件
         * @private
         */
        _setup_events: function () {
            var self = this;

            self.once('success:get_info:fetch', function () {

            }).on('success:update_info:save', function (response) {

            }).on('before:get_info:fetch', function (xhr) {
                self._get_info_xhr = xhr;
            }).on('success:get_info:fetch', function (response) {
                self.update_user(response.result_data.data);

            }).on('complete:get_info:fetch', function () {
                delete self._get_info_xhr;
            });
        },
        parse: function (response) {
            if (response.result_data) {
                return response.result_data.data;
            }

            return response;
        },
        initialize: function () {
            var self = this;

            self._setup_events();
        },
        get_zone_info: function (role,user_id)
        {
            var self = this;

            self.fetch
            ({
                url: global_config.ajax_url.zone_info,
                data :
                {
                    role : role,
                    user_id : user_id
                },
                cache: false,
                beforeSend: function (xhr, options) {
                    self.trigger('before:get_zone_info:fetch', xhr, options);
                },
                success: function (model, response, options) {
                    self.trigger('success:get_zone_info:fetch', response, options);
                },
                error: function (model, xhr, options) {
                    self.trigger('error:get_zone_info:fetch', model, xhr, options);
                },
                complete: function (xhr, status) {
                    self.trigger('complete:get_zone_info:fetch', xhr, status);
                }
            });


            return self;
        },
        get_info : function()
        {
            var self = this;

            self.fetch
            ({
                url: self.url,
                cache: false,
                beforeSend: function (xhr, options) {
                    self.trigger('before:get_info:fetch', xhr, options);
                },
                success: function (model, response, options) {

                    self.update_user(response.result_data.data);

                    self.trigger('success:get_info:fetch', response, options);
                },
                error: function (model, xhr, options) {
                    self.trigger('error:get_info:fetch', model, xhr, options);
                },
                complete: function (xhr, status) {
                    self.trigger('complete:get_info:fetch', xhr, status);
                }
            });

        },
        /**
         * 获取个人信息函数，专用于app
         */
        get_user_info_by_app : function()
        {
            var self = this;

            var params = window.__YUE_APP_USER_INFO__;

            console.info(window.__YUE_APP_USER_INFO__);

            var local_user_id = utility.login_id;
            var client_user_id = window.__YUE_APP_USER_INFO__.user_id || 0;

            var async = (local_user_id == client_user_id);

            console.log("=====local_user_id,client_user_id=====");
            console.log(local_user_id,client_user_id);

            self.fetch
            ({
                url: global_config.ajax_url.auth_get_user_info,
                data : params,
                cache: false,
                async : async,
                beforeSend: function (xhr, options) {
                    self.trigger('before:get_user_info_by_app:fetch', xhr, options);
                },
                success: function (model, response, options) {

                    self.update_user(response.result_data.data);

                    self.trigger('success:get_user_info_by_app:fetch', response, options);
                },
                error: function (model, xhr, options) {
                    self.trigger('error:get_user_info_by_app:fetch', model, xhr, options);
                },
                complete: function (xhr, status) {
                    self.trigger('complete:get_user_info_by_app:fetch', xhr, status);
                }
            });

        },
        update_info : function(data)
        {
            var self = this;

            self.fetch
            ({
                url: global_config.ajax_url.update_user_info,
                cache: false,
                data:data,
                beforeSend : function(xhr, options) {
                self.trigger('before:update_info:save', xhr, options);
            },
                success : function(model, response, options) {
                    self.trigger('success:update_info:save', response, options);
                },
                error : function(model, xhr, options) {
                    self.trigger('error:update_info:save', model, xhr, options);
                },
                complete : function(xhr, status) {
                    self.trigger('complete:update_info:save', xhr, status);
                }
            });

        },
        update_user: function (data) {
            var self = this;

            utility.storage.set('user', data);

            self.set(data);
        },

        send_recharge:function(data)
        {
            var self = this;
            var location = window.location;

            if(data.redirect)
            {
                var redirect_url = location.origin+"/mobile/"+window._page_mode+"#"+data.redirect;
            }
            else
            {
                var redirect_url = location.origin+"/mobile/"+window._page_mode+"#mine";
            }

            // 用于刷新整个网页
            if(data.is_refresh)
            {
                var rand = new Date().getTime();

                var redirect_url = location.origin+"/mobile/"+window._page_mode+'?'+rand+"#"+data.redirect;
            }

            redirect_url = encodeURIComponent(redirect_url);

            data = $.extend(data,{redirect_url : redirect_url});

            self.fetch
            ({
                url: global_config.ajax_url.recharge,
                data : data,
                cache: false,
                beforeSend: function (xhr, options) {
                    self.trigger('before:send_rechare:fetch', xhr, options);
                },
                success: function (model, response, options) {
                    self.trigger('success:send_rechare:fetch', response, options);
                },
                error: function (model, xhr, options) {
                    self.trigger('error:send_rechare:fetch', model, xhr, options);
                },
                complete: function (xhr, status) {
                    self.trigger('complete:send_rechare:fetch', xhr, status);
                }
            });
        },
        get_date_by_date_id : function(date_id)
        {
            var self = this;

            self.fetch
            ({
                url: global_config.ajax_url.get_date_by_date_id,
                cache: false,
                data :
                {
                    date_id : date_id
                },
                beforeSend: function (xhr, options) {

                    self.trigger('before:get_date_by_date_id:fetch', xhr, options);
                },
                success: function (model, response, options) {
                    self.trigger('success:get_date_by_date_id:fetch', response, options);
                },
                error: function (model, xhr, options) {
                    self.trigger('error:get_date_by_date_id:fetch', model, xhr, options);
                },
                complete: function (xhr, status) {
                    self.trigger('complete:get_date_by_date_id:fetch', xhr, status);
                }
            });

        },
        /**
         * 转换角色
         */
        change_role : function()
        {
            var self = this;

            self.fetch
            ({
                url: global_config.ajax_url.change_role,
                cache: false,
                beforeSend: function (xhr, options) {
                    self.trigger('before:change_role:fetch', xhr, options);
                },
                success: function (model, response, options) {
                    self.trigger('success:change_role:fetch', response, options);
                },
                error: function (model, xhr, options) {
                    self.trigger('error:change_role:fetch', model, xhr, options);
                },
                complete: function (xhr, status) {
                    self.trigger('complete:change_role:fetch', xhr, status);
                }
            });
        },
        /**
         * 注册功能
         * @param data
         */
        reg : function(data)
        {
            var self = this;

            self.fetch
            ({
                url : global_config.ajax_url.reg,
                data :data,
                cache : false,
                beforeSend : function(xhr,options)
                {
                    self.trigger('before:reg',xhr,options);
                },
                success : function(collection, response, options)
                {
                    self.trigger('success:reg',response,options);
                },
                error : function(collection, response, options)
                {
                    self.trigger('error:reg',response,options)
                },
                complete : function(xhr,status)
                {
                    self.trigger('complete:reg',xhr,status);
                }
            });
        },
        get_bail_available_balance : function()
        {
            var self = this;

            self.fetch
            ({
                url : global_config.ajax_url.get_bail_available_balance,
                data :{},
                cache : false,
                beforeSend : function(xhr,options)
                {
                    self.trigger('before:fetch:get_bail_available_balance',xhr,options);
                },
                success : function(collection, response, options)
                {
                    self.trigger('success:fetch:get_bail_available_balance',response,options);
                },
                error : function(collection, response, options)
                {
                    self.trigger('error:fetch:get_bail_available_balance',response,options)
                },
                complete : function(xhr,status)
                {
                    self.trigger('complete:fetch:get_bail_available_balance',xhr,status);
                }
            });
        },
        get_level_list : function()
        {
            var self = this;

            self.fetch
            ({
                url : global_config.ajax_url.level_list,
                cache : false,
                beforeSend : function(xhr,options)
                {
                    self.trigger('before:authentication_list:fetch',xhr,options);
                },
                success : function(collection, response, options)
                {
                    self.trigger('success:authentication_list:fetch',response,options);
                },
                error : function(collection, response, options)
                {
                    self.trigger('error:authentication_list:fetch',response,options)
                },
                complete : function(xhr,status)
                {
                    self.trigger('complete:authentication_list:fetch',xhr,status);
                }
            });
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