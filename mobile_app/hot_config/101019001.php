<?php
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$pai_user_obj = POCO::singleton ( 'pai_user_class' );
//北京排行榜单
$ranking_array = array('25'=>array('每日新模', '25', '小时', ''),
					   //'36'=>array('魅力值排行榜', '36', '魅力', ''),
					   '37'=>array('性感女王 私房专属','37', '魅力', ''),
					   '38'=>array('约约推荐 出片保证','38', '小时', ''),
					   '39'=>array('更多模特', '39', '魅力', '')); 
                       


return $ranking_array;
?>