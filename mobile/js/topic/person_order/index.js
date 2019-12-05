/**
 * Created by hudw on 2015-1-1.
 */
define(function(require,exports,module){
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var utility = require('../../common/utility');
    var form_view = require('./view');
    var m_alert = require('../../ui/m_alert/view');

    page_control.add_page([function()
    {
        return{
            title:'私人定制',
            route:
            {
                'topics/person_order(/:status)' : 'topics/person_order'
            },
            dom_not_cache: true,
            ignore_exist: true,
            transition_type : 'slide',
            page_init: function(page_view,route_params_arr,route_params_obj)
            {
                var self = this;

                self.form_view = new form_view
                ({
                    templateModel :
                    {

                    },
                    parentNode : self.$el
                }).render();
            },


            page_show:function()
            {
                var self = this;

            },
            page_before_remove: function()
            {
                var self = this;

            }

        }
    }]);
})