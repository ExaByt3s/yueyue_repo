/**
 * 模板help
 * --------------------
 * handlebars模板引擎的helps
 *
 */
define(function(require, exports, module) {
    var Handlebars = require('handlebars');
    var $ = require('$');

    var Utility = require('./utility');

    var currentDate = new Date;
    currentDate.setTime(currentDate.getTime());

    var Helps = module.exports = {
        formatString: function(str) {
            return new Handlebars.SafeString(str);
        },
        img_zoom : function(imgWidth, imgHeight,oriWidth)
        {
            var new_width = Utility.get_zoom_height_by_zoom_width(imgWidth, imgHeight,oriWidth);

            return 'width:' + new_width + 'px;height:' + imgHeight + 'px';
        },

        /**
         * 图片缩放
         * @param imgWidth
         * @param imgHeight
         * @param options
         */
        imageScale: function(imgWidth, imgHeight, options) {
            if (!imgWidth || !imgHeight) {
                return '';
            }

            var newWidth, newHeight;
            var maxWidth = options.hash.maxWidth;
            //var maxHeight = options.hash.maxHeight;

            if (imgWidth !== maxWidth) {
                newWidth = maxWidth;
                newHeight = Utility.int(imgHeight / (imgWidth / newWidth));
            } else {
                newWidth = imgWidth;
                newHeight = imgHeight;
            }

            return 'width:' + newWidth + 'px;height:' + newHeight + 'px';
        },
        add_one : function()
        {
            //利用+1的时机，在父级循环对象中添加一个_index属性，用来保存父级每次循环的索引
            this._index = index+1;
            //返回+1之后的结果
            return this._index;
        },

        /**
         * 日期格式化
         * @param timestamp
         * @returns {string}
         */
        formatDateTime: function(timestamp) {
            if (!timestamp) {
                return '';
            }

            var dateTime = new Date;
            dateTime.setTime(toInt(timestamp));

            var fullYear = dateTime.getFullYear();
            var month = toTwoDigitsNumber(dateTime.getMonth() + 1);

            var date = toTwoDigitsNumber(dateTime.getDate() + 1);
            var hours = dateTime.getHours();
            var minutes = toTwoDigitsNumber(dateTime.getMinutes());
            var seconds = toTwoDigitsNumber(dateTime.getSeconds());
            return [
                [fullYear, month, date].join('-'),
                [hours, minutes, seconds].join(':')
            ].join(' ');
        },

        /**
         * 格式化时间
         * @param timestamp
         * @returns {string}
         */
        formatTime: function(timestamp) {
            if (!timestamp) {
                return '';
            }

            var messageDate = new Date;
            messageDate.setTime(toInt(timestamp));

            var currFullYear = currentDate.getFullYear();
            var msgFullYear = messageDate.getFullYear();
            var currMonth = currentDate.getMonth() + 1;
            var msgMonth = messageDate.getMonth() + 1;
            var currDate = currentDate.getDate();
            var msgDate = messageDate.getDate();
            var currHours = currentDate.getHours();
            var msgHours = toTwoDigitsNumber(messageDate.getHours());
            var msgMinutes = toTwoDigitsNumber(messageDate.getMinutes());

            // UTC时间
            var timeZone = new Date;
            timeZone.setTime(messageDate.getTime() + 28800000);
            var timeZoneYear = timeZone.getUTCFullYear();
            var timeZoneMonth = timeZone.getUTCMonth() + 1;
            var timeZoneDate = timeZone.getUTCDate();
            var timeZoneHours = toTwoDigitsNumber(timeZone.getUTCHours());
            var timeZoneMinutes = toTwoDigitsNumber(timeZone.getUTCMinutes());

            var beforeTime = currentDate - messageDate;
            beforeTime = beforeTime > 0 ? beforeTime : 0;
            beforeTime = beforeTime / 1000;

            if (currFullYear != msgFullYear) {
                return timeZoneYear + '-' + timeZoneMonth + '-' + timeZoneDate + ' ' + timeZoneHours + ':' + timeZoneMinutes;
            }
            if (currMonth != msgMonth || currDate != msgDate) {
                return timeZoneMonth + '月' + timeZoneDate + '日 ' + timeZoneHours + ':' + timeZoneMinutes;
            }

            if (currHours != msgHours && beforeTime > 3600) {
                return '今天 ' + timeZoneHours + ':' + timeZoneMinutes;
            }

            if (beforeTime > 9 && beforeTime < 51) {
                beforeTime = beforeTime < 1 ? 1 : beforeTime;
                return Math.floor((beforeTime - 1) / 10) + 1 + '0秒前';
            }

            if (beforeTime <= 9) {
                return '刚刚';
            }

            return Math.floor(beforeTime / 60 + 1) + '分钟前';
        },
        /**
         * 判断两个指是否相等 (但准确度要扩展优化)
         * @param a
         * @param b
         * @param options
         * @returns {*}
         */
        if_equal : function(a,b,options)
        {
            if(a == b)
            {
                return options.fn(this);
            }
            else
            {
                return options.inverse(this);
            }

        }
    };

    // Helps
    // ---------
    function toInt(s) {
        return parseInt(s, 10);
    }

    /**
     * 转换为前导零的 2 位数字
     * @param s
     * @returns {boolean|string}
     */
    function toTwoDigitsNumber(s) {
        s < 10 && (s = '0' + s);
        return s;
    }
});