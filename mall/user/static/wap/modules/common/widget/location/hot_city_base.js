define('common/widget/location/hot_city_base', function(require, exports, module){ //热门城市配置城市

var hot_city_base = 
{

    title : "热门城市",
    id : "hot",
    data : [
        {
            city : "广州",
            location_id : 101029001
        },

        {
            city : "北京",
            location_id : 101001001
        },

        {
            city : "上海",
            location_id : 101003001
        },

        {
            city : "深圳",
            location_id : 101029002
        },

        {
            city : "香港",
            location_id : 101033001
        }
    ]

}

return hot_city_base; 
});