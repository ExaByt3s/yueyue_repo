<?php
/**
 * ��ȡ�����б�
 * 2015.10.13
 * @author ��Բ
 */
include_once 'config.php';

// ���ղ���
$topic_id = intval($_INPUT['topic_id']);
$remark = trim($_INPUT['remark']);
$remark = iconv("UTF-8", "gbk//TRANSLIT", $remark);  //��������

$trade_detail_obj = POCO::singleton ( 'pai_topic_class' );
$ret = $trade_detail_obj->add_task_enroll($topic_id,$yue_login_id,$remark);


// �������
mall_mobile_output($ret,false);

?>