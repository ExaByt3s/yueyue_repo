<?php
include_once 'common.inc.php';
set_time_limit(0);
ignore_user_abort(true);
ini_set('memory_limit', '256M');
$s=microtime(true);

$obj = POCO::singleton('pai_mall_seller_member_level_class');
$seller_list = $obj->seller_get_all_seller();
pai_log_class::add_log(array(), 'level_start', 'level');
foreach($seller_list as $k => $v)
{
    //    //�̼ҵȼ���ok:105.47684311867
    //    $obj->seller_level($v['user_id']);
    //	pai_log_class::add_log($v['user_id'], '�̼ҵȼ�'.":".date('Y-m-d H:i:s'), 'level');
            
    //�̼��Ƽ���ok:155.59123492241
//    $obj->seller_is_recommend($v['user_id']);
//	pai_log_class::add_log($v['user_id'], '�̼��Ƽ�'.":".date('Y-m-d H:i:s'), 'level');
    
    //�̼���������,������Ʒ������ֵ,�����̼ҵ����С�ok:826.28728103638
    $obj->seller_goods_total_point_and_goods_statistical_step_and_seller_step_new($v['user_id']);
	pai_log_class::add_log($v['user_id'], '�̼���������,��Ʒ������ֵ'.":".date('Y-m-d H:i:s'), 'level');
    
}
pai_log_class::add_log((microtime(true)-$s), 'level_end', 'level');
exit( 'ok'.":".(microtime(true)-$s) );


