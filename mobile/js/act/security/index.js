
define(function(require,exports,module){
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var utility = require('../../common/utility');
    var action_security_view = require('./view');
    var action_security_collection = require('./collection');

    page_control.add_page([function(){
        return{
           title:'我的活动劵',
            route:{
                'act/security' : 'act/security'
            },
            dom_not_cache : true,
            ignore_exist : true,
            transition_type : 'slide',
            page_init : function(){
                self.collection = new action_security_collection;
                this.security_view = new action_security_view({
                    parentNode: this.$el,
                    collection: self.collection
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