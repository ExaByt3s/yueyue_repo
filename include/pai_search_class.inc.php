<?php
/*
 * 搜索类
 */

class pai_search_class extends POCO_TDG
{
	
	/**
	 * 构造函数
	 *
	 */
	public function __construct()
	{
	
	}
	
	/*
	 * 获取结果
	 * @param bool $b_select_count
	 * @param string $where_str 查询条件
	 * @param string $order_by 排序
	 * @param string $limit 
	 * 
	 * return array
	 */
	public function get_search_list($search = '', $b_select_count = false, $limit = '0,10')
	{
		
		if (empty ( $search ))
		{
			return false;
		}
		
		if ($b_select_count)
		{
			$sql = "SELECT COUNT(*) as c FROM pai_db.pai_fulltext_tbl WHERE role='model' AND `fulltext` LIKE '%{$search}%' AND is_show = 1";
			
			$ret = db_simple_getdata ( $sql, true, 101 );
			
			return $ret ['c'];
		
		}
		else
		{
			$sql = "SELECT user_id,nickname FROM pai_db.pai_fulltext_tbl WHERE role='model' AND `fulltext` LIKE '%{$search}%' AND is_show = 1  LIMIT {$limit}";
			
			$ret = db_simple_getdata ( $sql, false, 101 );
			
			foreach ( $ret as $k => $val )
			{
				$ret [$k] ['user_icon'] = get_user_icon ( $val ['user_id'], 165 );
			}
			
			return $ret;
		}
	
	}
	
	/*
	 * 搜索接口 （组合时间和价格）
	 * @param array $query 查询条件 
	 * @param int $location_id 
	 * @param string $limit 
	 * 
	 * return array
	 */
	public function get_search_combo_list($query,$location_id=0,$limit='')
	{		
		$pic_obj = POCO::singleton ( 'pai_pic_class' );
		
		$sql_str_count = "SELECT COUNT(DISTINCTROW(L.user_id)) AS C
            FROM pai_db.pai_model_style_v2_tbl AS L, pai_db.pai_fulltext_tbl AS R 
            WHERE R.role='model' AND L.user_id=R.user_id AND R.is_show = 1 ";
		
		$sql_str = "SELECT L.user_id, L.price, L.hour, R.nickname, R.date_num, R.score_num 
            FROM pai_db.pai_model_style_v2_tbl AS L, pai_db.pai_fulltext_tbl AS R 
            WHERE R.role='model' AND L.user_id=R.user_id AND R.is_show = 1 ";
		
		if ($query ['tag'])
		{
			$tag = $query ['tag'];
			$sql_str .= " AND L.style LIKE '%{$tag}%' ";
			$sql_str_count .= " AND L.style LIKE '%{$tag}%' ";
		}
		if ($query ['price'])
		{
			$price_array = explode ( "-", $query ['price'] );
			$sql_str .= " AND L.price >= $price_array[0] AND L.price <= $price_array[1] ";
			$sql_str_count .= " AND L.price >= $price_array[0] AND L.price <= $price_array[1] ";
		}
		if ($query ['hour'])
		{
			$hour = $query ['hour'];
			$sql_str .= " AND L.hour = '{$hour}' ";
			$sql_str_count .= " AND L.hour = '{$hour}' ";
		}
		if ($query ['key'])
		{
			$key = ($query ['key']);
			
			$sql_str .= " AND R.`fulltext` LIKE '%{$key}%' ";
			$sql_str_count .= " AND R.`fulltext` LIKE '%{$key}%' ";
		}
		

		if ($location_id)
		{
			$sql_str .= " AND R.location_id=$location_id ";
			$sql_str_count .= " AND R.location_id=$location_id ";
		}
		
		
		$sql_str .= " GROUP BY L.user_id ";
		
		if ($query ['order'] == 'number')
		{
			$sql_str .= 'ORDER BY R.date_num DESC';
		}
		elseif ($query ['order'] == 'comment')
		{
			$sql_str .= 'ORDER BY R.score_num DESC';
		}
		else
		{
			$sql_str .= 'ORDER BY L.group_id ASC ';
		}
		if ($limit)
		{
			$sql_str .= " LIMIT {$limit}";
		}
		
		$result = db_simple_getdata ( $sql_str_count, TRUE, 101 );
		$data ['count'] = $result ['C'];
		
		
		$result = db_simple_getdata ( $sql_str, FALSE, 101 );
		foreach ( $result as $key => $val )
		{
			
			$data_val ['user_id'] = $val ['user_id'];
			$data_val ['price'] = '￥' . $val ['price'] . '.00(' . $val ['hour'] . '小时)';
			$data_val ['hour'] = '(' . $val ['hour'] . '小时)';
			$data_val ['nickname'] = $val ['nickname'];
			$data_val ['date_num'] = $val ['date_num'] . '次';
			$data_val ['score'] = $val ['score_num'];
			//$data_val['icon']       = get_user_icon($val['user_id'], $size = 468);
			//头像修改
			$data_val ['icon'] = '';
			$pic_array = $pic_obj->get_user_pic ( $val ['user_id'], $limit = '0,5' );
			foreach ( $pic_array as $k => $v )
			{
				$num = explode ( '?', $v ['img'] );
				$num = explode ( 'x', $num [1] );
				$num_v2 = explode ( '_', $num [1] );
				
				$width = $num [0];
				$height = $num_v2 [0];
				
				if ($width < $height)
				{
					$data_val ['icon'] = str_replace ( "_260.", "_440.", $v ['img'] );
					break;
				}
				$data_val ['icon'] = str_replace ( "_260.", "_440.", $v ['img'] );
			}
			
			$model_card_obj = POCO::singleton ( 'pai_model_card_class' );
			$model_card_info = $model_card_obj->get_model_card_info ( $data_val ['user_id'] );
			if ($model_card_info ['cover_img'])
			{
				$data_val ['icon'] = $model_card_info ['cover_img'];
			}
			
			$data_val ['url'] = 'http://yp.yueus.com/mobile/app?from_app=1#model_card/' . $val ['user_id'];
			
			
			// 评分星星
			$has_star = intval($val['score_num']);
			$miss_star = 5 - $has_star;
		
			for ($i=0; $i < 5; $i++) 
			{
				if($has_star>0)
				{
					$data_val['stars_list'][$i]['is_red'] = 1; 	
		
					$has_star--;
				}
				else
				{
					$data_val['stars_list'][$i]['is_red'] = 0; 	
		
					$miss_star--;						
				}
			}

			$data ['list'] [] = $data_val;
		}
		
		return $data;
	}

