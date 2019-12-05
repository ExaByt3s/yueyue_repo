/**
 * Created by nolestLam on 2014/8/30.
 */
define(function(require, exports, module)
{
    var Backbone = require('backbone');

    module.exports = Backbone.Model.extend
    ({
        default :
        {
            _seq_id_ : '',
            active_time : '',
            address : '',
            budget : '',
            category : '',
            club : '',
            content: '',
            cover_image : '',
            end_time: '',
            event_id: '',
            event_review : '',
            hit_count : '',
            is_authority : '',
            is_recommend : '',
            is_top : '',
            join_count : '',
            last_update_time : '',
            limit_num : '',
            location_id : '',
            review_time : '',
            score : '',
            start_time : '',
            status : '',
            title : '',
            type_icon : '',
            user_id : '',
            username : ''
        },
        _setup_events : function ()
        {

        },
        initialize : function()
        {
            var self = this;

            self._setup_events();
        },
        get_list : function(data)
        {
            var self = this;

            self.fetch
            ({
                url :self.get('url'),
                data : data,
                reset : true,
                success : function(collection, response,options)
                {
                    self.trigger('success:fetch',response,options);
                },
                error : function(response,options)
                {
                    self.trigger('error:fetch',response,options);
                },
                complete : function(xhr,status)
                {
                    self.trigger('complete:fetch',xhr,status);
                }
            });

            return self
        }
    });
});