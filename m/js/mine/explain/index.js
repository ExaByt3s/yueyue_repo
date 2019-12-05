/**
 * Created by nolest on 2015/1/6.
 */

define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var explain_view = require('./view');
    var global_config = require('../../common/global_config');

    page_control.add_page([function()
    {
        return{
            title : '说明页',
            route :
            {
                'mine/explain(/:type)' : 'mine/explain(/:type)'
            },
            dom_not_cache : true,
            ignore_exist : true,
            transition_type : 'slide',
            page_init : function(page_view,route_params_arr,route_params_obj)
            {
                var self = this;

                var title;

                var type = route_params_arr[0];

                var lev,charm,charm_list,commit_list,date_list;
                //级别说明 lev
                //魅力指数说明 charm
                //魅力排行榜说明 charm_list
                //评优排行榜说明 commit_list
                //约拍排行榜说明 data_list
                if(route_params_arr[0] && route_params_arr[0] == 'lev')
                {
                    title = '级别说明';

                    lev = 1;
                }
                else if(route_params_arr[0] && route_params_arr[0] == 'charm')
                {
                    title = '魅力指数说明';

                    charm = 1;
                }
                else if(route_params_arr[0] && route_params_arr[0] == 'charm_list')
                {
                    title = '魅力排行榜说明';

                    charm_list = 1;
                }
                else if(route_params_arr[0] && route_params_arr[0] == 'commit_list')
                {
                    title = '评优排行榜说明';

                    commit_list = 1;
                }
                else if(route_params_arr[0] && route_params_arr[0] == 'date_list')
                {
                    title = '约拍排行榜说明';

                    date_list = 1;
                }

                var view = new explain_view
                ({
                    parentNode : self.$el,
                    type : type,
                    templateModel : {title:title,lev:lev,charm:charm,charm_list:charm_list,commit_list:commit_list,date_list:date_list}
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

