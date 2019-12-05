
define(function(require,exports,module){
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var action_signin_view = require('./view');
    var action_signin_collection = require('./collection');
    var model = require('../detail/model');
    var utility = require('../../common/utility');

    page_control.add_page([function(){
        return{
            title:'报名网友',
            route:{
                'act/signin/:event_id(/:pay_ment_no)' : 'act/signin'
            },
            dom_not_cache : true,
            ignore_exist : true,
            transition_type : 'slide',
            page_init : function(appsPageView, appsParams,signinInfo)
            {
                /*var is_follow = 0;
                is_follow = signinInfo.model.cid && signinInfo.model.get('is_follow');*/

                var event_id = appsParams[0];

                var model_obj = null;
                //return_home用于处理是否显示支付成功后“返回我的”块的。
                var return_home = false;

                if(appsParams[1])
                {
                    // 设置该状态下Android页面返回禁止
                    utility.set_no_page_back(utility.getHash());

                    return_home = true;
                }

                console.log(signinInfo);
                // 传递过来的
                if(signinInfo && signinInfo.model && signinInfo.model.cid)
                {
                    model_obj = signinInfo.model;

                    model_obj.set('is_new_fetch',false);
                }
                else
                {
                    model_obj = new model
                    ({
                        event_id : event_id
                    });

                    model_obj.set('is_new_fetch',true);
                }

                var is_mine = false;
                (signinInfo && signinInfo.model && signinInfo.model.get('pub_user_id') == utility.login_id) && (is_mine = true);

                self.collection = new action_signin_collection();
                this.signin_view = new action_signin_view({
                    parentNode: this.$el,
                    /*templateModel : {
                        is_follow : is_follow ? 0 : 1,
                        follow_class : is_follow ? 'followed' : ''
                    },*/
                    return_home : return_home,
                    collection : self.collection,
                    model : model_obj,
                    templateModel : {
                        return_home : return_home,
                        is_mine : is_mine
                    }
                }).render();

                this.signin_view._set_event_id(event_id);

            },
            page_before_show : function()
            {
                var self = this;

                if(self.signin_view)
                {
                    self.signin_view.refresh();
                }
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