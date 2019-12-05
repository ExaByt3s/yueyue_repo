<?php
/**
 * @desc:   榜单首页和内容页配置v2版
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/7/20
 * @Time:   11:54
 * version: 2.0
 */

class pai_rank_event_v2_class extends POCO_TDG
{
	
	
	/**
	 * 构造函数
	 *
	 */
	public function __construct()
	{
		$this->setServerId ( 101 );
		$this->setDBName ( 'pai_db' );
		$this->setTableName ( 'pai_rank_event_v2_tbl' );
        $this->versions_list = include("/disk/data/htdocs232/poco/pai/yue_admin_v2/rank/versions_config.inc.php");//版本配置文件
	}

    /**
     * 添加
     * @param array $insert_data
     * @return int  true|false
     * @throws App_Exception
     */
    public function add_info($insert_data)
	{
		
		if (empty ( $insert_data ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':数组不能为空' );
		}
		
		return $this->insert ( $insert_data );
	
	}

    /**
     * 添加榜单数据和log
     * @param array $insert_data 榜单数据
     * @param string $action     榜单操作
     * @return array
     * @throws App_Exception     报出异常
     */
    public function add_info_and_log($insert_data,$action='insert')
    {
        $rank_event_log_v2_obj = POCO::singleton( 'pai_rank_event_log_v2_class' ) ;
        global $yue_login_id;
        $ret = array('status'=>0,'msg'=>'','success_log'=>'');
        $action = trim($action);
        if(!is_array($insert_data) || empty($insert_data))
        {
            throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':数组不能为空' );
        }
        $data_log = $this->get_serialize_rank_event_list();
        //添加榜单
        $status = $this->add_info($insert_data);
        $status = intval($status);
        if($status <1)
        {
            $ret['status'] = -1;
            $ret['msg'] =  '榜单添加失败';
            return $ret;
        }
        //添加log参数整理
        $data['act']      = $action;
        $data['data_log'] = $data_log;
        $data['rank_event_id'] = $status;
        $data['audit_id'] = $yue_login_id;
        $data['audit_time'] = time();
        $log_status = $rank_event_log_v2_obj->add_info($data);
        $log_status = intval($log_status);
        if($log_status <1)
        {
            $ret['status'] = -2;
            $ret['msg'] =  '添加榜单log失败';
            return $ret;
        }
        $ret['status'] = 1;
        $ret['msg'] =  '添加榜单和添加榜单log成功';
        $ret['success_log'] = $log_status;
        return $ret;
    }


    /**
     * 更新数据
     * @param array $data
     * @param int  $id
     * @return boo|int
     * @throws App_Exception
     */
    public function update_info($data, $id)
	{
		if (!is_array($data) || empty($data))
		{
			throw new App_Exception(__CLASS__.'::'.__FUNCTION__.':数组不能为空');
		}
		$id = (int)$id;
		if ($id <1)
		{
			throw new App_Exception(__CLASS__.'::'.__FUNCTION__.':ID不能为空');
		}
		
		$where_str = "id = {$id}";
		return $this->update($data, $where_str);
	}

    /**
     * 更新榜单数据和添加log
     * @param array    $update_data   榜单数据
     * @param int     $id
     * @param  string $action
     * @return array  返回值
     * @throws App_Exception 报出异常
     */
    public function update_info_and_log($update_data,$id,$action='update')
    {
        $rank_event_log_v2_obj = POCO::singleton( 'pai_rank_event_log_v2_class' ) ;
        global $yue_login_id;

        $ret = array('status'=>0,'msg'=>'','success_log'=>'');
        $action = trim($action);
        if (!is_array($update_data) || empty($update_data))
        {
            throw new App_Exception(__CLASS__.'::'.__FUNCTION__.':数组不能为空');
        }
        $id = intval($id);
        if ($id <1)
        {
            throw new App_Exception(__CLASS__.'::'.__FUNCTION__.':ID不能为空');
        }
        $data_log = $this->get_serialize_rank_event_list();
        $status = $this->update_info($update_data,$id);
        $status = intval($status);
        if($status <1)
        {
            $ret['status'] = -1;
            $ret['msg'] =  '修改榜单失败';
            return $ret;
        }
        //添加log参数整理
        $data['act']      = $action;
        $data['data_log'] = $data_log;
        $data['rank_event_id'] = $id;
        $data['audit_id'] = $yue_login_id;
        $data['audit_time'] = time();
        $log_status = $rank_event_log_v2_obj->add_info($data);
        $log_status = intval($log_status);
        if($log_status <1)
        {
            $ret['status'] = -2;
            $ret['msg'] =  '添加榜单log失败';
            return $ret;
        }
        $ret['status'] = 1;
        $ret['msg'] =  '修改榜单和添加榜单log成功';
        $ret['success_log'] = $log_status;
        return $ret;
    }

    /**
     * 获取一条数据
     * @param $id
     * @return array
     * @throws App_Exception
     */
    public function get_rank_event_info($id)
    {
        $id = (int) $id;
        if ($id <1)
        {
            throw new App_Exception(__CLASS__.'::'.__FUNCTION__.':ID不能为空');
        }
        $ret = $this->find ( "id={$id}" );
        return $ret;
    }


    /**
     * 删除
     * @param  int $id
     * @return bool|int
     * @throws App_Exception
     */
    public function delete_info($id)
	{
        $id = (int)$id;
        if ($id <1)
        {
            throw new App_Exception(__CLASS__.'::'.__FUNCTION__.':ID不能为空');
        }
        $where_str = "id = {$id}";
        return $this->delete($where_str);
	}

    /**
     * 删除榜单数据并且加log
     * @param int $id        榜单ID
     * @param string $action 榜单操作
     * @return array         返回值
     * @throws App_Exception 输出异常
     */
    public function delete_info_and_log($id,$action='del')
    {
        $rank_event_log_v2_obj = POCO::singleton( 'pai_rank_event_log_v2_class' ) ;
        global $yue_login_id;
        $ret = array('status'=>0,'msg'=>'','success_log'=>'');
        $action = trim($action);
        $id = intval($id);
        if($id <1)
        {
            throw new App_Exception(__CLASS__.'::'.__FUNCTION__.':ID不能为空');
        }
        $data_log = $this->get_serialize_rank_event_list();
        $status = $this->delete_info($id);
        $status = intval($status);
        if($status <1)
        {
            $ret['status'] = -1;
            $ret['msg'] =  '删除榜单失败';
            return $ret;
        }
        //添加log参数整理
        $data['act']      = $action;
        $data['data_log'] = $data_log;
        $data['rank_event_id'] = $id;
        $data['audit_id'] = $yue_login_id;
        $data['audit_time'] = time();
        $log_status = $rank_event_log_v2_obj->add_info($data);
        $log_status = intval($log_status);
        if($log_status <1)
        {
            $ret['status'] = -2;
            $ret['msg'] =  '添加榜单log失败';
            return $ret;
        }
        $ret['status'] = 1;
        $ret['msg'] =  '删除榜单和添加榜单log成功';
        $ret['success_log'] = $log_status;
        return $ret;
    }

    /**
     * 删除榜单所有数据
     * @return bool|int
     */
    public function dell_rank_event()
    {
        $where_str = '1';
        return $this->delete($where_str);
    }

    /**
     * 恢复榜单数据
     * @param int    $id      log表的自定增ID
     * @param string $action  榜单操作类型
     * @return array
     */
    public function  restore_rank_event_by_id($id,$action='restore')
    {
        $rank_event_log_v2_obj = POCO::singleton( 'pai_rank_event_log_v2_class' ) ;
        global $yue_login_id;
        $ret = array('status'=>0,'msg'=>'','success_log'=>'');
        $action = trim($action);
        $id = intval($id);
        $data_log = $this->get_serialize_rank_event_list();//获取所有event_rank数据
        $this->dell_rank_event();//删除榜单数据
        /*$status = intval($status);
        if($status <1)
        {
            $ret['status'] = -1;
            $ret['msg'] =  '删除所有榜单失败';
            return $ret;
        }*/
        $list = $rank_event_log_v2_obj->get_unserialize_rank_event_info($id);
        if(!is_array($list)) $list = array();
        //恢复榜单数据
        foreach($list as $val)
        {
           $this->add_info($val);
        }
        //添加log参数整理
        $data['act']      = $action;
        $data['data_log'] = $data_log;
        $data['rank_event_id'] = $id;
        $data['audit_id'] = $yue_login_id;
        $data['audit_time'] = time();
        $log_status = $rank_event_log_v2_obj->add_info($data);
        $log_status = intval($log_status);
        if($log_status <1)
        {
            $ret['status'] = -2;
            $ret['msg'] =  '添加榜单log失败';
            return $ret;
        }
        $ret['status'] = 1;
        $ret['msg'] =  '恢复榜单和添加榜单log成功';
        $ret['success_log'] = $log_status;
        return $ret;

    }
	
	/*
	 * 获取
	 * @param bool $b_select_count
	 * @param int $versions_id    版本ID
	 * @param string $place       位置index(首页)|list(列表)
	 * @param int    $location_id 地区
	 * @param string $where_str 查询条件
	 * @param string $order_by 排序
	 * @param string $limit 
	 * @param string $fields 查询字段
	 * 
	 * return array
	 */
	public function get_rank_event_list($b_select_count = false,$versions_id,$place,$location_id,$where_str = '', $order_by = 'id DESC', $limit = '0,20', $fields = '*')
	{
        $versions_id = intval($versions_id);
		$place = trim($place);
        $location_id = intval($location_id);
        if($versions_id >0)
        {
            if(strlen($where_str)>0) $where_str .= ' AND ';
            $where_str .= "versions_id={$versions_id}";
        }
        if(strlen($place)>0)
        {
            if(strlen($where_str)>0) $where_str .= ' AND ';
            $where_str .= "place=:x_place";
            sqlSetParam($where_str,"x_place",$place);
        }
        if($location_id >0)
        {
            if(strlen($where_str)>0) $where_str .= ' AND ';
            $where_str .= "location_id={$location_id}";
        }
		if ($b_select_count == true)
		{
			$ret = $this->findCount ( $where_str );
		} else
		{
			$ret = $this->findAll ( $where_str, $limit, $order_by, $fields );
		}
		return $ret;
	}

	/*
	 * 获取
	 * @param string $where_str 查询条件
	 * @param string $order_by 排序
	 * @param string $limit 
	 * @param string $fields 查询字段
	 * 
	 * return array
	 */
	public function get_serialize_rank_event_list($where_str='')
	{
		$total_count  = $this->findCount ( $where_str );
		$where_str = "1";
		$ret = $this->findAll($where_str, "0,{$total_count}", "sort_order DESC,id DESC");
		return serialize($ret);
	}

    /**
     * 获取版本名称
     * @param $versions_id
     * @return mixed
     */
    public function versions_option($versions_id)
    {

        $versions_id = intval($versions_id);
        foreach($this->versions_list as $val)
        {
            if($val['versions_id'] == $versions_id) return $val['name'];
        }
    }

    /**
     * 通过版本名获取版本ID
     * @param $version_name   版本名
     * @return int      版本ID
     */
    public function  get_version_id($version_name)
    {
        $version_name = trim($version_name);
        if(strlen($version_name) <1) return 0;
        foreach($this->versions_list as $val)
        {
            if($val['name'] == $version_name) return $val['versions_id'];
        }
        return 0;
    }

    /**
     * 获取列表数据 APP接口,勿动
     * @param $place  位置  index(首页),list(列表页)
     * @param int $type_id      分类ID  (首页可以为空,默认选择为-1),例如模特邀约为31
     * @param int $location_id  地区ID  (默认选择为101029001广州)例如广州的location_id '101029001'
     * @param $version_name     版本名(可以为空) 例如 '3.0.0'
     * @param string $limit     循环条数,示例:"0,15"
     * @param int $status       状态-1全部，0下架，1为上架
     * @return array|int        返回值
     */
    public function get_rank_event_by_location_id($place,$type_id = -1,$location_id = 101029001,$version_name,$limit="0,15",$status=1)
    {
        $arr = array('index','list');
        $place = trim($place);
        $type_id = intval($type_id);
        $location_id = intval($location_id);
        $version_name = trim($version_name);
        $status = intval($status);
        $versions_id = 0;
        $where_str = '';
        if(strlen($place) <1 || !in_array($place,$arr))
        {
            return false;
        }
        if($place == 'list')
        {
           if($type_id >=0)
           {
               if(strlen($where_str) >0) $where_str .= ' AND ';
               $where_str .= "cat_id ={$type_id}";
           }
        }
        if($location_id <1)
        {
            $location_id = 101029001;
        }
        if(strlen($version_name) >0)
        {
            $versions_id = $this->get_version_id($version_name);
        }
        else
        {
            if(strlen($where_str) >0) $where_str .= ' AND ';
            $where_str .= "versions_id !=2";
        }
        if($status >=0)
        {
            if(strlen($where_str) >0) $where_str .= ' AND ';
            $where_str .= "status={$status}";
        }
        $list = $this->get_rank_event_list(false,$versions_id,$place,$location_id,$where_str,'sort_order DESC,id DESC',$limit,'cat_id as type_id,type,rank_id,url,cover_url,headtile,subtitle,pid,app_sort,cms_type');
        if(!is_array($list) || empty($list)) return false;
        foreach($list as &$val)
        {
            //if($val['pid'] == 0) $val['pid'] = 1220101;
            $val['cover_url'] = yueyue_resize_act_img_url($val['cover_url']);
            //$val['headtile'] = urlencode(iconv('gbk', 'utf8', $val['headtile']));
            $curl = '';
            if($val['type'] ==1)//榜单方式
            {
                 $cms_str = urlencode('yueyue_static_cms_id=' . $val['rank_id'].'&cms_type='.$val['cms_type']);
                 $curl = "yueyue://goto?type=inner_app&pid={$val['pid']}&return_query={$cms_str}&title=".urlencode(iconv('gbk', 'utf8', $val['headtile']));
            }
            elseif($val['type'] == 0)
            {
                $url_arr = parse_url($val['url']);
                $httts = trim($url_arr['scheme']);
                if($httts == 'http' || $httts == 'https')//是否为http||https
                {
                   $wifi_url = str_replace('yp.yueus.com','yp-wifi.yueus.com',$val['url']);
                   $curl = "yueyue://goto?type=inner_web&url=".urlencode(iconv('gbk', 'utf8',$val['url']))."&wifi_url=".urlencode(iconv('gbk', 'utf8',$wifi_url))."&title=".urlencode(iconv('gbk', 'utf8', $val['headtile']))."&showtitle=2";
                }
                elseif($httts == 'yueyue' || $httts == 'yueseller')
                {
                    $curl = $val['url'];
                }
                else
                {
                    $curl = "yueyue://goto?type=inner_app&pid={$val['pid']}&return_query=".urlencode(iconv('gbk', 'utf8',$val['url']))."&title=" . urlencode(iconv('gbk', 'utf8', $val['headtile']));
                }

            }
            $val['curl'] = $curl;
        }
        return $list;
    }

    /**
     * 获取榜单连接
     * @param int $type 类型
     * @param $headtile 大标题
     * @param int $pid      PID
     * @param string $url 自动模式连接
     * @param int $rank_id 榜单模式,榜单连接
     * @param string $cms_type 榜单模式,有商品和商家模式(good,mall)
     * @return string
     */
    public function get_url_by_type($type =0,$headtile,$pid,$url='',$rank_id =0,$cms_type = '')
   {
       $type = intval($type);
       $headtile = trim($headtile);
       $pid = intval($pid);
       $url = trim($url);
       $rank_id = intval($rank_id);
       $cms_type = trim($cms_type);

       $curl = '';
       if($type ==1)//榜单方式
       {
           $cms_str = urlencode('yueyue_static_cms_id=' .$rank_id.'&cms_type='.$cms_type);
           $curl = "yueyue://goto?type=inner_app&pid={$pid}&return_query={$cms_str}&title=".urlencode(iconv('gbk', 'utf8', $headtile));
       }
       elseif($type == 0)
       {
           $url_arr = parse_url($url);
           $httts = trim($url_arr['scheme']);
           if($httts == 'http' || $httts == 'https')//是否为http||https
           {
               $wifi_url = str_replace('yp.yueus.com','yp-wifi.yueus.com',$url);
               $curl = "yueyue://goto?type=inner_web&url=".urlencode(iconv('gbk', 'utf8',$url))."&wifi_url=".urlencode(iconv('gbk', 'utf8',$wifi_url))."&title=".urlencode(iconv('gbk', 'utf8', $headtile))."&showtitle=2";
           }
           elseif($httts == 'yueyue' || $httts == 'yueseller')
           {
               $curl = $url;
           }
           else
           {
               $curl = "yueyue://goto?type=inner_app&pid={$pid}&return_query=".urlencode(iconv('gbk', 'utf8',$url))."&title=" . urlencode(iconv('gbk', 'utf8', $headtile));
           }

       }
       return $curl;
   }
	
}

?>