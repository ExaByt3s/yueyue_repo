/**
 * Created by nolest on 2014/9/28.
 */

define(function(require, exports, module)
{
    var global_config = require('../common/global_config');
    var Backbone = require('backbone');
    var utility = require('../common/utility');

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
        initialize: function ()
        {

        },
        get_rank: function (page)
        {
            var self = this;

            var loc;

            if(utility.storage.get('location').location_id)
            {
                loc = utility.storage.get('location').location_id;
            }
            else
            {
                loc = 0;
            }

            self.fetch
            ({
                url: self.get("url"),
                data:
                {
                    location_id : loc,
                    page : page
                },
                cache : false,
                beforeSend: function (xhr, options) {
                    self.trigger('before:date:fetch', xhr, options);
                },
                success: function (model, response, options) {
                    self.trigger('success:date:fetch', response, options);
                },
                error: function (model, xhr, options) {
                    self.trigger('error:date:fetch', model, xhr, options);
                },
                complete: function (xhr, status) {
                    self.trigger('complete:date:fetch', xhr, status);
                }
            });

            return self;
        }
    });
});