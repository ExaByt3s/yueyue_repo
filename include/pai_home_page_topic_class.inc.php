<?php
/*
 * 操作类
 */
include_once ('/disk/data/htdocs232/poco/pai/yue_admin/cms/cms_common.inc.php');

class pai_home_page_topic_class extends POCO_TDG
{
	var $cms_obj;
	
	function __construct()
	{
		$this->cms_obj = new cms_system_class ();
	}
	
	/*
	 * 获取大分类
	 */
	function get_big_category($location_id = 0)
	{
		switch ($location_id)
		{
			//广州
			case 101029001 :
				$issue_id = 9595;
				break;
			
			/*//武汉
			case 101019001 :
				$issue_id = 54;
				break;
			
			//北京
			case 101001001 :
				$issue_id = 55;
				break;
			
			//上海
			case 101003001 :
				$issue_id = 56;
				break;
			
			//成都
			case 101022001 :
				$issue_id = 58;
				break;
			
			//重庆
			case 101004001 :
				$issue_id = 57;
				break;*/
			
			default :
				$issue_id = 9595;
				break;
		}
		
		$info = $this->cms_obj->get_record_list_by_issue_id(false, $issue_id, "0,10", "place_number ASC", $freeze=null, $where_str="");
		foreach($info as $k=>$val)
		{
            $remark_arr = explode("|",$val['remark']);
			$info[$k]['pc_img'] = $val['img_url'];
			$info[$k]['b_id'] = $remark_arr[0];
            $info[$k]['type_id'] = $remark_arr[1];
		}
		return $info;
	}
	
	/*
	 * 获取小分类
	 */
	function get_small_category($issue_id=0)
	{
		$info = $this->cms_obj->get_record_list_by_issue_id(false, $issue_id, "0,10", "place_number ASC", $freeze=null, $where_str="");
		foreach($info as $k=>$val)
		{
			$content_arr = explode("|",$val['content']);
			$info[$k]['pc_img'] = $content_arr[0];
			$info[$k]['wap_img'] = $content_arr[1];
			$info[$k]['app_img'] = $content_arr[2];
			$info[$k]['s_id'] = $val['remark'];
		}
		return $info;
	}
	
	/*
	 * 获取小分类3个商品
	 */
	function get_small_category_3_goods($issue_id=0)
	{
		$info = $this->cms_obj->get_record_list_by_issue_id(false, $issue_id, "0,1", "place_number ASC", $freeze=null, $where_str="");
		$s_id = (int)$info[0]['remark'];
		
		return $this->get_goods_list($b_select_conut=false, $s_id, $limit="0,3", $order_by="place_number ASC", $freeze=null, $where_str="");
	}
	
	/*
	 * 获取商品列表
	 */
	function get_goods_list($b_select_conut=false, $issue_id, $limit="0,10", $order_by="place_number ASC", $freeze=null, $where_str="")
	{
		$info = $this->cms_obj->get_record_list_by_issue_id($b_select_conut, $issue_id, $limit, $order_by, $freeze, $where_str);
		foreach($info as $k=>$val)
		{
			$content_arr = explode("|",$val['content']);
			$remark_arr = explode("|",$val['remark']);
			$info[$k]['pc_img'] = $content_arr[0];
			$info[$k]['wap_img'] = $content_arr[1];
			$info[$k]['app_img'] = $content_arr[2];
			
			$info[$k]['star'] = (int)$remark_arr[0];
			$info[$k]['auth'] = $remark_arr[1];
		}
		return $info;
	}
	
	/*
	 * 获取首页专题列表
	 */
	function get_topic_list()
	{
		$info = $this->cms_obj->get_record_list_by_issue_id($b_select_conut=false, 10128, $limit="0,100", $order_by="place_number ASC", $freeze=null, $where_str="");
		return $info;
	}

