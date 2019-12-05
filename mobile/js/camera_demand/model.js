/**
 * Created by nolest on 2014/8/30.
 *
 * 活动列表模型
 *
 */
define(function(require, exports, module)
{
    var Backbone = require('backbone');
    var global_config = require('../common/global_config');

    module.exports = Backbone.Model.extend
    ({
        default :
        {
              
        },
        parse : function(response)
        {

            if(response.result_data.list)
            {
                return response.result_data.list;
            }

            return response;
        },
        _setup_events : function ()
        {

        },
        initialize : function()
        {
            var self = this;

            self._setup_events();
        },

        get_detail : function(data)
        {
            var self = this;
            self.fetch
            ({
                url : global_config.ajax_url.requirement_detail,
                data: data,
                cache : false,
                beforeSend : function(collection, response,options)
                {
                    self.trigger('before:detail:fetch',response,options);
                },
                success : function(collection, response,options)
                {
                    self.trigger('success:detail:fetch',response,options);
                },
                error : function(collection, response,options)
                {
                    self.trigger('error:detail:fetch',response,options);
                },
                complete : function(xhr,status)
                {
                    self.trigger('complete:detail:fetch',xhr,status);
                }
            });

            return self
        },
        send_data : function(data)
        {
            var self = this;
            self.fetch
            ({
                url : global_config.ajax_url.sign_requirement_act,
                data: data,
                cache : false,
                beforeSend : function(collection, response,options)
                {
                    self.trigger('before:send_data:fetch',response,options);
                },
                success : function(collection, response,options)
                {
                    self.trigger('success:send_data:fetch',response,options);
                },
                error : function(collection, response,options)
                {
                    self.trigger('error:send_data:fetch',response,options);
                },
                complete : function(xhr,status)
                {
                    self.trigger('complete:send_data:fetch',xhr,status);
                }
            });

            return self
        }


    /**
     * 获得广告图数据
     * @param data
     */

    });
});