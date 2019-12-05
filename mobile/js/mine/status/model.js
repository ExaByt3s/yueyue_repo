/**
 * Created by nolest on 2014/9/24.
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
        get_more_list: function (data) {
            var self = this;

            self.fetch
            ({
                url: global_config.ajax_url.status_list,
                data:data,
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
        cancel_act : function(enroll_id)
        {
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
        },
        set_event_end_act : function(event_id)
        {
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
        },
        /**
         * 取消我发布的活动
         * @param event_id
         * @returns {exports}
         */
        set_event_cancel_act : function(event_id)
        {
            console.log("in_model");
            
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
                    console.log("success");
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
        /**
         * 确认活动
         * @param event_id
         */
        confirm_date : function(event_id)
        {
            var self = this;

            self.fetch
            ({
                url: global_config.ajax_url.set_event_end_act,
                data:
                {
                    event_id : event_id
                },
                cache : false,
                beforeSend: function (xhr, options) {
                    self.trigger('before:confirm_date:fetch', xhr, options);
                },
                success: function (model, response, options) {
                    self.trigger('success:confirm_date:fetch', response, options);
                },
                error: function (model, xhr, options) {
                    self.trigger('error:confirm_date:fetch', model, xhr, options);
                },
                complete: function (xhr, status) {
                    self.trigger('complete:confirm_date:fetch', xhr, status);
                }
            });
        }
    });
});