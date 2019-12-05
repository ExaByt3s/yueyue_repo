/**
 * 我要约拍
 * nolestLam 2014.8.21
 * @param  {[type]} require
 * @param  {[type]} exports
 * @param  {[type]} module
 * @return {[type]}
 */
define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var utility = require('../../common/utility');
    var model_style_view = require('../model_style/view');
    var model_card_model = require('../model_card/model');

    page_control.add_page([function()
    {
        return{
            title : '我要约拍',
            page_key : 'model_style',
            route :
            {
                'model_style/:query(/:payment_no)' : 'model_style'
            },
            dom_not_cache : true,
            ignore_exist : true,
            transition_type : 'slide',
            initialize : function()
            {

            },
            page_init : function(page_view,route_params_arr,route_params_obj)
            {
                var self = this;

                //从路由取id
                self.user_id = route_params_arr[0];

                var model_obj = null;
                var model = route_params_obj;
                //用于预览跳过来
                var preview_data = null;

                var is_preview = !utility.is_empty(route_params_obj);

                if(is_preview)
                {
                    route_params_obj.data.is_preview = true;
                    preview_data = route_params_obj.data
                }



                var options = {};

                if(is_preview){
                    options.templateModel = {
                        is_preview : is_preview
                    }
                }

                options.user_id = self.user_id;
                options.is_preview = is_preview;

                //判断是否从模特卡到达此页面
                if(model && model.cid)
                {
                    model_obj = route_params_obj;
                    options.model = model_obj;
                    options.is_new_fetch  = false;

                }
                else
                {
                    //建立model
                    model_obj = new model_card_model
                    ({
                        user_id : self.user_id
                    });

                    options.model = model_obj;
                    options.is_new_fetch  = true;
                }

                options.parentNode = self.$el;

                //生成大view，传入model对象 modify by hdw 2014.8.25 19:13
                var model_style_view_obj = new model_style_view(options).set_preview_data(preview_data).render();

            },
            page_before_show : function()
            {

            }
        }
    }]);

});