	/**
	 * @param $search_type  搜索类型 seller/goods
	 * @param $type_id		 分类ID
	 * @param $location_id  地区
	 * @return array|string
	 */
	public function get_search_tag($search_type, $type_id, $location_id)
	{
		if(!$search_type) return '缺少搜索类型';
		//if(!$type_id) return '缺少分类ID';

		$array_type[31] = '模特邀约';
		$array_type[5] = '摄影培训';
		$array_type[12] = '影棚租赁';
		$array_type[3] = '化妆服务';
		$array_type[40] = '摄影服务';
		$array_type[41] = '约美食';
		$array_type[43] = '约其他';



		$array_cms_id['seller']['101029001'][31] = 944;
		$array_cms_id['seller']['101029001'][5] = 945;
		$array_cms_id['seller']['101029001'][12] = 947;
		$array_cms_id['seller']['101029001'][3] = 946;
		$array_cms_id['seller']['101029001'][40] = 949;
		$array_cms_id['seller']['101029001'][41] = 950;
		$array_cms_id['seller']['101029001'][43] = 951;

		$array_cms_id['goods']['101029001'][31] = 952;
		//$array_cms_id['goods']['101029001'][31] = 944;
		$array_cms_id['goods']['101029001'][5] = 953;
		$array_cms_id['goods']['101029001'][12] = 955;
		$array_cms_id['goods']['101029001'][3] = 954;
		$array_cms_id['goods']['101029001'][40] = 957;
		$array_cms_id['goods']['101029001'][41] = 958;
		$array_cms_id['goods']['101029001'][43] = 959;

		$cms_id = $array_cms_id[$search_type][$location_id][$type_id];

		if(!$cms_id)
		{
			if($type_id)
			{
				$cms_id = $array_cms_id[$search_type][101029001][$type_id];
			}else{
				$cms_id = $array_cms_id[$search_type][101029001][31];
			}
		}
		;
		if(!$cms_id)
		{
			$cms_id = $array_cms_id[$search_type][101029001][31];
		}

		include_once('/disk/data/htdocs232/poco/pai/yue_admin/cms/cms_common.inc.php');
		$cms_obj 	= new cms_system_class();
		$record_list = $cms_obj->get_last_issue_record_list(false, '0,9', 'place_number DESC', $cms_id);

		foreach($record_list AS $key=>$val)
		{
			if(HEYH_TEST == 1) var_dump($val);

			$url = filter_var($val['link_url'], FILTER_VALIDATE_URL) ? $val['link_url'] : '';
			if (!empty($url)) {
				if ($val['link_type'] == 'inner_web') {
					$url = "yueyue://goto?type=inner_web&url=" . urlencode($val['link_url']) . "&wifi_url=" . urlencode(str_replace("yp.yueus.com", "yp-wifi.yueus.com", $val['link_url']));
				} elseif ($val['link_type'] == 'inner_app') {
					$scheme = parse_url($url, PHP_URL_SCHEME);  // 获取协议头
					if ($scheme != 'yueyue') {
						$url = "yueyue://goto?type=inner_app&pid=1220025&mid=122RO01001&user_id=" . $val['user_id'];
					}
				}
			}else{
				if($search_type == 'seller')
				{
					$url = "yueyue://goto?type=inner_app&pid=1220125&keyword=" . $val['title'] . "&type_id=" . $type_id;
				}else{
					$url = "yueyue://goto?type=inner_app&pid=1220126&keyword=" . $val['title'] . "&type_id=" . $type_id;
				}

			}


			$search_tag[] =array(
								'str' => $val['title'],
								'link' => $url,
							);
		}
		if(HEYH_TEST == 1)
		{
			echo "<BR>---------------------------<BR>";
			var_dump($search_tag);
			echo "<BR>---------------------------<BR>";
		}

		//$search_tag = array();
		//$search_tag[] = array('str'=>'欧美','link'=>'yueyue://goto?type=inner_app&pid=1220125&return_query=type_id%3d31%26detail%5b46%5d%3d47%26status%3d1%26is_show%3d1%26city%3d101029%26location_id%3d101029001%26is_black%3d0%26prices_list%3d0%26user_id%3d0%26s_action%3dgoods');
		//$search_tag[] = array('str'=>'情绪','link'=>'yueyue://goto?type=inner_app&pid=1220125&return_query=type_id%3d31%26detail%5b46%5d%3d48%26status%3d1%26is_show%3d1%26city%3d101029%26location_id%3d101029001%26is_black%3d0%26prices_list%3d0%26user_id%3d0%26s_action%3dgoods');
		//$search_tag[] = array('str'=>'糖水','link'=>'yueyue://goto?type=inner_app&pid=1220125&return_query=type_id%3d31%26detail%5b46%5d%3d49%26status%3d1%26is_show%3d1%26city%3d101029%26location_id%3d101029001%26is_black%3d0%26prices_list%3d0%26user_id%3d0%26s_action%3dgoods');
		//$search_tag[] = array('str'=>'古装','link'=>'yueyue://goto?type=inner_app&pid=1220125&return_query=type_id%3d31%26detail%5b46%5d%3d50%26status%3d1%26is_show%3d1%26city%3d101029%26location_id%3d101029001%26is_black%3d0%26prices_list%3d0%26user_id%3d0%26s_action%3dgoods');

		return $search_tag;
	}


