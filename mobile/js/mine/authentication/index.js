
define(function(require,exports,module){
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var model = require('../model');
    var view = require('./view');
    var utility = require('../../common/utility');

    page_control.add_page([function(){
        return{
           title:'信用等级认证',
            route:{
                'mine/authentication/:type(/:status)' : 'mine/authentication/:type(/:status)'
            },
            dom_not_cache : true,
            ignore_exist: true,
            transition_type : 'slide',
            page_init : function(page_view,route_params_arr,route_params_obj){

                var mine_model = new model;
                //路由数据
                var options = {};
                var is_v2;
                var from_date = 0;
                var from_date_success = 0;

                route_params_arr[0] == 'v2' ? is_v2 = true : is_v2 = false ;

                //等级1时去v2
                if(utility.user.get("user_level") == 1)
                {
                    is_v2 = true
                }

                if(/from_date_/.test(route_params_arr[1])){
                    from_date = 1;
                    options.yp_url = route_params_arr[1].replace(/from_date_/,"");
                }

                if(/from_date_success_/.test(route_params_arr[1])){
                    from_date_success = 1;
                    options.yp_url = route_params_arr[1].replace(/from_date_success_/,"");
                }

                options.is_v2 = is_v2;
                options.from_date = from_date;
                options.from_date_success = from_date_success;


                this.apply_view = new view({
                    templateModel: {
                        nav_title : is_v2 ? 'V2 实名认证': 'V3 达人认证',
                        scoll_wrapper_height : utility.get_view_port_height('bar') - 95
                    },
                    parentNode: this.$el,
                    model: mine_model
                }).set_options(options).render();

                page_view.view = this.apply_view
            },
            page_before_show : function()
            {

            },
            page_show : function()
            {

            },
            page_before_hide : function()
            {

            }

        }
    }]);
})