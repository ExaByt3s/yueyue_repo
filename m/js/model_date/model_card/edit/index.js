
define(function(require,exports,module){
    var $ = require('$');
    var page_control = require('../../../frame/page_control');
    var Utility = require('../../../common/utility');
    var model_card_view = require('./view');

    var model_card_model = require('../model');

    page_control.add_page([function(){
        return{
           title:'编辑模特卡',
            route:{
                'model_date/model_card(/:is_can_back)' : 'model_date/model_card'
            },
            dom_not_cache: true,
            ignore_exist: true,
            transition_type : 'slide',
            page_init : function(page_view,route_params_arr,route_params_obj){

                var login_info = Utility.user;
                var data_login_info = login_info.toJSON();

                var is_can_back = route_params_arr[0];

                var no_page_back = 0;

                if(is_can_back.toString() == 'edit')
                {
                    // 设置该状态下Android页面返回禁止
                    Utility.set_no_page_back(Utility.getHash());

                    no_page_back = 1;

                }

                //注册跳过来不显示返回按钮

                data_login_info = $.extend(data_login_info,{is_from_reg : no_page_back});

                data_login_info.scoll_wrapper_height = Utility.get_view_port_height('bar') + 50;
                this.model_card_view = new model_card_view({
                    templateModel: data_login_info,
                    parentNode: this.$el,
                    model:new model_card_model,
                    is_can_back : is_can_back,
                    no_page_back : no_page_back
                }).render();

            },
            page_before_show : function()
            {

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