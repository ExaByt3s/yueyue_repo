/**
 * Created by nolest on 2014/9/1.
 *
/* 预览页
 */
define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var utility = require('../../common/utility');
    var pub_preview_view = require('./view');
    var pub_preview_model = require('./model');

    page_control.add_page([function()
    {
        return{
            title : '活动预览',
            dom_not_cache : true,
            ignore_exist : true,
            transition_type : 'slide',
            route :
            {
                'act/detail/:event_id(/:type)': 'act/detail'
            },
      
            page_init : function(page_view,route_params_arr,route_params_obj)
            {
                var self = this;

                var model = new pub_preview_model();

                var templateModel = {};

                var event_id = utility.int(route_params_arr[0]);

                console.log(route_params_obj);



                model.set('event_id',event_id);



                if(route_params_obj && route_params_obj.form_info_title)
                {
                    //如果从活动详情点击title过来的 需要隐藏“报名按钮”
                    templateModel =
                    {
                        title : '活动详情',
                        is_preview : false,
                        form_info_title : true
                    };
                }
                else if(event_id)
                {
                    //event_id == 0 时是发布活动的预览，传递一个route_params_obj过来构建页面内容 nolest 2015-1-30
                    templateModel =
                    {
                        title : '活动详情',
                        is_preview : false
                    };

                }
                else
                {

                    templateModel =
                    {
                        title : '活动预览',
                        is_preview : true
                    }
                }

                var type = route_params_arr[1]  ? route_params_arr[1] : 'intro';

                var view = new pub_preview_view
                ({
                    model : model,
                    parentNode : self.$el,
                    type : type,
                    templateModel : templateModel,
                    preview_obj : route_params_obj

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


