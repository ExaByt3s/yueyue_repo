
define(function(require,exports,module){
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var model = require('../model');
    var view = require('./view');

    page_control.add_page([function(){
        return{
           title:'信用等级认证',
            route:{
                'mine/authentication_list' : 'mine/authentication_list'
            },
            page_key : 'authentication_list',
            dom_not_cache : true,
            ignore_exist: true,
            transition_type : 'slide',
            page_init : function(){

                var mine_model = new model;

                this.apply_view = new view({
                    parentNode: this.$el,
                    model: mine_model
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