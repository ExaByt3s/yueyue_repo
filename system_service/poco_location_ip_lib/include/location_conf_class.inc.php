<?php
/**
 * location_conf_class 地区ID定义操作类
 * @author ERLDY
 *
 */
if(!defined('LOCATION_SYS'))
{
	exit('Access Denied');
}

if (!class_exists('location_conf_class', false))
{


	class location_conf_class extends location_db_base_action_class
	{
		//操作表名
		protected $opt_tbl = 'location_config_tbl';


		/**
	 * 增加动态类型
	 *
	 * @param array $data
	 * @return bool
	 */
		public function add_location_config($data)
		{
			if ($data['lct_fid']*1<0)
			{
				$this->return_error(0, '父ID不能为空');
				return false;
			}
			if (empty($data['lct_icon']))
			{
				$this->return_error(0, '地区标识不能为空');
				return false;
			}
			if (empty($data['lct_name']))
			{
				$this->return_error(0, '地区名称不能为空');
				return false;
			}
			//$data['type_fid'] = substr($data['type_path'], -1);//截取父ID
			$ret = $this->_insert_table_filed($data);

			return $ret;
		}

		/**
	 * 修改动态类型
	 *
	 * @param array $data
	 * @param int $type_id
	 * @return bool
	 */
		public function update_location_config($data, $lct_id)
		{
			if ($data['lct_fid']*1<0)
			{
				$this->return_error(0, '父ID不能为空');
				return false;
			}
			if (empty($data['lct_icon']))
			{
				$this->return_error(0, '地区标识不能为空');
				return false;
			}
			if (empty($data['lct_name']))
			{
				$this->return_error(0, '地区名称不能为空');
				return false;
			}
			$lct_id = $lct_id*1;
			if ($lct_id<1)
			{
				$this->return_error(0, 'ID不能为空');
				return false;
			}
			$where_str = "lct_id = '{$lct_id}'";
			$ret = $this->_update_table_filed($data, $where_str);

			return $ret;
		}

		/**
	 * 删除指定分类和它下级分类
	 *
	 * @param int $lct_id
	 * @return bool
	 */
		public function del_location_config($lct_id)
		{
			$lct_id = $lct_id*1;
			if ($lct_id<1)
			{
				$this->return_error(0, 'ID不能为空');
				return false;
			}
			$id_arr = $this->get_lct_offspring_by_id($lct_id);
			if (empty($id_arr)) {
				return false;
			}
			if(!is_array($id_arr)) { $id_arr = array($id_arr); }
			$id_arr = implode(', ', $id_arr);
			$where_str = "lct_id IN ({$id_arr})";
			$ret = $this->_del_table_flied($where_str);
			return $ret;
		}

		/**
	 * 取得所有
	 *
	 * @return array
	 */
		public function get_location_all_list()
		{
			$sql = "SELECT lct_id,lct_fid,lct_icon,lct_name FROM member_db.{$this->opt_tbl}";
			$rows = $this->_get_table_list(false, $sql, '', 'lct_fid DESC');
			/*$arr = array();
			$this->return_tree_arr($rows, &$arr);*/

			return $rows;
		}

		/**
	 * 通过ID取得指定数据
	 *
	 * @param int $lct_id
	 * @return array
	 */
		public function get_location_info_by_id($lct_id)
		{
			global $__LOCATION_CONF_INFO__;
			$lct_id = (int)$lct_id;
			if ($lct_id<1)
			{
				$this->return_error(0, 'ID不能为空');
				return false;
			}
			if (empty($__LOCATION_CONF_INFO__[$lct_id]))
			{
				$where_str = "lct_id = '{$lct_id}'";
				$select_str = "lct_id,lct_fid,lct_icon,lct_name";
				$__LOCATION_CONF_INFO__[$lct_id] = $this->_get_table_row_info($where_str, $select_str);
			}

			return $__LOCATION_CONF_INFO__[$lct_id];
		}

		/**
	 * 通过标识取得指定数据
	 *
	 * @param string $lct_icon
	 * @return array
	 */
		public function get_type_info_by_icon($lct_icon)
		{
			if (empty($lct_icon))
			{
				$this->return_error(0, '标识不能为空');
				return false;
			}
			$where_str = "lct_icon = '{$lct_icon}'";
			$select_str = "lct_id,lct_fid,lct_icon,lct_name";
			return $this->_get_table_row_info($where_str, $select_str);
		}

		/**
	 * 通过标识取得指定下级分类ID
	 *
	 * @param string $lct_icon
	 * @return array
	 */
		public function get_lct_offspring_id_by_icon($lct_icon)
		{
			$row = $this->get_location_info_by_id($lct_icon);
			if (empty($row)) return null;
			$idx = $this->get_lct_offspring_by_id($row['lct_id']);
			return $idx;
		}

		/**
	 * 通过标识取得指定下级分类标识
	 *
	 * @param string $type_icon
	 * @return array
	 */
		public function get_lct_offspring_icon_by_icon($lct_icon)
		{
			$row = $this->get_location_info_by_icon($lct_icon);
			if (empty($row)) return null;

			$idx = $this->get_lct_offspring_by_id($row['lct_id']);
			$idx = implode(',', $idx);
			$sql = "SELECT lct_icon FROM member_db.{$this->opt_tbl} WHERE lct_id IN ({$idx})";
			$rows = $this->_get_table_list(false, $sql, '');
			$icon_arr = array();
			foreach ($rows as $item)
			{
				$icon_arr[] = $item['lct_icon'];
			}
			return $icon_arr;
		}

		/**
	 * 获取父分类二维数组，不含己身
	 * 
	 * @param int $type_id 己身所在id
	 * @return array
	 */
		public function _get_lct_forefathers_by_id($lct_id, $select_str = 'lct_id')
		{
			$data = array();
			$where_str = "lct_id = '" . $lct_id ."'";
			$row = $this->_get_table_row_info($where_str, 'lct_fid');
			$where_str = "lct_id = '".$row['lct_fid']."'";
			$row = $this->_get_table_row_info($where_str, $select_str);
			if($row) {   /* 如果存在有父类 */
				$data[] = $row;
				$data = array_merge($data, $this->_get_lct_forefathers_by_id($row['lct_id'])); /* 递归查询 */
				return $data;
			} else {
				return $data;
			}
		}

		/**
	 * 获取父类二维数组，含己身
	 * 
	 * @param int $type_id 己身所在id
	 * @return array
	 */
		public function get_lct_forefathers_by_id($lct_id, $select_str = 'lct_id') {
			$where_str = "lct_id = '" . $lct_id . "'";
			$row = $this->_get_table_row_info($where_str, $select_str);
			return array_reverse(array_merge(array($row), $this->_get_type_forefathers_by_id($lct_id)));
		}

		/**
	 * 获取指定id的所有下级分类，不含指定id
	 * 
	 * @param mixed $lct_id 指定id, 有可能是array
	 * @return array 所有下级id的一维数组
	 */
		public function _get_lct_offspring_by_id($lct_id) {
			$ids = array();
			if(!is_array($lct_id)) { $lct_id = array($lct_id); }
			$lct_id = implode(', ', $lct_id);
			$sql = "SELECT lct_id FROM member_db.{$this->opt_tbl} WHERE lct_fid IN (" . $lct_id . ")";
			$rows = $this->_get_table_list(false, $sql, '');
			if(!empty($rows)) {
				foreach ($rows as $item)
				{
					$ids[] = $item['lct_id'];
				}
				$ids = array_merge($ids, $this->_get_lct_offspring_by_id($ids));
				return $ids;
			} else {
				return $ids;
			}
		}

		/**
	 * 获得指定id的所有下级分类，含指定id
	 * 
	 * @param mixed $lct_id 指定id, 有可能是array
	 * @return array 所有下级的和指定id的一维数组
	 */
		public function get_lct_offspring_by_id($lct_id) {
			if(!is_array($lct_id)) { $lct_id = array($lct_id); }
			return array_merge($lct_id, $this->_get_lct_offspring_by_id($lct_id));
		}

		/**
	 * 返回树型结构数组
	 *
	 * @param array $arr 要处理的目标数组
	 * @param array $arr2 处理后的结果数组
	 * @param int $lct_fid 父ID
	 * @param int $lv 层次ID
	 */
		public function return_tree_arr($arr, &$arr2=array(), $lct_fid=0, $lv=0)
		{
			static $i = 0; //从0开始

			if ((bool)$arr)
			{
				foreach ($arr as $value)
				{
					if ($value['lct_fid']==$lct_fid)
					{
						$value['lv'] = $lv;
						$arr2[$i] = $value;
						$i++;
						$lv++;
						$this->return_tree_arr($arr,$arr2,$value['lct_id'],$lv--);
					}
				}
			}
		}

		/**
	 * 通过用户客户端IP取地区信息
	 *
	 * @param $ip IP地址
	 * @param $get_cache 默认取缓存数据
	 * @return array
	 */
		public function get_location_ip_2_location($ip, $get_cache = true)
		{
			$Ip = new IpLocation();
			if (empty($ip))
			{
				$ip = $Ip->getIP();
			}

			$location = $Ip->getlocation($ip);

			return $this->get_location_2_location_id($location['country'], $get_cache);
		}

		/**
	 * 通过地区名取地区ID
	 *
	 * @param $location 地区名
	 * @param $get_cache 默认取缓存数据
	 * @return array
	 */
		public function get_location_2_location_id($location, $get_cache = true)
		{
			if (empty($location))
			{
				$Ip = new IpLocation();
				$ip = $Ip->getIP();
				$location = $Ip->getlocation($ip);
				$location = $location['country'];
				unset($Ip);
			}

			//缓存数据
			$_cache_key = __CLASS__.'::'.__FUNCTION__.'::location_lib';
			$poco_cache_obj = new poco_cache_class();
			$province_arr = array();
			$cache_data = $poco_cache_obj->get_cache($_cache_key);
			if ($get_cache==true&&!empty($cache_data))
			{
				$province_arr = $cache_data;
			}
			else
			{
				$rows = $this->get_location_all_list();
				$tmp_arr = array();

				foreach ($rows as $item)
				{
					$tmp_arr[$item['lct_id']] = $item;
				}

				foreach ($tmp_arr as $item)
				{
					if ($item['lct_fid']!=0)
					{
						$province_arr[$tmp_arr[$item['lct_fid']]['lct_name']][$tmp_arr[$item['lct_fid']]['lct_name']] = $tmp_arr[$item['lct_fid']]['lct_icon'];
						$province_arr[$tmp_arr[$item['lct_fid']]['lct_name']][$item['lct_name']] = $item['lct_icon'];
					}
				}

				$_cache_time = 24*60*60;//默认一天
				$poco_cache_obj->save_cache($_cache_key, $province_arr, $_cache_time);
			}

			unset($rows);
			unset($tmp_arr);
			$locat = $location;
			$locat_a = array();
			$locat_a['address'] = $locat;
			//首先对地址进行省份和市匹配拆分
			$pattern = '/(.*)省(.*)市/isU';
			preg_match_all($pattern, $locat, $add_arr);
			$province = $add_arr[1][0];
			$city = $add_arr[2][0];
			if (!empty($province)&&!empty($city))
			{
				$locat_a['province'] = $province;
				$locat_a['city'] = $city;
				$locat_a['location_id'] = $province_arr[$province][$city];
				unset($add_arr);
				unset($province);
				unset($city);
			}

			//如果没法匹配省份和市，用市级名称匹配
			if (empty($locat_a['location_id']))
			{
				$have = false;
				foreach ($province_arr as $province=>$item)
				{
					$item = array_reverse($item, TRUE);
					foreach ($item as $city=>$location_id)
					{
						$city_tmp = str_replace('市', '', $city);
						if (in_array($city_tmp, array('龙江'))) //黑龙江 龙江的问题
						{
							if (2<=preg_match_all("/{$city_tmp}/", $locat, $match))
							{
								$have = true;
							}
						}
						elseif (strstr($locat, $city_tmp))
						{
							$have = true;
						}
						elseif ($city=='北京市'&&(strstr($locat, '中国')||strstr($locat, '首都')||strstr($locat, '中央')))
						{
							$have = true;
						}

						if ($have===true)
						{
							$locat_a['province'] = $province;
							$locat_a['city'] = $province==$city ? '' : $city;
							$locat_a['location_id'] = $location_id;
							break;
						}
					}
					if (!empty($locat_a['location_id'])) break;
				}
				unset($province);
				unset($city);
			}

			unset($province_arr);

			return $locat_a;
		}
	}
}
?>