	/*
	 * 首页评价列表
	 */
	function get_comment_list()
	{
		$info = $this->cms_obj->get_record_list_by_issue_id($b_select_conut=false, 10152, $limit="0,3", $order_by="place_number ASC", $freeze=null, $where_str="");
		foreach($info as $k=>$val)
		{
			$info[$k]['user_icon'] = get_user_icon($val['user_id']);
		}
		return $info;
	}
	
	
	/*
	 * 获取分类banner
	 */
	function get_banner_list($b_id)
	{
		switch ($b_id)
		{
		//找模特
			case 9421 :
				$issue_id = 11793;
				break;
	    //找场地
			case 9424 :
				$issue_id = 11796;
				break;
		//找外拍
			case 9948 :
				$issue_id = 11794;
				break;
		//找培训		
			case 9423 :
				$issue_id = 10268;
				break;
		//找化妆	
			case 9425 :
				$issue_id = 11795;
				break;
				
		//摄影服务
			case 27927 :
				$issue_id = 27928;
				break;
				 	
			default:
				$issue_id = 11793;
				break;

		}
		$info = $this->cms_obj->get_record_list_by_issue_id($b_select_conut=false, $issue_id, $limit="0,100", $order_by="place_number ASC", $freeze=null, $where_str="");
		foreach($info as $k=>$val)
		{
			$content_arr = explode("|",$val['content']);

			$info[$k]['pc_img'] = $val['img_url'];
			//$info[$k]['wap_img'] = $content_arr[1];
			$info[$k]['app_img'] = $val['img_url'];
			
		}
		return $info;
	}


    /*
     * 根据地区ID，分类ID获取BANNER
     */
    function get_banner_type_list($location_id,$type_id)
    {
        $location_id = (int)$location_id;
        $type_id = (int)$type_id;

        $location_id = $location_id ? $location_id : 101029001;
        $type_id = $type_id ? $type_id : 31;

        //广州
        $banner_data['101029001']['3'] = 11795;//化妆服务
        $banner_data['101029001']['5'] = 10268;//摄影培训
        $banner_data['101029001']['31'] = 11793;//模特服务
        $banner_data['101029001']['40'] = 27928;//摄影服务
        $banner_data['101029001']['12'] = 11796;//影棚租赁
        $banner_data['101029001']['42'] = 11794;//活动
        $banner_data['101029001']['41'] = 36370;//约美食
        $banner_data['101029001']['43'] = 36502;//约更多

        //武汉
        $banner_data['101019001']['3'] = 35058;//化妆服务
        $banner_data['101019001']['5'] = 35059;//摄影培训
        $banner_data['101019001']['31'] = 35060;//模特服务
        $banner_data['101019001']['40'] = 35061;//摄影服务
        $banner_data['101019001']['12'] = 35062;//影棚租赁
        $banner_data['101019001']['42'] = 35063;//活动
        $banner_data['101019001']['41'] = 36371;//约美食
        $banner_data['101019001']['43'] = 36503;//约更多

        //北京
        $banner_data['101001001']['3'] = 35064;//化妆服务
        $banner_data['101001001']['5'] = 35065;//摄影培训
        $banner_data['101001001']['31'] = 35066;//模特服务
        $banner_data['101001001']['40'] = 35067;//摄影服务
        $banner_data['101001001']['12'] = 35068;//影棚租赁
        $banner_data['101001001']['42'] = 35069;//活动
        $banner_data['101001001']['41'] = 36372;//约美食
        $banner_data['101001001']['43'] = 36504;//约更多

        //上海
        $banner_data['101003001']['3'] = 35070;//化妆服务
        $banner_data['101003001']['5'] = 35071;//摄影培训
        $banner_data['101003001']['31'] = 35072;//模特服务
        $banner_data['101003001']['40'] = 35073;//摄影服务
        $banner_data['101003001']['12'] = 35074;//影棚租赁
        $banner_data['101003001']['42'] = 35075;//活动
        $banner_data['101003001']['41'] = 36373;//约美食
        $banner_data['101003001']['43'] = 36505;//约更多

        //重庆
        $banner_data['101004001']['3'] = 35076;//化妆服务
        $banner_data['101004001']['5'] = 35077;//摄影培训
        $banner_data['101004001']['31'] = 35078;//模特服务
        $banner_data['101004001']['40'] = 35079;//摄影服务
        $banner_data['101004001']['12'] = 35080;//影棚租赁
        $banner_data['101004001']['42'] = 35081;//活动
        $banner_data['101004001']['41'] = 36374;//约美食
        $banner_data['101004001']['43'] = 36506;//约更多

        //成都
        $banner_data['101022001']['3'] = 35082;//化妆服务
        $banner_data['101022001']['5'] = 35083;//摄影培训
        $banner_data['101022001']['31'] = 35084;//模特服务
        $banner_data['101022001']['40'] = 35085;//摄影服务
        $banner_data['101022001']['12'] = 35086;//影棚租赁
        $banner_data['101022001']['42'] = 35087;//活动
        $banner_data['101022001']['41'] = 36375;//约美食
        $banner_data['101022001']['43'] = 36507;//约更多

        //西安
        $banner_data['101015001']['3'] = 35093;//化妆服务
        $banner_data['101015001']['5'] = 35092;//摄影培训
        $banner_data['101015001']['31'] = 35091;//模特服务
        $banner_data['101015001']['40'] = 35090;//摄影服务
        $banner_data['101015001']['12'] = 35089;//影棚租赁
        $banner_data['101015001']['42'] = 35088;//活动
        $banner_data['101015001']['41'] = 36376;//约美食
        $banner_data['101015001']['43'] = 36508;//约更多


        //深圳
        $banner_data['101029002']['3'] = 37061;//化妆服务
        $banner_data['101029002']['5'] = 37058;//摄影培训
        $banner_data['101029002']['31'] = 37059;//模特服务
        $banner_data['101029002']['40'] = 37063;//摄影服务
        $banner_data['101029002']['12'] = 37062;//影棚租赁
        $banner_data['101029002']['42'] = 37060;//活动
        $banner_data['101029002']['41'] = 37065;//约美食
        $banner_data['101029002']['43'] = 37066;//约更多


        $issue_id = $banner_data[$location_id][$type_id];

        if(empty($issue_id))
        {
            return false;
        }

        $info = $this->cms_obj->get_record_list_by_issue_id($b_select_conut=false, $issue_id, $limit="0,100", $order_by="place_number ASC", $freeze=null, $where_str="");
        foreach($info as $k=>$val)
        {
            $content_arr = explode("|",$val['content']);

            $info[$k]['pc_img'] = $val['content'];
            $info[$k]['app_img'] = $val['img_url'];
            $info[$k]['app_img_v2'] = $val['remark'];
        }
        return $info;

    }
	
