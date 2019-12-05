/**
 * Created by Administrator on 2014/8/30.
 */
define(function(require, exports, module)
{
    var global_config = require('../common/global_config');
    var collection = require('../common/collection');
    var utility = require('../common/utility');

    module.exports = collection.extend
    ({
        url : global_config.ajax_url.act_list,
        initialize : function(options)
        {
            var self = this;

            self.select_options = options.default_options;

            self.page = 1;

            self._setup_events();

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
            var self = this;

            self
            .on('before:fetch',function(response)
            {

            })
            .on('success:fetch',function(response)
            {
                self.has_next_page = response.result_data.has_next_page;
            })

        },
        get_list : function(page)
        {
            var self = this;

            /*
            if(self.has_next_page)
            {
                self.page++;
            }

            self.page_options =
            {
                page : self.page
            };

            self.select_options = $.extend(true,{},self.select_options,self.page_options);

*/
            var page = self.select_options.page;

            var location_id = utility.storage.get('location_id') || 0;

            //每次请求时获取locaiton_id nolest 2015-02-05
            self.select_options = $.extend(true,{},self.select_options,{location_id:location_id});

            console.log(self.select_options);
            self.fetch
            ({
                url :self.url,
                data:self.select_options,
                cache : false,
                remove : page == 1,
                reset :  self.select_options.reset?1:0,
                beforeSend : function(collection, response,options)
                {
                    self.trigger('before:fetch',response,options);
                },
                success : function(collection, response,options)
                {
                    self.trigger('success:fetch',response,options);
                },
                error : function(collection, response,options)
                {
                    self.trigger('error:fetch',response,options);
                },
                complete : function(xhr,status)
                {
                    self.trigger('complete:fetch',xhr,status);
                }
            });

            return self
        },
        set_select_options : function(obj)
        {
            var self = this;

            self.select_options = $.extend(true,{},self.select_options,obj);

        },
        get_select_options : function()
        {
            var self = this;

            return self.select_options;
        }
    });
});

