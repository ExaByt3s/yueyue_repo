/**
 * Created by nolest on 2014/9/9.
 */
define(function(require, exports, module)
{
    var global_config = require('../../common/global_config');
    var Backbone = require('backbone');
    var utility = require('../../common/utility');

    module.exports = Backbone.Model.extend
    ({
        url: global_config.ajax_url.consider_list,
        default: {
            "date_id": "",
            "from_date_id": "",
            "to_date_id": "",
            "date_status": "",
            "date_time": "",
            "date_address": "",
            "date_type": "",
            "date_style": "",
            "date_hour": "",
            "date_price": "",
            "check_id": "",
            "event_id": "",
            "add_time": "",
            "update_time": "",
            "pay_status": "",
            "cancel_reason": "",
            "status_remark": "",
            "cameraman_icon": ""
        },
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
        get_more_list: function (data)
        {
            var self = this;

            self.fetch
            ({
                url: self.url,
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