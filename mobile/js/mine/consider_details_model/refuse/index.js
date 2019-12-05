/**
 * Created by nolest on 2014/9/11.
 */


define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../../../frame/page_control');
    var refuse_view = require('./view');
    var global_config = require('../../../common/global_config');
    var utility = require('../../../common/utility');
    var ref_model = require('./model');

    page_control.add_page([function()
    {
        return{
            title : '约拍取消',
            page_key : 'date_cancel',
            route :
            {
                'mine/consider_details_model/refuse' : 'mine/consider_details_model/refuse'
            },
            transition_type : 'slide',
            initialize : function()
            {

            },
            page_init : function(page_view,route_params_arr,route_params_obj)
            {
                var self = this;

                // 设置此页面不能返回
                window.AppCanPageBack = false;

                var from_app = utility.get_url_params(window.location.search,'from_app');

                var refuse_model = new ref_model({});

                console.log(route_params_obj)
                var view = new refuse_view
                ({
                    from_app : utility.int(from_app),
                    model : refuse_model,
                    parentNode : self.$el,
                    templateModel : route_params_obj
                }).render();

                //templateModel : templateModel
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

