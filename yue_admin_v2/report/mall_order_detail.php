<?php
/**
 * @desc:   ��������
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/9/1
 * @Time:   15:25
 * version: 1.0
 */
include_once('common.inc.php');
$mall_comment_log_obj = POCO::singleton( 'pai_mall_comment_log_class' );//������
$tpl = new SmartTemplate(REPORT_TEMPLATES_ROOT.'mall_order_detail.tpl.htm');

$id = intval($_INPUT['id']);
$date = trim($_INPUT['date']);

if($id <1)
{
    exit('�Ƿ�����');
}

$ret = $mall_comment_log_obj->get_comment_info_by_id($date,$id);
if(!is_array($ret)) $ret = array();

$order_info = unserialize($ret['order_info_ser']);

//print_r($order_info);

$tpl->assign($order_info);

$tpl->output();


