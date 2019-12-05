/**
 *
 */
define(function(require, exports, module)
{
    var global_config = require('../../common/global_config');
    var collection = require('../../common/collection');

    module.exports = collection.extend
    ({
        url : global_config.ajax_url.get_act_joiners,
        initialize : function(options)
        {
            var self = this;

            self._setup_events();

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

            /*self
            .on('before:fetch',function(response)
            {

            })
            .on('success:fetch',function(response)
            {
                self.has_next_page = response.result_data.has_next_page;
            })*/

        },
        get_games: function(page,event_id) {
            var self = this;
            /*if (self.getState() == 'sending') {
                return self;
            }*/

            var paramData = {
                event_id : event_id
            };

            self.fetch({
                remove: page === 1,
                reset: page === 1,
                data: paramData,
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
        set_get_partners_id : function(id){
            this.id = id;
        }
    });
});

