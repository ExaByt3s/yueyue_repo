<?php
/**
 * 统计类
 * 
 * @author ljl
 */

class pai_mall_statistical_class extends POCO_TDG
{
    //缓存前缀
	public $_redis_cache_name_prefix = "G_YUEUS_MALL_GOODS_ID_TOTAL_VIEWS_";
    
    public $_redis_cache_goods_id_uv_prefix = "G_YUEUS_MALL_GOODS_ID_UV_TOTAL_VIEWS_";
    //刷新时间
    public $_seconds = 3;
    //浏览次数
    public $_view_numbers = array(1,2,3,4,5,6,7,8,9,10);
    //作弊开关
    public $_button = false;
    //白名单
    public $_white_name = array();
    //黑名单
    public $_black_name = array();
    
	/**
	 * 
	 */
	public function __construct()
	{
		$this->setServerId('101');
		$this->setDBName('mall_db');
	}
	
	/**
	 *
	 */
	private function set_mall_goods_statistical_tbl()
	{
		$this->setTableName('mall_goods_statistical_tbl');
	}
    
    /**
     * 删除缓存
     * @param type $goods_id
     * @return boolean
     */
    private function del_goods_id_total_views_cache($mod)
	{
		$cache_key = $this->_redis_cache_name_prefix."{$mod}";
		POCO::deleteCache($cache_key);
		return true;
	}
    
    /**
     * 获取缓存
     * @param type $goods_id
     * @return boolean
     */
    private function get_goods_id_total_views_cache($goods_id)
	{
        $goods_id = (int)$goods_id;
        if( ! $goods_id )
        {
            return false;
        }
        $mod = $goods_id%100;
		$cache_key = $this->_redis_cache_name_prefix."{$mod}";
		return POCO::getCache($cache_key);
		
	}
    
    /**
     * 根据goods_id 设置缓存
     * @param type $goods_id
     * @param type $view_numbers
     * @return boolean
     */
    private function set_goods_id_total_views_cache($goods_id,$view_numbers=1)
	{
        $goods_id = (int)$goods_id;
        $view_numbers = (int)$view_numbers;
        if( ! $goods_id || ! $view_numbers )
        {
            return false;
        }        
        $org_cache = $this->get_goods_id_total_views_cache($goods_id);
		$org_cache['goods_id_total'][$goods_id]['view_numbers']+=$view_numbers;

		$mod = $goods_id%100;
        $cache_key = $this->_redis_cache_name_prefix."{$mod}";
        POCO::setCache($cache_key, $org_cache, array('life_time'=>86400));        
        return true;
	}
    
    /**
     * 根据goods_id同步其他表
     * @param type $goods_id
     * @return boolean
     */
    private function sync_goods_id_other_tbl($goods_id)
    {
        $goods_id = (int)$goods_id;
        if( ! $goods_id )
        {
            return false;
        }
        $goods_obj = POCO::singleton('pai_mall_goods_class');
		$goods_info = $goods_obj->get_goods_info($goods_id);
        return $goods_obj->exec_cmd_pai_mall_synchronous_goods($goods_id,$goods_info['goods_data']['type_id'],2,false);
    }
    
    /**
     * 更新统计表的数量
     * @param type $goods_id
     * @param type $view_number
     * @return boolean
     */
    private function update_goods_statistica_view_number($goods_id,$view_number)
    {
        $this->setDBName('mall_db');
        
        $goods_id = (int)$goods_id;
        $view_number = (int)$view_number;
        if( ! $goods_id || ! $view_number )
        {
            return false;
        }
        $sql = "update {$this->_db_name}.mall_goods_statistical_tbl set `view_num`=`view_num`+{$view_number} where goods_id='{$goods_id}'";
        return $this->query($sql);
    }
    
