/**
 * 专题 列表基础框架
 * hudw 2014.8.4
 * @param  {[type]} require
 * @param  {[type]} exports
 * @param  {[type]} module
 * @return {[type]}
 */
define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../frame/page_control');
    var utility = require('../common/utility');
    var App = require('../common/I_APP');
    var topic_view = require('./list/view');
    var topic_collection = require('./collection');

    var topic_html_tpl = '';

	page_control.add_page([function()
    {
        return{
            title : '专题列表',
            page_key : 'topic_list',
			dom_not_cache: true,
            ignore_exist: true,
            route :
            {
                'topic_list(/:custom_params)' : 'topic_list'
            },
            transition_type : 'slide',
            page_init : function(page_view,route_params_arr,route_params_obj)
            {
                var self = this;

                self.topic_collection_obj = new topic_collection();

				if(!route_params_arr)
				{
					route_params_arr = [0];
				}

                var topic_view_obj = new topic_view
                ({
                    parentNode : self.$el,
					route_params_arr : route_params_arr,
                    collection : self.topic_collection_obj
                }).render();


                topic_view_obj.refresh();

            },
            page_before_show : function()
            {
                var self = this;

            },
            page_show : function()
            {

            },
            page_before_show : function()
            {
                var self = this;

                // 设置地区id
                if(App.isPaiApp)
                {
                    App.check_login(function(ret)
                    {
                        console.log('====== topic list check_login ======');
                        console.log(ret);

                        var client_location_id = utility.int(ret.locationid);

                        utility.storage.set('location_id',client_location_id);

                    })
                }
            },
            page_before_hide : function()
            {
                var self = this;
            },
            page_hide : function()
            {
                var self = this;
            }
        }
    }]);

})

