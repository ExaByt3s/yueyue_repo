/**
 * 2015.4.20
 * 上传图片页面
 */
define(function(require,exports,module){
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var utility = require('../../common/utility');
    var m_alert = require('../../ui/m_alert/view');
    var edit_view = require('./view');

    page_control.add_page([function()
    {
        return{
            title:'拍摄图片',
            route:
            {
                'edit_page/photo(/:type)' : 'edit_page/photo(/:type)'
            },
            dom_not_cache: true,
            ignore_exist: true,
            transition_type : 'slide',
            page_init: function(page_view,params_arr,route_params_obj)
            {
                var self = this;

                if(params_arr[0] && /http/.test(params_arr[0]))
                {
                    var pic = decodeURIComponent(params_arr[0]);
                }
                else
                {
                    var pic = 'http://yp.yueus.com/mobile/images/pai/id_demo.png';
                }

                self.edit_view = new edit_view
                ({

                    templateModel:
                    {
                        pic : pic
                    },
                    params_obj : route_params_obj,
                    parentNode: self.$el
                }).render();
            },


            page_show:function()
            {
                var self = this;


            },
            page_before_remove: function()
            {
                var self = this;


            }

        }
    }]);
})