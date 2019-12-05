/**
 * Created by nolest on 2014/9/11.
 */

define(function(require, exports, module)
{
    var global_config = require('../../../common/global_config');
    var Backbone = require('backbone');
    var utility = require('../../../common/utility');

    module.exports = Backbone.Model.extend
    ({
        url: global_config.ajax_url.accept_invite,
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
        send_request: function (data) {
            var self = this;

            self.fetch
            ({
                url: self.url,
                data:data,
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
        }
    });
});