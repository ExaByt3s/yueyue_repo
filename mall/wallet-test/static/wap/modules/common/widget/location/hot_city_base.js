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
            city : "武汉",
            location_id : 101019001
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
            city : "成都",
            location_id : 101022001
        },

        {
            city : "重庆",
            location_id : 101004001
        },

        {
            city : "新疆",
            location_id : 101024001
        }
    ]

}

return hot_city_base; 
});