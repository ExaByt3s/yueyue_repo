<?php 
/*
 * ��ʱ���ȫ�ķ����������쳣
 */
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$ret = event_fulltext_search($time_querys,$price_querys,$start_querys, false, "0,5");

echo json_encode($ret);

?>