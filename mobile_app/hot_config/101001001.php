<?php
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$pai_user_obj = POCO::singleton ( 'pai_user_class' );
//武汉排行榜单
$ranking_array = array('24'=>array('每日新模', 'whb', '小时', ''),
					   //'32'=>array('魅力值排行榜', 'wh_mlbxb', '魅力', ''),
					   //'33'=>array('性感女王 私房专属','wh_xgnw', '魅力', ''),
					   '34'=>array('约约推荐 出片保证','wh_yytj', '小时', ''),
					   //'35'=>array('更多模特', 'wh_gdmt', '魅力', '')
					   );     
                       


return $ranking_array;
?>