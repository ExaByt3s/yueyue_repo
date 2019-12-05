<?php
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$pai_user_obj = POCO::singleton ( 'pai_user_class' );
//重庆排行榜单
 $ranking_array = array('44'=>array('每日新模', '44', '小时', ''),
					   //'45'=>array('魅力值排行榜', '45', '魅力', ''),
					   //'46'=>array('性感女王 私房专属','46', '魅力', ''),
					   '47'=>array('约约推荐 出片保证','47', '小时', ''),
					   //'48'=>array('更多模特', '48', '魅力', '')
					   );
                       


return $ranking_array;
?>