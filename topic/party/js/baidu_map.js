define(function(require, exports, module) {

require('http://api.map.baidu.com/api?v=2.0&ak=KIpwmISmRtIMMssrIQ4NF9ji');
    exports.get_baidu_map = function()
    {
        var baidu_map_obj = new BMap();
        
        return baidu_map_obj;
    }
    
}