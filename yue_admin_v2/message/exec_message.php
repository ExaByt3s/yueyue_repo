<?php 

/**
 * ��ʱִ���ļ�
 * @authors xiao xiao  (xiaojm@yueus.com)
 * @date    2015-03-17 14:29:45
 * @version 1
 */
  include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
  $send_message_log_v2_obj = POCO::singleton('pai_send_message_log_v2_class');
  //��ʼ����ʱ��
  $start_time = strtotime('9:00:00');

  //��������ʱ��
  $end_time   = strtotime('19:00:00');
  //��ǰʱ����
  $sfm = strtotime(date('H:i:s', time()));
  if($sfm < $start_time || $sfm > $end_time)
  {
      echo "���г�ʱ";
      exit;
  }

  $ret = $send_message_log_v2_obj->send_group_message_by_log();
  var_dump($ret);

 ?>