	/*
	 * 获取分类文案
	 */
	function get_category_text($b_id)
	{
		switch ($b_id)
		{
		//找模特
			case 9421 :
				$text['top1'] = "找模特";
				$text['top2'] = "女神驾到，随时约约";
				$text['top3'] = "妩媚迷人满足你各种拍摄风格";
				$text['top4'] = "女神出没，心动不如行动 ";
				break;
	    //找场地
			case 9424 :
				$text['top1'] = "找场地";
				$text['top2'] = "轻松预约影棚";
				$text['top3'] = "影棚面积从50平米到几千平米不等，不同档次的灯光布景，约约满足你的各种创作拍摄需求。";
				$text['top4'] = "最火爆的影棚集结号";
				break;
		//找外拍
			case 9948 :
				$text['top1'] = "找外拍";
				$text['top2'] = "最好的外拍活动平台";
				$text['top3'] = "最新、最多、最好的摄影活动。一个平台，玩转所有外拍活动";
				$text['top4'] = "最好的活动等你来玩";
				break;
		//找培训		
			case 9423 :
				$text['top1'] = "找培训	";
				$text['top2'] = "全国领先的摄影培训平台";
				$text['top3'] = "名师大咖，海量课程，放心选择";
				$text['top4'] = "更多精彩课程尽在约约课堂";
				break;
		//找化妆	
			case 9425 :
				$text['top1'] = "找化妆";
				$text['top2'] = "你的专属化妆师";
				$text['top3'] = "在约约，一瞬间变美，就是这么简单。";
				$text['top4'] = "美丽奇迹 从此开始";
				break;
				
		//摄影服务
			case 27927 :
				$text['top1'] = "找摄影";
				$text['top2'] = "轻松约摄影";
				$text['top3'] = "海量摄影大师、机构，随时随地为你提供专业摄影服务";
				$text['top4'] = "";
				break;
				
			default:
				$text['top1'] = "找模特";
				$text['top2'] = "女神驾到，随时约约";
				$text['top3'] = "妩媚迷人满足你各种拍摄风格";
				$text['top4'] = "女神出没，心动不如行动 ";
				break;
		}
		
		return $text;
	}