    /**
     * 操作goods_id_total的内存数据,对goods_id进行访问量的更新
     */
    public function do_goods_id_view_numbers_update()
    {
        $goods_obj = POCO::singleton('pai_mall_goods_class');
		$j = array();
		$max = 0;
        for($i=0;$i<100;$i++)
        {
            $cache_key = $this->_redis_cache_name_prefix."{$i}";
            $cache_data = poco::getCache($cache_key);
            if( ! empty($cache_data) )
            {
                //goods_id=>array('view_numbers'=>1)
                foreach($cache_data['goods_id_total'] as $k => $v)
                {
                    //更新到统计表
                    $this->update_goods_statistica_view_number($k,$v['view_numbers']);                    
                    //同步更新到其他表
					$goods_info = $goods_obj->get_goods_info($k);
					if($goods_info)
					{
						$goods_obj->exec_cmd_pai_mall_synchronous_goods($k,$goods_info['goods_data']['type_id'],2,false);
						$j['detail'][$i]['count'] += 1;
						$j['detail'][$i]['data'][$k] = $v['view_numbers'];
						$j['log'] .= $k."|".$v['view_numbers']."\r\n";
						if($v['view_numbers']>$max)
						{
							$j['count_v'] += $v['view_numbers'];
							$j['max'] = $v['view_numbers'];
							$j['max_url'] = "http://www.yueus.com/mall/user/goods/service_detail.php?goods_id=".$k;
							$max = $v['view_numbers'];
						}
					}
                    //$this->sync_goods_id_other_tbl($k);
                }
            }            
            unset($cache_key);
            unset($cache_data);
            //清空缓存
            $this->del_goods_id_total_views_cache($i);
        }
        return $j;
    }
    
    /**
     * uv 删除缓存
     * @param type $mod
     * @return boolean
     */
    public function uv_del_cache($mod)
    {
        $cache_key = $this->_redis_cache_goods_id_uv_prefix."{$mod}";
		POCO::deleteCache($cache_key);
		return true;
    }
    
    /**
     * uv 拿缓存
     * @param type $goods_id
     * @return boolean
     */
    public function uv_get_cache($goods_id)
    {
        $goods_id = (int)$goods_id;
        if( ! $goods_id )
        {
            return false;
        }
        $mod = $goods_id%100;
		$cache_key = $this->_redis_cache_goods_id_uv_prefix."{$mod}";
		return POCO::getCache($cache_key);
    }
    
    /**
     * uv 设定缓存
     * @param type $goods_id
     * @return boolean
     */
    public function uv_set_cache($goods_id)
    {
        global $yue_login_id;
        
        $goods_id = (int)$goods_id;
        
        if( ! $goods_id )
        {
            return false;
        }
        
        //客户端数据
        $ip = mall_get_ip();
        //来源url
        //HTTP_REFERER
        $source_url = $_SERVER['HTTP_REFERER'];
        //执行的url 
        $go_url = $_SERVER['SCRIPT_FILENAME'];
        $browse = mall_get_browser();
        $system = mall_get_os();
        
        $org_cache = $this->uv_get_cache($goods_id);
        $unit = array(
            'goods_id'=>$goods_id,
            'user_id'=>$yue_login_id,
            'ip'=>$ip,
            'v_time'=>time(),
            'source_url'=>$source_url,
            'go_url'=>$go_url,
            'browse'=>$browse,
            'system'=>$system,
        );
		$org_cache['goods_id_total'][]=$unit;
        $mod = $goods_id%100;
        $cache_key = $this->_redis_cache_goods_id_uv_prefix."{$mod}";
        POCO::setCache($cache_key, $org_cache, array('life_time'=>86400));        
        return true;
    }
    
    /**
     * 批量插入uv log
     * @return boolean
     */
    public function uv_batch_insert_goods_uv_log()
    {
        for($i=0;$i<100;$i++)
        {
            $cache_key = $this->_redis_cache_goods_id_uv_prefix."{$i}";
            $cache_data = poco::getCache($cache_key);
            $month = date('Ym');
            if( ! empty($cache_data) )
            {
                foreach($cache_data['goods_id_total'] as $k => $v)
                {
                    $rs = $this->select_which_tbl($month);
                    if($rs)
                    {
                       $this->insert($v);
                    }
                    
                }
            }
            
            $this->uv_del_cache($i);
            unset($cache_data);
        }
        return true;
    }
    
