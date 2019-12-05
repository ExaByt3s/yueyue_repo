<?php
/**
 * 获取促销列表
 * 2015.10.13
 * @author 汤圆
 */
include_once 'config.php';

// 接收参数
$topic_id = intval($_INPUT['topic_id']);

$trade_detail_obj = POCO::singleton ( 'pai_topic_class' );
$ret = $trade_detail_obj->add_task_enroll($topic_id,$yue_login_id);


// 输出数据
mall_mobile_output($ret,false);

?>