	/**
	 * @param $search_type 搜索类型 seller/goods
	 * @param $type_id     分类ID
	 * @param $location_id 地区
	 * @return array
	 */
	public function get_search_recommend_content($search_type, $type_id, $location_id, $count)
	{
		if($search_type == 'seller') {
			$type = 'seller';
			$rank_id = 441;
			switch($type_id)
			{
				case 3: //化妆服务
					$rank_id = 450;
					break;

				case 5: //培训服务
					$rank_id = 444;
					break;

				case 12: //影棚服务
					$rank_id = 447;
					break;

				case 31: //模特服务
					$rank_id = 441;
					break;

				case 40: //摄影服务
					$rank_id = 456;
					break;

				case 41: //美食服务
					$rank_id = 459;
					break;

				case 43: //有趣服务
					$rank_id = 1059;
					break;

				case 42: //活动服务
					break;

				default:
					$rank_id = 441;
					break;

			}
		}elseif($search_type == 'goods'){
			$type = 'goods';
			$rank_id = 440;
			switch($type_id)
			{
				case 3: //化妆服务
					$rank_id = 449;
					break;

				case 5: //培训服务
					$rank_id = 443;
					break;

				case 12: //影棚服务
					$rank_id = 446;
					break;

				case 31: //模特服务
					$rank_id = 440;
					break;

				case 40: //摄影服务
					$rank_id = 455;
					break;

				case 41: //美食服务
					$rank_id = 458;
					break;

				case 43: //有趣服务
					$rank_id = 1058;
					break;

				case 42: //活动服务
					break;

				default:
					$rank_id = 440;
					break;

			}
		}else{
			$type = 'goods';
			$rank_id = 440;
		}
		include_once('/disk/data/htdocs232/poco/pai/yue_admin/cms/cms_common.inc.php');
		$cms_obj 			= new cms_system_class();
		$mall_seller_obj 	= POCO::singleton('pai_mall_seller_class');
		$task_goods_obj 	= POCO::singleton('pai_mall_goods_class');
		$default_cover 	= $mall_seller_obj->_seller_cover;  // 默认背景

		$record_list = $cms_obj->get_last_issue_record_list(false, '0,9', 'place_number DESC', $rank_id);


		foreach ($record_list as $value) {
			if (empty($value['link_url']) && $type == 'seller') {
				// 搜索商家
				$search_data = array(
//                'user_id' => $value['user_id'],
					'keywords' => $value['user_id'],
				);
				$result_list = $mall_seller_obj->user_search_seller_list($search_data, '0,1');
				//print_r($result_list);
				$search_result = $result_list['data'][0];
				if (empty($search_result)) {
					continue;
				}
				$seller_id = $search_result['user_id'];
				$name = get_seller_nickname_by_user_id($seller_id);
				$buy_num = $search_result['bill_finish_num']; // 购买人数
				$cover = empty($search_result['cover']) ? $default_cover : $search_result['cover'];
				$goods[] = array(
					'goods_id' => 0,
					'seller_user_id' => $seller_id,
					'seller' =>empty($name) ? '商家' : $name, // 暂时作为商家名称
					'titles' => $buy_num > 0 ? '已售：' . $buy_num  : '已售：' . $buy_num, // preg_replace('/&#\d+;/', '', $search_result['seller_introduce']),
					'images' => yueyue_resize_act_img_url($cover, '640'),
					'link' => 'yueyue://goto?seller_user_id=' . $seller_id . '&pid=1220103&type=inner_app', // 商家首页
					'prices' => empty($name) ? '商家' : $name, // 暂时作为商家名称
					'buy_num' => '提供' . $search_result['onsale_num'] . '个服务',
					'step' => '5分',
				);
				continue;
			}
			if (empty($value['link_url']) && $type ==  'goods') {
				// 搜索商品
				$r_data = array(
					'keywords' => $value['user_id'],
				);
			} else {
				$r_data = array();
				parse_str(urldecode($value['link_url']), $r_data);   // 解析成数组
			}
			$tmp_result = $task_goods_obj->user_search_goods_list($r_data, '0,1');
			$goods_info = $tmp_result['data'][0];
			//print_r($goods_info);
			if (empty($goods_info)) {
				$err_arr[] = array('$r_data' => $r_data, '$tmp_result' => $tmp_result);  // for debug;
				continue;
			}
			$goods_info_arr[] = array('$r_data' => $r_data, '$goods_info' => $goods_info);  // for debug
			$goods_id = $goods_info['goods_id'];
			$name = get_seller_nickname_by_user_id($goods_info['user_id']);
			$price_str = sprintf('%.2f', $goods_info['prices']);
			$prices_list = unserialize($goods_info['prices_list']);
			if (!empty($prices_list)) {
				$min = 0;
				foreach ($prices_list as $v) {
					$v = intval($v);
					if ($v <= 0) {
						continue;
					}
					$min = ($min > 0 && $min < $v) ? $min : $v;
				}
				if ($min > 0) {
					$price_str = sprintf('%.2f', $min) . '元 起';
				}
			}

			if($goods_info['review_times'])
			{
				$score = sprintf('%.1f', ceil($goods_info['total_overall_score'] / $goods_info['review_times'] * 2) / 2);
			}
			else
			{
				$score = 5;
			}
			$buy_num = $goods_info['bill_finish_num'];
			$cover = empty($goods_info['images']) ? $default_cover : $goods_info['images'];
			$goods[] = array(
				'goods_id' => $goods_id,
				'seller' => empty($name) ? '商家' : $name,
				'titles' => '[' . $task_goods_obj->get_goods_typename_for_type_id($goods_info[type_id]) .  ']' . preg_replace('/&#\d+;/', '', $goods_info['titles']),
				'images' => yueyue_resize_act_img_url($cover, '640'),
				'link' => 'yueyue://goto?goods_id=' . $goods_id . '&pid=1220102&type=inner_app',
				'prices' => '￥' . $price_str,
//            'buy_num' => '已有' . $goods_info['bill_finish_num'] . '人购买',
				'buy_num' => $buy_num > 0 ? '已有' . $buy_num . '人购买' : $name,
				'step' => $score . '分',
				'bill_finish_num' => '已售：' . ($goods_info['old_bill_finish_num'] + $goods_info['bill_finish_num']),
				'seller_img' => get_seller_user_icon($goods_info['user_id']),
			);

		}
		return $goods;
	}

}

?>