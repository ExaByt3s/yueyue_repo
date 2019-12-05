/**
 * Created by nolest on 2014/9/28.
 *
 * 积分榜 次数榜
 */


define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../frame/page_control');
    var rank_view = require('./view');
    var global_config = require('../common/global_config');
    var utility = require('../common/utility');
    var rank_model = require('./model');
    var m_alert = require('../ui/m_alert/view');

    page_control.add_page([function()
    {
        return{
            title : '模特榜',
            route :
            {
                'rank/:type' : 'rank'
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

                var is_date = false;

                var is_score = false;

                var url;

                if(route_params_arr[0] == 'date')
                {
                    is_date = true;
                    url = global_config.ajax_url.get_model_date_rank_list;
                }
                else if(route_params_arr[0] == 'score')
                {
                    is_score = true;
                    url = global_config.ajax_url.get_model_score_rank_list;
                }

                var templateModel =
                {
                    is_date : is_date,
                    is_score : is_score
                };

                var model = new rank_model
                ({
                    url : url
                });

                var view = new rank_view
                ({
                    model : model,
                    parentNode : self.$el,
                    templateModel : templateModel
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
