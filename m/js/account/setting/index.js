
define(function(require,exports,module){
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    //var model = require('../model');
    var app = require('../../common/I_APP');
    var ua = require('../../frame/ua');
    var account_setting_view = require('./view');

    page_control.add_page([function(){
        return{
           title:'系统设置',
            route:{
                'account/setting' : 'account/setting'
            },
            dom_not_cache : true,
            ignore_exist : true,
            transition_type : 'slide',
            page_init : function(){
                var self = this;

                this.setting_view = new account_setting_view({
                    parentNode: this.$el
                }).render();

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