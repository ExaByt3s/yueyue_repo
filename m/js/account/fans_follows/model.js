/**
 * 城市选择 模型
 * 汤圆 2014.8.19
 */
define(function(require, exports, module)
{
    var global_config = require('../../common/global_config');
    var Backbone = require('backbone');

    module.exports = Backbone.Model.extend
    ({
        idAttribute : 'user_id',
        default :
        {
            "user_id": "",
            "city": 0,
            "chest": 0,
            "cup": "",
            "waist": 0,
            "hip": 0,
            "height": 0,
            "weight": 0,
            "intro": "",
            "honor": "",
            "user_level": "",
            "cameraman_require": 0,
            "set_top": 0,
            "add_time": 0,
            "update_time": 0,
            "pv": 0,
            "user_icon_165": "",
            "user_icon_468": ""
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