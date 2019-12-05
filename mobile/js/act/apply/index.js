
define(function(require,exports,module){
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var model = require('../detail/model');
    var action_apply_view = require('./view');

    page_control.add_page([function()
    {
        return{
            title:'活动报名',
            route:
            {
                'act/apply/:event_id' : 'act/apply'
            },
            dom_not_cache : true,
            ignore_exist : true,
            transition_type : 'slide',
            page_init : function(apps_page_view, apps_params,apply_info)
            {


                var event_id = apps_params[0];

                var act_model = null;

                var is_new_fetch = false;

                // 直接获取传递过来的model对象
                if(apply_info && apply_info.cid)
                {
                    act_model = apply_info;

                    act_model.set('is_new_fetch',true);
                }
                else
                {
                    act_model = new model
                    ({
                        event_id : event_id
                    });

                    act_model.set('is_new_fetch',false);

                }

                this.apply_view = new action_apply_view
                ({
                    parentNode: this.$el,
                    model:act_model,
                    event_id : event_id,
                    is_new_fetch : is_new_fetch
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