    /**
     * 检查客户端是否重复访问　返回false不可以重复操作，返回true表示可以操作
     * @param type $goods_id
     * @return boolean
     */
    public function uv_check_is_one_day_repeat($goods_id)
    {
        $goods_id = (int)$goods_id;
        if( ! $goods_id )
        {
            return false;
        }
        $md5_goods_id = md5($goods_id);
        if($_COOKIE[$md5_goods_id])
        {
            return false;
        }
        if( ! $_COOKIE[$md5_goods_id] )
        {
            $last_time = strtotime(date('Y-m-d'))+86400;
            $life_time = $last_time-time();
            setcookie($md5_goods_id, time(),time()+$life_time);
            return true;
        }
        
       
        
    }
    /**
     * 选择哪个月份的表 201509
     * @param type $month
     * @return boolean|string
     */
    public function select_which_tbl($month)
    {
        $this->setDBName('mall_log_db');
        $month = (int)$month;
        if( ! $month )
        {
            return false;
        }
        $tbl_name = "mall_goods_uv_view_log_".$month."_tbl";
        //数据库没表就新建
        $res = $this->query("SHOW TABLES FROM mall_log_db LIKE '{$tbl_name}'");
		if (empty($res) || !is_array($res)) 
		{
			$tab = "{$this->_db_name}.{$tbl_name}";
			$creat_sql = "CREATE TABLE IF NOT EXISTS {$tab} (
                            `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                            `goods_id` int(11) unsigned NOT NULL COMMENT '商品id',
                            `user_id` int(11) unsigned NOT NULL COMMENT '用户id',
                            `ip` varchar(22) NOT NULL COMMENT 'ip地址',
                            `v_time` int(10) unsigned NOT NULL COMMENT '访问时间',
                            `go_url` text NOT NULL COMMENT '执行的url',
                            `source_url` text NOT NULL COMMENT '来源的url',
                            `browse` varchar(255) NOT NULL COMMENT '浏览器',
                            `system` varchar(255) NOT NULL COMMENT '系统',
                            PRIMARY KEY (`id`),
                            KEY `goods_id` (`goods_id`) USING BTREE,
                            KEY ` v_time` (`v_time`) USING BTREE,
                            KEY ` ip` (`ip`) USING BTREE,
                          ) ENGINE=MyISAM DEFAULT CHARSET=gbk;";
			$this->query($creat_sql);
        }
        
        $this->setTableName($tbl_name);
        
        return true;
    }
    
    /*
	 * uv列表
	 * @param bool $b_select_count
	 * @param string $where_str 
	 * @param string $order_by
	 * @param string $limit 
	 * @param string $fields 
	 * return array
	 */
	public function get_uv_list($b_select_count = false, $where_str = '',$month,$order_by = 'id DESC', $limit = '0,10', $fields = '*')
	{
        $month = (int)$month;
        if( ! $month )
        {
            $month = date('Ym');
        }
		$this->select_which_tbl($month);
		if ($b_select_count == true)
		{
			$ret = $this->findCount ( $where_str );
		} else
		{
			$ret = $this->findAll ( $where_str, $limit, $order_by, $fields );
		}
		return $ret;
	}
    
    /**
     * 获取uv某条纪录详情
     * @param type $id
     * @param type $month
     * @return boolean
     */
    public function get_uv_info($id,$month)
    {
        $id = (int)$id;
        $month = (int)$month;
        if( ! $id )
        {
            return false;
        }
        if( ! $month )
        {
            return false;
        }
        $this->select_which_tbl($month);
        
        return $this->find("id='{$id}'");
        
    }
    
    /**
     * 获取goods_id 月uv 总数
     * @param type $month
     * @return type
     */
    public function get_month_uv_total($month,$where = '')
    {
        $month = (int)$month;
        if( ! $month )
        {
            $month = date('Ym');
        }
        $rs = array();
        $this->select_which_tbl($month);
        $sql = "SELECT goods_id,count(*) as total from {$this->_db_name}.mall_goods_uv_view_log_{$month}_tbl $where group by goods_id order by total desc limit 100";
        $rs = $this->query($sql);
        return $rs;
    }
    
    /**
     * 获取ip浏览页面的汇总
     * @param type $month
     * @return type
     */
    public function get_ip_visist_total($month,$where = '')
    {
        $month = (int)$month;
        if( ! $month )
        {
            $month = date('Ym');
        }
        $rs = array();
        $this->select_which_tbl($month);
        $sql = "SELECT ip,user_id,count(*) as total from {$this->_db_name}.mall_goods_uv_view_log_{$month}_tbl $where group by ip order by total desc limit 100";
        $rs = $this->query($sql);
        return $rs;
    }
    
    /**
     * 获取ip访问页面汇总详情
     * @param type $month
     * @param type $ip
     * @return boolean|array
     */
    public function get_ip_visit_pages_total_detail($month,$ip)
    {
        $month = (int)$month;
        $ip = addslashes($ip);
        if( ! $ip )
        {
            return false;
        }
        if( ! $month )
        {
            $month = date('Ym');
        }
        $this->select_which_tbl($month);
        $sql = "select * from {$this->_db_name}.mall_goods_uv_view_log_{$month}_tbl where ip='{$ip}'";
        $rs = $this->query($sql);
        if( ! empty($rs) )
        {
            $lst = array();
            $page_unit = array();
            foreach($rs as $k => $v)
            {
                $page_unit[$v['goods_id']]++;
                $lst['page'] = $page_unit;
            }
        }
        
        return $lst;
        
    }
    
    /**
     * 参数明细 日期，系统，浏览器，小时，
     * @param type $month
     * @param type $goods_id
     * @return boolean
     */
    public function get_month_params_uv_total($month,$goods_id)
    {
        $month = (int)$month;
        $goods_id = (int)$goods_id;
        if( ! $goods_id )
        {
            return false;
        }
        if( ! $month )
        {
            $month = date('Ym');
        }
        $this->select_which_tbl($month);
        $sql = "select * from {$this->_db_name}.mall_goods_uv_view_log_{$month}_tbl where goods_id='{$goods_id}'";
        $rs = $this->query($sql);
        $lst = array();
        $date_unit = array();
        $system_unit = array();
        $browse_unit = array();
        if( ! empty($rs) )
        {
            foreach($rs as $k => $v)
            {
                //1号到30号
                $date_unit[date('Y-m-d',$v['v_time'])]++;
                $lst['date'] = $date_unit;
                
                //系统
                $system_unit[$v['system']]++;
                $lst['system']= $system_unit;
                
                //浏览器
                $browse_unit[$v['browse']]++;
                $lst['browse']= $browse_unit;
                
                //小时
                $hour_unit[date('Y-m-d',$v['v_time'])][date('H',$v['v_time'])]++;
                $lst['hour'] = $hour_unit;
            }
        }
        
        return $lst;
    }
    
    
    
//////////////////////////前台用户调用的方法/////////////////////////////////////////////////////////////  
	/*
	 * 统计访问量
	 * @param int $goods_id
	 * @return bool
	 */
    public function user_update_goods_view($goods_id)
	{
		$goods_id = (int)$goods_id;
		if(!$goods_id)
		{
			return false;
		}
		$this->user_update_goods_uv_log($goods_id);
        //刷新时间段
        $seconds = $this->_seconds;
        
        //浏览次数随机
        $num = rand(0,9);
        $view_numbers = $this->_view_numbers[$num];
        
        //作弊开关
        $button = $this->_button;
        
        //作弊开关没开
        if( ! $button )
        {
            //防刷新
            if( ! empty($_COOKIE[$goods_id.'_time_s']) )
            {
                if( time()-$_COOKIE[$goods_id.'_time_s'] <= $seconds )
                {
                    return false;
                }
            }
        }
        
        setcookie($goods_id.'_time_s', time(),time()+60);
        
        //黑白名单
        if(in_array($goods_id,$this->_white_name))
        {
            $this->set_goods_id_total_views_cache($goods_id,$view_numbers);
        }else if( ! in_array($goods_id,$this->_black_name) )
        {
            $this->set_goods_id_total_views_cache($goods_id,$view_numbers);
        }
        else
        {
            $this->set_goods_id_total_views_cache($goods_id,$view_numbers);
        }
        
        return true;
        
    }
    
    /**
     * 用户更新uv日志
     * @param type $goods_id
     */
    public function user_update_goods_uv_log($goods_id)
    {
        $can_operate = $this->uv_check_is_one_day_repeat($goods_id);
        if($can_operate)
        {
            $this->uv_set_cache($goods_id);
        }else
        {
            return false;
        }
    }
    
    
    
    
}
