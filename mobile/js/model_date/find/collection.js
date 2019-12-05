define(function(require, exports, module)
{
    var global_config = require('../../common/global_config');
    var collection = require('../../common/collection');
    var model = require('./model');
    var utility = require('../../common/utility');

    module.exports = collection.extend
    ({
        model : model,
        url : global_config.ajax_url.find,
        initialize : function(options)
        {
            var self = this;

            self._setup_events();

            self.location = utility.storage.get('location');

            self.type = options.type;
        },
        set_location : function(loc)
        {
            var self = this;

            self.location.location_id = loc;

        },
        get_location : function()
        {
            var self = this;

            return self.location;

        },
        set_type : function(type)
        {
            var self = this;

            self.type = type;
        },
        parse : function(response)
        {
            if(response.result_data.data)
            {
                return response.result_data.data;
            }

            return response;
        },
        _setup_events : function ()
        {

        },
        get_list : function()
        {
            var self = this;

            self.fetch
            ({
                url :self.url,
                reset : true,
                data :
                {
                    location_id : self.location.location_id,
                    type : self.type
                },
                success : function(collection, response,options)
                {
                    self.trigger('success:fetch',response,options);
                }
            });

            return self
        }
    });
});

