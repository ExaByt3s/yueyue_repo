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
    var topic_view = require('./list/view');
    var topic_collection = require('./collection');

    var topic_html_tpl = '';

	page_control.add_page([function()
    {
        return{
            title : '专题列表',
            route :
            {
                'topic_list' : 'topic_list'
            },
            transition_type : 'slide',
            dom_not_cache: true,
            ignore_exist: true,
            page_init : function(page_view,route_params_arr,route_params_obj)
            {
                var self = this;

                self.topic_collection_obj = new topic_collection();

                var topic_view_obj = new topic_view
                ({
                    parentNode : self.$el,
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

