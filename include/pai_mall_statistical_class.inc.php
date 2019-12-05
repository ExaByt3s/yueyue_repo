<?php
/**
 * ͳ����
 * 
 * @author ljl
 */

class pai_mall_statistical_class extends POCO_TDG
{
    //����ǰ׺
	public $_redis_cache_name_prefix = "G_YUEUS_MALL_GOODS_ID_TOTAL_VIEWS_";
    
    public $_redis_cache_goods_id_uv_prefix = "G_YUEUS_MALL_GOODS_ID_UV_TOTAL_VIEWS_";
    //ˢ��ʱ��
    public $_seconds = 3;
    //�������
    public $_view_numbers = array(1,2,3,4,5,6,7,8,9,10);
    //���׿���
    public $_button = false;
    //������
    public $_white_name = array();
    //������
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
     * ɾ������
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
     * ��ȡ����
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
     * ����goods_id ���û���
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
     * ����goods_idͬ��������
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
     * ����ͳ�Ʊ������
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
     * ����goods_id_total���ڴ�����,��goods_id���з������ĸ���
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
                    //���µ�ͳ�Ʊ�
                    $this->update_goods_statistica_view_number($k,$v['view_numbers']);                    
                    //ͬ�����µ�������
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
            //��ջ���
            $this->del_goods_id_total_views_cache($i);
        }
        return $j;
    }
    
    /**
     * uv ɾ������
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
     * uv �û���
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
     * uv �趨����
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
        
        //�ͻ�������
        $ip = mall_get_ip();
        //��Դurl
        //HTTP_REFERER
        $source_url = $_SERVER['HTTP_REFERER'];
        //ִ�е�url 
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
     * ��������uv log
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
     * ���ͻ����Ƿ��ظ����ʡ�����false�������ظ�����������true��ʾ���Բ���
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
     * ѡ���ĸ��·ݵı� 201509
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
        //���ݿ�û����½�
        $res = $this->query("SHOW TABLES FROM mall_log_db LIKE '{$tbl_name}'");
		if (empty($res) || !is_array($res)) 
		{
			$tab = "{$this->_db_name}.{$tbl_name}";
			$creat_sql = "CREATE TABLE IF NOT EXISTS {$tab} (
                            `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                            `goods_id` int(11) unsigned NOT NULL COMMENT '��Ʒid',
                            `user_id` int(11) unsigned NOT NULL COMMENT '�û�id',
                            `ip` varchar(22) NOT NULL COMMENT 'ip��ַ',
                            `v_time` int(10) unsigned NOT NULL COMMENT '����ʱ��',
                            `go_url` text NOT NULL COMMENT 'ִ�е�url',
                            `source_url` text NOT NULL COMMENT '��Դ��url',
                            `browse` varchar(255) NOT NULL COMMENT '�����',
                            `system` varchar(255) NOT NULL COMMENT 'ϵͳ',
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
	 * uv�б�
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
     * ��ȡuvĳ����¼����
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
     * ��ȡgoods_id ��uv ����
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
     * ��ȡip���ҳ��Ļ���
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
     * ��ȡip����ҳ���������
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
     * ������ϸ ���ڣ�ϵͳ���������Сʱ��
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
                //1�ŵ�30��
                $date_unit[date('Y-m-d',$v['v_time'])]++;
                $lst['date'] = $date_unit;
                
                //ϵͳ
                $system_unit[$v['system']]++;
                $lst['system']= $system_unit;
                
                //�����
                $browse_unit[$v['browse']]++;
                $lst['browse']= $browse_unit;
                
                //Сʱ
                $hour_unit[date('Y-m-d',$v['v_time'])][date('H',$v['v_time'])]++;
                $lst['hour'] = $hour_unit;
            }
        }
        
        return $lst;
    }
    
    
    
//////////////////////////ǰ̨�û����õķ���/////////////////////////////////////////////////////////////  
	/*
	 * ͳ�Ʒ�����
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
        //ˢ��ʱ���
        $seconds = $this->_seconds;
        
        //����������
        $num = rand(0,9);
        $view_numbers = $this->_view_numbers[$num];
        
        //���׿���
        $button = $this->_button;
        
        //���׿���û��
        if( ! $button )
        {
            //��ˢ��
            if( ! empty($_COOKIE[$goods_id.'_time_s']) )
            {
                if( time()-$_COOKIE[$goods_id.'_time_s'] <= $seconds )
                {
                    return false;
                }
            }
        }
        
        setcookie($goods_id.'_time_s', time(),time()+60);
        
        //�ڰ�����
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
     * �û�����uv��־
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
