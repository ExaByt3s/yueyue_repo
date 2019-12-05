/**
 * 2015.1.1 hudw
 * 通用编辑页面
 */
define(function(require,exports,module){
    var $ = require('$');
    var page_control = require('../frame/page_control');
    var utility = require('../common/utility');
    var edit_view = require('./view');
    var m_alert = require('../ui/m_alert/view');

    page_control.add_page([function()
    {
        return{
            title:'文字设置',
            route:
            {
                'edit_page/:type' : 'edit_page/:type'
            },
            dom_not_cache: true,
            ignore_exist: true,
            transition_type : 'slide',
            page_init: function(page_view,route_params_arr,route_params_obj)
            {
                var self = this;

                // type 、text、title 为必传项
                var type = route_params_arr[0];

                var text = route_params_obj && route_params_obj.text;

                var title = route_params_obj && route_params_obj.title;

                var input_type = (route_params_obj && route_params_obj.input_type) || '' ;

                if(utility.is_empty(type) || utility.is_empty(title))
                {
                    m_alert.show('缺少参数，无法初始化','error',{delay:2000});

                    setTimeout(function()
                    {
                        page_control.back();
                    },2000);

                    return;
                }

                self.edit_view = new edit_view
                ({

                    templateModel:
                    {
                        title : title,
                        type : type,
                        text : text,
                        input_type : input_type
                    },
                    is_empty : route_params_obj && route_params_obj.is_empty || false,
                    edit_obj : route_params_obj && route_params_obj.edit_obj,
                    parentNode: self.$el
                }).render();
            },


            page_show:function()
            {
                var self = this;

                //必需在page_show时设置焦点才有效
                self.edit_view && self.edit_view._inputFocus();
            },
            page_before_remove: function()
            {
                var self = this;

                self.edit_view && self.edit_view.destroy();
            }

        }
    }]);
})