/**
 * Created by nolestLam on 2015/3/5.
 */
define(function(require, exports, module)
{
    var global_config = require('../common/global_config');
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
        get_coupon_list : function(data)
        {
            //coupon/choose coupon/list 页查询请求
            var self = this;

            self.fetch
            ({
                url : self.get("url"),
                data : data,
                cache : false,
                beforeSend : function(xhr,options)
                {
                    self.trigger('before:get_coupon_list:fetch',xhr,options);
                },
                success : function(collection, response, options)
                {
                    self.trigger('success:get_coupon_list:fetch',response,options);
                },
                error : function(collection, response, options)
                {
                    self.trigger('error:get_coupon_list:fetch',response,options)
                },
                complete : function(xhr,status)
                {
                    self.trigger('complete:get_coupon_list:fetch',xhr,status);
                }
            });
        },
        get_single_coupon : function(sn)
        {
            //优惠券详情页取单独优惠券信息
            var self = this;

            self.fetch
            ({
                url : global_config.ajax_url.get_single_coupon,
                data : {sn:sn},
                cache : false,
                beforeSend : function(xhr,options)
                {
                    self.trigger('before:get_single_coupon:fetch',xhr,options);
                },
                success : function(collection, response, options)
                {
                    self.trigger('success:get_single_coupon:fetch',response,options);
                },
                error : function(collection, response, options)
                {
                    self.trigger('error:get_single_coupon:fetch',response,options)
                },
                complete : function(xhr,status)
                {
                    self.trigger('complete:get_single_coupon:fetch',xhr,status);
                }
            });
        },
        get_supply_detail : function(supply_id)
        {
            //优惠券详情页取单独优惠券信息
            var self = this;

            self.fetch
            ({
                url : global_config.ajax_url.get_supply_detail,
                data : {supply_id:supply_id},
                cache : false,
                beforeSend : function(xhr,options)
                {
                    self.trigger('before:get_supply_detail:fetch',xhr,options);
                },
                success : function(collection, response, options)
                {
                    self.trigger('success:get_supply_detail:fetch',response,options);
                },
                error : function(collection, response, options)
                {
                    self.trigger('error:get_supply_detail:fetch',response,options)
                },
                complete : function(xhr,status)
                {
                    self.trigger('complete:get_supply_detail:fetch',xhr,status);
                }
            });
        },
        give_supply_coupon : function(supply_id)
        {
            //免费领取 按钮
            var self = this;

            self.fetch
            ({
                url : global_config.ajax_url.give_supply_coupon,
                data : {supply_id:supply_id},
                cache : false,
                beforeSend : function(xhr,options)
                {
                    self.trigger('before:give_supply_coupon:fetch',xhr,options);
                },
                success : function(collection, response, options)
                {
                    self.trigger('success:give_supply_coupon:fetch',response,options);
                },
                error : function(collection, response, options)
                {
                    self.trigger('error:give_supply_coupon:fetch',response,options)
                },
                complete : function(xhr,status)
                {
                    self.trigger('complete:give_supply_coupon:fetch',xhr,status);
                }
            });
        },
        give_coupon : function(sn)
        {
            //免费领取 按钮
            var self = this;

            self.fetch
            ({
                url : global_config.ajax_url.give_coupon,
                data : {sn:sn},
                cache : false,
                beforeSend : function(xhr,options)
                {
                    self.trigger('before:give_coupon:fetch',xhr,options);
                },
                success : function(collection, response, options)
                {
                    self.trigger('success:give_coupon:fetch',response,options);
                },
                error : function(collection, response, options)
                {
                    self.trigger('error:give_coupon:fetch',response,options)
                },
                complete : function(xhr,status)
                {
                    self.trigger('complete:give_coupon:fetch',xhr,status);
                }
            });
        }
    });
});