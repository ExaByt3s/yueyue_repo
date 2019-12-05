
define(function(require,exports,module){
    var $ = require('$');
    var page_control = require('../../../frame/page_control');
    var Utility = require('../../../common/utility');
    var edit_condition_view = require('./view');

    var model_card_model = require('../model');

    page_control.add_page([function(){
        return{
           title:'编辑约拍条件',
            route:{
                'model_date/model_card/edit_condition' : 'model_date/model_card/edit_condition'
            },
            dom_not_cache: true,
            ignore_exist: true,
            transition_type : 'slide',
            page_init : function(app_view, route_params,other_info){


                var login_info = Utility.user;
                var data_login_info = login_info.toJSON();

                this.edit_condition_view = new edit_condition_view({
                    templateModel : data_login_info,
                    parentNode : this.$el,
                    model : new model_card_model
                })._set_model_info(other_info.model_info).render();
            },
            page_before_show : function()
            {
                //is_style_select_back
                if(window._model_style_id && this.edit_condition_view['model_style_card' + '_' + window._model_style_id]){
                    this.edit_condition_view['model_style_card' + '_' + window._model_style_id].$('[data-role="model-style"]').html(window._model_style_text);
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