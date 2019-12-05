<?php
/**
 * @Author      : hudw <hudw@poco.cn>
 * @Date        : 2015-11-05
 * @Description : 获取搜索筛选条件
 */
include_once 'config.php';

// 接收参数
$type_id = intval($_INPUT['type_id']);
$search_type = trim($_INPUT['search_type']);

// 筛选 类型整合
$type_obj = POCO::singleton('pai_mall_goods_type_attribute_class');
// 根据类型获取筛选数据
$filter_data = $type_obj->property_for_search_get_data($type_id);

$output_arr['filter_data'] = $filter_data;

// 输出数据
mall_mobile_output($output_arr,false);

?>