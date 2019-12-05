<?php 

/**
 * 定时执行文件
 * @authors xiao xiao  (xiaojm@yueus.com)
 * @date    2015-03-17 14:29:45
 * @version 1
 */
  include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
  $message_log_obj = POCO::singleton('pai_send_message_log_class');
  //开始运行时间
  $start_time = strtotime('9:00:00');

  //结束运行时间
  $end_time   = strtotime('18:00:00');
  //当前时分秒
  $sfm = strtotime(date('H:i:s', time()));
  if($sfm < $start_time || $sfm > $end_time)
  {
      echo "运行超时";
      exit;
  }

  $ret = $message_log_obj->send_group_message_by_log();
  var_dump($ret);

 ?>