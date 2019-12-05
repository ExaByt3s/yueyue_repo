/**
 * 支付选项 模型
 * hudw 2014.9.5
 */
define(function(require, exports, module)
{
    var global_config = require('../../common/global_config');
    var Backbone = require('backbone');

    module.exports = Backbone.Model.extend
    ({

        default:
        {
            'total_price' : 0,
            'available_balance' : 0,
            'need_price' : 0,
            'pay_type' : '',
            'can_use_balance' : '',
            'is_support_outtime' : '',
            'is_support_now_out' : ''
        },

        /**
         * 安装事件
         * @private
         */
        _setup_events: function ()
        {
            var self = this;

        },

        initialize: function (options)
        {
            var self = this;

            self._setup_events();

        }
    });
});