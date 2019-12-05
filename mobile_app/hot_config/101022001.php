<?php
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$pai_user_obj = POCO::singleton ( 'pai_user_class' );
//成都排行榜单
$ranking_array = array('49'=>array('每日新模', '49', '小时', ''),
					   //'50'=>array('魅力值排行榜', '50', '魅力', ''),
					   '51'=>array('性感女王 私房专属','51', '魅力', ''),
					   '52'=>array('约约推荐 出片保证','52', '小时', ''),
					   '53'=>array('更多模特', '53', '魅力', '')); 
                       


return $ranking_array;
?>