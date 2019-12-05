<?php
/**
 * 商城卖家通用文件
 * @copyright 2015-06-18
 */
include(dirname(__FILE__).'/api_rest.php');
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php'); 
require_once('/disk/data/htdocs232/poco/pai/yue_admin/task/include/basics.fun.php');
include_once(dirname(__FILE__).'/include/output_function.php');
include_once(dirname(__FILE__).'/no_copy_online_config.inc.php');
define('TASK_TEMPLATES_ROOT',MALL_USER_DIR_APP."templates/default/");

// 设置UA常量
$user_agent_arr = mall_get_user_agent_arr();
if($user_agent_arr['is_pc'] == 1 )
{
	define('MALL_UA_IS_PC',1);
}
else
{
	define('MALL_UA_IS_MOBILE',1);
	
	if($user_agent_arr['is_weixin'] == 1 )
	{
		define('MALL_UA_IS_WEIXIN',1);
	}
	else if($user_agent_arr['is_yueyue_app'] == 1 )
	{
		define('MALL_UA_IS_YUEYUE',1);
	}
}



// 栏目关键字配置
$MALL_COLUMN_CONFIG = array
(
        // 摄影服务 品类列表
        '40' => array(
                "title_key" => '约约 - 摄影服务 你的专属摄影师',
                "keywords_key" => '约摄影师，约摄影，摄像，人像摄影，摄影圈，约拍，拍摄，摄影服务',
                "description_key" => '这里聚集众多摄影大咖，为你提供高质量和高性价比的精品摄影服务；我们不只是拍照记录，而是要让每张照片都说出你的故事；约摄，你的专属摄影师。',
                "key_nav" => '摄影服务'
        ),

        // 模特服务 品类列表
        '31' => array(
                "title_key" => '约约 - 模特邀约 100000+模特随心约',
                "keywords_key" => '约模特，模特，女神，摄影模特，平面模特，模特圈，封面模特，车展模特',
                "description_key" => '高效约拍全国红模，每天海量新模入驻，每年打造顶级新模；这里不仅能约到平面模特，还能约到商业活动模特，摄影、走秀、车展、淘宝各类模特应有尽有；约约在手，正妹我有！',
                "key_nav" => '模特邀约'
        ),

        // 摄影培训 品类列表
        '5' => array(
                "title_key" => '约约 - 摄影培训 轻松学摄影',
                "keywords_key" => '摄影培训，摄影入门，学摄影，摄影技巧，摄影课堂，网络摄影培训，摄影教学，摄影基础，摄影后期，摄影班，摄影私教班，摄影导师，摄影老师，摄影培训机构',
                "description_key" => '汇聚摄影名师大咔，万千课程满足您专业摄影培训及个性化的培训需求，让你体验轻松、高效、有趣学摄影！',
                "key_nav" => '摄影培训'
        ),

        // 化妆服务 品类列表
        '3' => array(
                "title_key" => '约约 - 化妆服务 专属你的美丽',
                "keywords_key" => '约化妆，化妆服务，彩妆服务，化妆，美容，美颜',
                "description_key" => '告诉我们你的美丽要求，约约即为你轻松搞定。约约平台拥有大量资深化妆服务提供商，擅长各类妆容及造型。在约约，一瞬间变美，就是这么简单。',
                "key_nav" => '化妆服务'
        ),

        // 影棚租赁 品类列表
        '12' => array(
                "title_key" => '约约 - 影棚租赁 你的地盘你做主',
                "keywords_key" => '约影棚，找场地，影棚租凭，摄影棚，影棚出租，场地租赁',
                "description_key" => '各类影棚，特色场地任你挑选，几十到几千平方应有尽有，上万种配套满足你的创作拍摄需求，一站式打包拍摄服务按需选择，约约在手，场地不愁。',
                "key_nav" => '影棚租赁'
        ),

        // 约美食 品类列表
        '41' => array(
                "title_key" => '约约 - 约美食 纯真的吃货联盟',
                "keywords_key" => '约美食，美食达人，中餐，西餐，日韩料理，自助餐，聚餐，餐饮服务，甜品，蛋糕，火锅',
                "description_key" => '约约携手美食达人，为用户量身订造特色佳肴。私人定制，饕客力推，无需等位，快来一起享受味蕾之旅吧！',
                "key_nav" => '约美食'
        ),
        // 约美食 品类列表
        '43' => array(
                "title_key" => '约约 - 约有趣',
                "keywords_key" => '约有趣',
                "description_key" => '约有趣',
                "key_nav" => '约有趣'
        ),
        // 约活动
        '42' => array(
                "title_key" => '约约 - 约活动',
                "keywords_key" => '约活动',
                "description_key" => '约活动',
                "key_nav" => '约活动'
        )
);



