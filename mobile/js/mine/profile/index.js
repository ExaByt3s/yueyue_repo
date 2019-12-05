
define(function(require,exports,module){
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var utility = require('../../common/utility');
    var m_alert = require('../../ui/m_alert/view');
    var mine_profile_view = require('./view');
    var model = require('../model');

    page_control.add_page([function(){
        return{
            title:'编辑资料',
            page_key : 'cameraman_profile',
            route:{
                'mine/profile(/:from_ret)' : 'mine/profile'
            },
            dom_not_cache: true,
            ignore_exist: true,
            transition_type : 'slide',
            page_init : function(page_view,route_params_arr,route_params_obj){

                if(!utility.login_id)
                {
                    m_alert.show('非法登录!');

                    return;
                }

                // 判断是否新用户进来编辑个人资料 modify hudw 2014.9.27
                var is_from_reg = route_params_arr[0];

                if(utility.int(is_from_reg))
                {
                    // 设置该状态下Android页面返回禁止
                    utility.set_no_page_back(utility.getHash());

                    console.log('set_no_page_back')
                }

                var login_info = utility.user;
                var data_login_info = login_info.toJSON();

                data_login_info = $.extend(data_login_info,{is_from_reg : is_from_reg})
                data_login_info.scoll_wrapper_height = utility.get_view_port_height('bar') + 50;
                this.profile_view = new mine_profile_view({
                    is_from_reg : is_from_reg,
                    templateModel: data_login_info,
                    parentNode: this.$el,
                    model:utility.user
                }).render();

            },
            page_before_show : function()
            {

            },
            page_show : function()
            {
                var self = this;
                var editData = self.profile_view._editData;

                //判断是否有修改
                if (!!editData) {
                    if (typeof editData.nickname == 'string') {
                        self.profile_view.$nickname.html(editData.nickname);
                        self.profile_view._has_change_info();
                    }
                    if (typeof editData.phone == 'string') {
                        self.profile_view.$phone.html(editData.phone);
                        self.profile_view._has_change_info();
                    }
                    if (typeof editData.signature == 'string') {
                        self.profile_view.$signature.html(editData.signature);
                        self.profile_view._has_change_info();
                    }
                }

            },
            page_before_hide : function()
            {

            }

        }
    }]);
})