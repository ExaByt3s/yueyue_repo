/**
 * 发现页列表
 * nolestLam 2014.8
 */
define(function(require, exports, module)
{
    var Backbone = require('backbone');

    module.exports = Backbone.Model.extend
    ({
        default :
        {
            has_next_page : '',
            list :
            {
                city : '',
                id : '',
                price : '',
                pv : '',
                set_top : '',
                style : '',
                user_icon_165 : '',
                user_icon_468 : '',
                user_id : ''
            }

        },
        _setup_events : function ()
        {

        },
        initialize : function()
        {
            var self = this;

            self._setup_events();
        }

    });
});