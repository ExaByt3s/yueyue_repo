<?php
/*
 * �Զ�������ڵĻ����ǩ�����Զ���ɣ�ûǩ�����Զ�ȡ��
 */

include_once ('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$ret=event_fulltext_search($time_querys = '', $price_querys = '', $start_querys = '', $b_select_count = false, $limit = '0,50',$location_id=101029001, $keyword='',$querys=array());

print_r($ret);

?>