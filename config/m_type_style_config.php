<?php
/** 
 * 模特的拍摄类型跟风格配置数组
 * 
 * author 星星
 * 
 * 2014-6-19
 */





//模特类型
$modle_type_config_arr = array(

    array("value"=>1,"value_name"=>"室内人像"),
    array("value"=>2,"value_name"=>"外景人像"),
    array("value"=>3,"value_name"=>"平面广告"),
	array("value"=>4,"value_name"=>"户外写真"),
    array("value"=>5,"value_name"=>"外拍活动"),
    array("value"=>6,"value_name"=>"妆面"),
    array("value"=>7,"value_name"=>"其他"),
);

//拍摄风格
$modle_style_config_arr = array(

    array("value"=>1,"value_name"=>"欧美"),
    array("value"=>2,"value_name"=>"韩服"),
    array("value"=>3,"value_name"=>"日系"),
    array("value"=>4,"value_name"=>"性感"),
    array("value"=>5,"value_name"=>"内衣"),
    array("value"=>6,"value_name"=>"人体"),
    array("value"=>7,"value_name"=>"活动"),
    array("value"=>8,"value_name"=>"中老年"),
    array("value"=>9,"value_name"=>"其他"),
);

$model_cancel_config_arr = array(
    
    1=>"价格原因",
    2=>"时间原因",
    3=>"地点原因",
    4=>"风格原因",
    5=>"摄影水平",
    6=>"VIP等级",

    
    
);

$model_cancel_config_square_arr = array(
    
    array("value"=>1,"value_name"=>"价格原因"),
    array("value"=>2,"value_name"=>"时间原因"),
    array("value"=>3,"value_name"=>"地点原因"),
    array("value"=>4,"value_name"=>"风格原因"),
    array("value"=>5,"value_name"=>"摄影水平"),
    array("value"=>6,"value_name"=>"VIP等级"),
    
    
);
?>