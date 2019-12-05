
define(function(require,exports,module){
    var $ = require('$');
    var page_control = require('../../../frame/page_control');
    var Utility = require('../../../common/utility');
    var edit_condition_view = require('./view');

    var model_card_model = require('../model');

    page_control.add_page([function(){
        return{
           title:'选择风格',
            route:{
                'model_date/model_card/price_style_select' : 'model_date/model_card/price_style_select'
            },
            dom_not_cache: true,
            ignore_exist: true,
            transition_type : 'slide',
            page_init : function(app_view, route_params,other_info){

                console.log(other_info)

                this.edit_condition_view = new edit_condition_view({
                    parentNode : this.$el,
                    model : new model_card_model
                })._set_style_info(other_info).render();
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