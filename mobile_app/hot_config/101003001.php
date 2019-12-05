<?php
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$pai_user_obj = POCO::singleton ( 'pai_user_class' );
//上海排行榜单
$ranking_array = array('26'=>array('每日新模', '26', '小时', ''),
				   //'40'=>array('魅力值排行榜', '40', '魅力', ''),
				   '41'=>array('性感女王 私房专属','41', '魅力', ''),
				   '42'=>array('约约推荐 出片保证','42', '小时', ''),
				   '43'=>array('更多模特', '43', '魅力', '')); 
                       


return $ranking_array;
?>