define(function(require, exports, module) {
    function isFunction(o) {
        return typeof o === 'function';
    }

    module.exports = function(imgUrl, options) {
        var img = new Image();

        img.src = imgUrl;

        // 如果图片被缓存，则直接返回缓存数据
        if (img.complete) {
            isFunction(options.load) && options.load.call(img);
            return;
        }

        // 加载错误后的事件
        img.onerror = function () {
            isFunction(options.error) && options.error.call(img);
            img = img.onload = img.onerror = null;

            //delete img;
        };

        // 完全加载完毕的事件
        img.onload = function () {
            isFunction(options.load) && options.load.call(img);

            // IE gif动画会循环执行onload，置空onload即可
            img = img.onload = img.onerror = null;

            //delete img;
        };
    };

});