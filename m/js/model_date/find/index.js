/**
 * 首页 - 发现
 * nolestLam 2014.8.4
 */
define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var find_view = require('../find/view');
    var utility = require('../../common/utility');
    var App = require('../../common/I_APP');


    page_control.add_page([function()
    {
        var find_view_obj;

        var location_id;

        return {
            title : '发现',
            route :
            {
                'find' : 'find'
            },
            initialize : function()
            {


            },
            events :
            {

            },
            page_init : function()
            {
                var self = this;

                find_view_obj = new find_view
                ({
                    parentNode : self.$el
                }).render();

                if(utility.storage.get('location'))
                {
                    self.location_id = utility.storage.get('location').location_id;
                }


            },
            page_before_show : function ()
            {
                var self = this;

                find_view_obj.set_header_location();

                //对比self.location_id和当前id，进行刷新
                if(utility.storage.get('location') && self.location_id != utility.storage.get('location').location_id)
                {
                    console.log("in_location_refresh");

                    self.location_id = utility.storage.get('location').location_id;

                    find_view_obj.$style_collection.set_location(self.location_id);

                    find_view_obj.style_grid_view && find_view_obj.style_grid_view.refresh();
                }

            },
            page_show : function()
            {
                if(App.isPaiApp)
                {
                    //App.remove_front();
                }
            },
            page_before_hide : function()
            {
                var self = this;

            },
            page_hide : function()
            {
                var self = this;
                //换页前固定下拉菜单
                //find_view_obj.select_drop_obj.stay();
            }
        }
    }]);
});