    /*
	 * 获取分类文案
	 */
    function get_category_text_by_type_id($type_id)
    {
        switch ($type_id)
        {
            //找模特
            case 31 :
                $text['top1'] = "找模特";
                $text['top2'] = "女神驾到，随时约约";
                $text['top3'] = "妩媚迷人满足你各种拍摄风格";
                $text['top4'] = "女神出没，心动不如行动 ";
                break;
            //找场地
            case 12 :
                $text['top1'] = "找场地";
                $text['top2'] = "轻松预约影棚";
                $text['top3'] = "影棚面积从50平米到几千平米不等，不同档次的灯光布景，约约满足你的各种创作拍摄需求。";
                $text['top4'] = "最火爆的影棚集结号";
                break;
            //找外拍
            case 99 :
                $text['top1'] = "找外拍";
                $text['top2'] = "最好的外拍活动平台";
                $text['top3'] = "最新、最多、最好的摄影活动。一个平台，玩转所有外拍活动";
                $text['top4'] = "最好的活动等你来玩";
                break;
            //找培训
            case 5 :
                $text['top1'] = "找培训	";
                $text['top2'] = "全国领先的摄影培训平台";
                $text['top3'] = "名师大咖，海量课程，放心选择";
                $text['top4'] = "更多精彩课程尽在约约课堂";
                break;
            //找化妆
            case 3 :
                $text['top1'] = "找化妆";
                $text['top2'] = "你的专属化妆师";
                $text['top3'] = "在约约，一瞬间变美，就是这么简单。";
                $text['top4'] = "美丽奇迹 从此开始";
                break;

            //摄影服务
            case 40 :
                $text['top1'] = "找摄影";
                $text['top2'] = "轻松约摄影";
                $text['top3'] = "海量摄影大师、机构，随时随地为你提供专业摄影服务";
                $text['top4'] = "";
                break;


            //约美食
            case 41 :
                $text['top1'] = "约美食";
                $text['top2'] = "美食盛宴，约定你";
                $text['top3'] = "携手达人，量身订造特色佳肴，共享味蕾之旅！";
                $text['top4'] = "";
                break;

        }

        return $text;
    }
	
	/*
	 * 获取分类下面商品
	 */
	function get_category_goods($b_id)
	{
		switch ($b_id)
		{
		//找模特
			case 9421 :
				$issue_id = 12132;
				break;
	    //找场地
			case 9424 :
				$issue_id = 12107;
				break;
		//找外拍
			case 9948 :
				$issue_id = 12131;
				break;
		//找培训		
			case 9423 :
				$issue_id = 12105;
				break;
		//找化妆	
			case 9425 :
				$issue_id = 12106;
				break;
				
			default:
				$issue_id = 12132;
				break;

		}
		
		$info = $this->cms_obj->get_record_list_by_issue_id($b_select_conut=false, $issue_id, $limit="0,3", $order_by="place_number ASC", $freeze=null, $where_str="");
		foreach($info as $k=>$val)
		{
			$content_arr = explode("|",$val['content']);
			$remark_arr = explode("|",$val['remark']);
			$info[$k]['pc_img'] = $content_arr[0];
			$info[$k]['wap_img'] = $content_arr[1];
			$info[$k]['app_img'] = $content_arr[2];
		}
		return $info;
	}


