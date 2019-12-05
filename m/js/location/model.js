/**
 * 城市选择 模型
 * 汤圆 2014.8.19
 */
define(function(require, exports, module)
{
    var global_config = require('../common/global_config');
    var Backbone = require('backbone');

    module.exports = Backbone.Model.extend
    ({
        idAttribute : 'location_id',
        default :
        {
            "location_id": "",
            "location_name": ""
        },
        /**
         * 安装事件
         * @private
         */
        _setup_events : function()
        {

        },
        parse : function(response)
        {
            if(response.result_data)
            {
                return response.result_data
            }

            return response;
        },
        initialize : function()
        {
            var self = this;

            self._setup_events();
        }
    });
});