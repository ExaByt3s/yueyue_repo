
define(function(require,exports,module){
    var $ = require('$');
    var page_control = require('../../../frame/page_control');
    var utility = require('../../../common/utility');
    var model_card_view = require('./view');

    var model_card_model = require('../model');

    page_control.add_page([function(){
        return{
           title:'编辑模特卡',
            route:{
                'model_date/model_card/edit_all(/:type)' : 'model_date/model_card/edit_all(/:type)'
            },
            dom_not_cache: true,
            ignore_exist: true,
            transition_type : 'slide',
            page_init : function(page_view,route_params_arr,route_params_obj){
                var login_info = utility.user;
                var data_login_info = login_info.toJSON();
                var is_can_back = route_params_arr[1];

                if(utility.int(is_can_back))
                {
                    // 设置该状态下Android页面返回禁止
                    utility.set_no_page_back(utility.getHash());
                }

                //注册跳过来不显示返回按钮
                //route_params_obj && (data_login_info.is_from_reg = route_params_obj.is_from_reg);

                data_login_info.scoll_wrapper_height = utility.get_view_port_height('bar') + 50;
                this.model_card_view = new model_card_view({
                    templateModel: data_login_info,
                    parentNode: this.$el,
                    model:new model_card_model,
                    is_can_back : is_can_back
                }).render();


                //用于路由直接导航到某part
                var type = route_params_arr[0];
                type && this.model_card_view.navigate_to_page(this.model_card_view.$('[data-target=' + type + ']'),type);

            },
            page_before_show : function()
            {
                //选择风格后返回
                if(window._model_style_id && this.model_card_view['model_style_card' + '_' + window._model_style_id]){
                    this.model_card_view['model_style_card' + '_' + window._model_style_id].$('[data-role="model-style"]').html(window._model_style_text);
                }

            },
            page_show : function()
            {
                var self = this;

            },
            page_before_hide : function()
            {

            }

        }
    }]);
})