    function get_pc_home_category($location_id)
    {
        include_once('/disk/data/htdocs232/poco/pai/yue_admin/cms/cms_common.inc.php');
        $cms_obj = new cms_system_class();

        $yuepai_arr = array(
            101029001 => 312, //广州
            101019001 => 313, //武汉
            101001001 => 314, //北京
            101003001 => 315, //上海
            101004001 => 316, //重庆
            101022001 => 318, //成都
            101015001 => 320, //西安
            101024001 => 321, //新疆
        );
        $cover_img_arr = array(
            md5('模特邀约')=>'http://image19-d.yueus.com/yueyue/cms/20150825/47052015082509265062824369.png',
            md5('摄影服务')=>'http://image19-d.yueus.com/yueyue/cms/20150825/4045201508250928369016023.png',
            md5('摄影培训')=>'http://image19-d.yueus.com/yueyue/cms/20150825/82842015082509285947314889.png',
            md5('化妆服务')=>'http://image19-d.yueus.com/yueyue/cms/20150825/14262015082509292126338879.png',
            md5('影棚租赁')=>'http://image19-d.yueus.com/yueyue/cms/20150825/81152015082509295525782751.png',
            md5('约美食')=>'http://image19-d.yueus.com/yueyue/20150918/20150918180354_857294_10002_13381.png?320x240_130',

        );

        // 大分类 按钮
        $ico_key = isset($yuepai_arr[$location_id]) ? $yuepai_arr[$location_id] : 312;
        $ico_result = $cms_obj->get_last_issue_record_list(false, '0,10', 'place_number ASC', $ico_key);


        $ico_list = array();
        foreach ($ico_result as $value) {
            list($dsmall, $dbig) = explode('|', $value['content']);   // 触发前
            list($hsmall, $hbig) = explode('|', $value['remark']);  // 触发后
            if($value['title']!='外拍活动') {
                $ico_list[] = array(
                    'str' => $value['title'],
                    'url' => $value['link_url'],
                    'img_url' => $cover_img_arr[md5($value['title'])],
                );
            }
        }

        return $ico_list;
    }

    /*
     * 3.1.0版PC首页分类
     */
    function get_pc_home_category_3_1_0($location_id)
    {
        include_once('/disk/data/htdocs232/poco/pai/yue_admin/cms/cms_common.inc.php');
        include_once('/disk/data/htdocs232/poco/pai/mall/user/api_rest.php');
        $cms_obj = new cms_system_class();

        $yuepai_arr = array(
            '101029001' => 587, //广州
            '101001001' => 703, //北京
            '101003001' => 705, //上海
            '101022001' => 708, //成都
            '101004001' => 707, //重庆
            '101015001' => 710, //西安
            '101029002' => 875, //深圳
        );
        $cover_img_arr = array(
            md5('约模特')=>'http://image19-d.yueus.com/yueyue/20151027/20151027114649_160564_10002_24233.png?236x176_130',
            md5('约摄影')=>'http://image19-d.yueus.com/yueyue/20151027/20151027114727_907890_10002_24235.png?236x176_130',
            md5('约培训')=>'http://image19-d.yueus.com/yueyue/20151027/20151027114748_279059_10002_24238.png?236x176_130',
            md5('约化妆')=>'http://image19-d.yueus.com/yueyue/20151027/20151027114805_19341_10002_24241.png?236x176_130',
            md5('约有趣')=>'http://image19-d.yueus.com/yueyue/20151027/20151027114821_33090_10002_24244.jpg?236x176_120',
            md5('约美食')=>'http://image19-d.yueus.com/yueyue/20151027/20151027114841_550033_10002_24246.png?236x176_130',
            md5('商业定制')=>'http://image19-d.yueus.com/yueyue/20151027/20151027114855_878951_10002_24248.png?236x176_130',
            md5('约活动')=>'http://image19-d.yueus.com/yueyue/20151027/20151027114909_240924_10002_24249.png?236x176_130',

        );

        // 大分类 按钮
        $ico_key = isset($yuepai_arr[$location_id]) ? $yuepai_arr[$location_id] : $yuepai_arr['101029001'];
        $ico_result = $cms_obj->get_last_issue_record_list(false, '0,8', 'place_number ASC', $ico_key);


        $ico_list = array();
        foreach ($ico_result as $value) {
            list($dsmall, $dbig) = explode('|', $value['content']);   // 触发前
            list($hsmall, $hbig) = explode('|', $value['remark']);  // 触发后

            $ico_list[] = array(
                'str' => $value['title'],
                'url' => mall_yueyue_app_to_http($value['link_url']),
                'img_url' => $cover_img_arr[md5($value['title'])],
            );

        }

        return $ico_list;
    }
	
}

?>

