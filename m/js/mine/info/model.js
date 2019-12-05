/**
 * Created by nolest on 2014/10/27.
 *
 * 活动状态详情model
 */


define(function(require, exports, module)
{
    var global_config = require('../../common/global_config');
    var Backbone = require('backbone');
    var utility = require('../../common/utility');

    module.exports = Backbone.Model.extend
    ({
        _setup_events: function ()
        {

        },
        parse: function (response) {
            if (response.result_data) {
                return response.result_data;
            }
            return response;
        },
        initialize: function () {

        },
        get_info: function (enroll_id){
            var self = this;

            self.fetch
            ({
                url: global_config.ajax_url.get_enroll_detail_info,
                data:{enroll_id : enroll_id},
                cache : false,
                beforeSend: function (xhr, options) {
                    self.trigger('before:fetch', xhr, options);
                },
                success: function (model, response, options) {
                    self.trigger('success:fetch', response, options);
                },
                error: function (model, xhr, options) {
                    self.trigger('error:fetch', model, xhr, options);
                },
                complete: function (xhr, status) {
                    self.trigger('complete:fetch', xhr, status);
                }
            });

            return self;
        },
        get_pub_info : function(event_id)
        {
            var self = this;

            self.fetch
            ({
                url: global_config.ajax_url.act_info,
                data:{event_id : event_id,is_from_my_act_list:1},
                cache : false,
                beforeSend: function (xhr, options) {
                    self.trigger('before:pub_fetch', xhr, options);
                },
                success: function (model, response, options) {
                    self.trigger('success:pub_fetch', response, options);
                },
                error: function (model, xhr, options) {
                    self.trigger('error:pub_fetch', model, xhr, options);
                },
                complete: function (xhr, status) {
                    self.trigger('complete:pub_fetch', xhr, status);
                }
            });

            return self;

        },
        set_event_end_act : function(event_id)
        {
            //组织者活动完成
            var self = this;

            self.fetch
            ({
                url :global_config.ajax_url.set_event_end_act,
                data:
                {
                    event_id : event_id
                },
                cache : false,
                beforeSend : function(xhr,options)
                {
                    self.trigger('before:set_event_end_act:fetch',xhr,options);
                },
                success : function(collection, response,options)
                {
                    self.trigger('success:set_event_end_act:fetch',response,options);
                },
                error : function(response,options)
                {
                    self.trigger('error:set_event_end_act:fetch',response,options);
                },
                complete : function(xhr,status)
                {
                    self.trigger('complete:set_event_end_act:fetch',xhr,status);
                }
            });

            return self
        },//
        set_event_cancel_act : function(event_id)
        {
            //组织者活动完成
            var self = this;

            self.fetch
            ({
                url :global_config.ajax_url.set_event_cancel_act,
                data:
                {
                    event_id : event_id
                },
                cache : false,
                beforeSend : function(xhr,options)
                {
                    self.trigger('before:set_event_cancel_act:fetch',xhr,options);
                },
                success : function(collection, response,options)
                {
                    self.trigger('success:set_event_cancel_act:fetch',response,options);
                },
                error : function(response,options)
                {
                    self.trigger('error:set_event_cancel_act:fetch',response,options);
                },
                complete : function(xhr,status)
                {
                    self.trigger('complete:set_event_cancel_act:fetch',xhr,status);
                }
            });

            return self
        },
        cancel_act : function(enroll_id)
        {
            //参与者取消参加
            var self = this;

            self.fetch
            ({
                url :global_config.ajax_url.del_enroll_act,
                data:
                {
                    enroll_id : enroll_id
                },
                cache : false,
                beforeSend : function(xhr,options)
                {
                    self.trigger('before:cancel_act:fetch',xhr,options);
                },
                success : function(collection, response,options)
                {
                    self.trigger('success:cancel_act:fetch',response,options);
                },
                error : function(response,options)
                {
                    self.trigger('error:cancel_act:fetch',response,options);
                },
                complete : function(xhr,status)
                {
                    self.trigger('complete:cancel_act:fetch',xhr,status);
                }
            });

            return self
        }
    });
});