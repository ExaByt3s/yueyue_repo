<?php
/**
 * 分类管理
 * 
 * @author ljl
 */

class pai_mall_goods_type_class extends POCO_TDG
{
    
    //缓存前缀
	public $_redis_cache_name_prefix = "G_YUEUS_MALL_GET_FIRST_CACHE_";
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
	private function set_mall_goods_type_tbl()
	{
		$this->setTableName('mall_goods_type_tbl');
	}	

	/**
	 * 
	 */
	private function set_mall_goods_type_attribute_tbl()
	{
		$this->setTableName('mall_goods_type_attribute_tbl');
	}
    
    /**
     * 获取所有子级
     * @param type $id
     * @param type $res
     * @return type
     */
    public function get_all_childs($id,$res = array())
    {
        $this->set_mall_goods_type_tbl();
        $data = $this->findAll("parents_id='$id' and status=1",'','step ASC','id');
        foreach($data as $v)
        {
			$res[] = $v['id'];
            $res = $this->get_all_childs($v['id'],$res); 
		}
		
		return $res;
    }
    
    /**
     * 获取所有父级
     * @param type $id
     * @param type $res
     * @return type
     */
    public function get_all_parents($id,$res = array())
    {
         $this->set_mall_goods_type_tbl();
         $data = $this->findAll("id='$id' and status=1",'','step asc','parents_id');
         foreach($data as $v)
         {
             $res[] = $v['parents_id'];
             $res = $this->get_all_parents($v['parents_id'],$res);
         }
		 
         return $res;
    }
    
    /**
     * 保存
     * @param type $data
     * @return type
     */
    public function do_save($post)
    {
        $this->set_mall_goods_type_tbl();
        
        //数据过滤
        $data['name'] = addslashes($post['name']);
        $data['parents_id'] = (int)$post['parents_id'];
        $data['status'] = 1;
        $data['step'] = (int)($post['step']);
        $data['stock_type'] = is_array($post['stock_type']) ? implode(",", $post['stock_type']) : addslashes($post['stock_type']);
        $data['add_time'] = time();
        $data['user_id'] = (int)$post['user_id'];
        $data['id'] = $post['id'] ? (int)$post['id'] : '';
        $update_data = array();
        if( $data['id'] == '')
        {
            //do insert
            $this->insert($data,"REPLACE");
            
            $this->update_all_parents_childs();
            
            //删除缓存
            $this->del_get_first_cate_get_data();
            
            return true;
            
        }else
        {
            //不能找子级作父级
            $get_child = $this->get_type_cate($data['id']);
            foreach($get_child as $k => $v){
                if($v['id']==$data['parents_id'])
                {
                    return false;
                }
            }
            //do update
            $id = $data['id'];
            $data['add_time'] = time();
            unset($data['id']);
            $this->update($data,"id='$id'");
            
            $this->update_all_parents_childs();
            
            //删除缓存
            $this->del_get_first_cate_get_data();
            
            return true;
        }
    }
    
    /**
     * 更新所有的子级父级
     */
    public function update_all_parents_childs()
    {
        $this->set_mall_goods_type_tbl();
        $all_data = $this->get_type_cate();
        foreach($all_data as $v)
        {
            $update_data = array();
            
            $parents_list = $this->get_all_parents($v['id']);
            $child_list = $this->get_all_childs($v['id']);
            //数组反序
            $parents_list = array_reverse($parents_list);
            $child_list = array_reverse($child_list);
            
            $update_data['child_list'] = implode(",",$child_list);
            $update_data['parents_list'] = implode(",",$parents_list);
            $update_data['add_time'] = time();
            $this->update($update_data,"id='{$v['id']}'");
        }
    }
    
    /**
     * 删除，有子级的不能删除
     * @param type $id
     * @return boolean
     */
    public function del_one($id)
    {
        $this->set_mall_goods_type_tbl();
        $one = $this->check_have_parent($id);
        if( $one )
        {
            return false;
        }
        $this->update(array('status'=>2,'add_time'=>time()),"id='$id'");
        //更新所有的子级父级
        $this->update_all_parents_childs();
        
        //删除缓存
        $this->del_get_first_cate_get_data();
        
        return true;
    }
    
    /**
     *检查是否有父级
     * @param type $id
     * @return type
     */
    public function check_have_parent($id)
    {
        $this->set_mall_goods_type_tbl();
        return $this->find("parents_id='$id' and status='1'");
    }
    
    /**
     * 获取类型属性
     * @param type $id
     */
    public function get_type_info($id)
    {
        $this->set_mall_goods_type_tbl();
		if(is_array($id))
		{
			$id = implode(',',$id);
			$sql = "id in ($id)";
			return $this->findAll($sql);
		}
		$id = (int)$id;
        return $this->find("id='$id'");
    }
    
    /**
     * 无限级分类按id下标整理
     * @author ljl
     * @param type $pid 父id
     * @param type $level 层级
     * @return $res 数组
     */
    public function get_type_cate_order_id($pid = 0,$bool=false, $level = 0 ,$res = array())
    {
		$pid = (int)$pid;
		$re = $this->get_type_cate($pid,$bool,$level,$res);
		foreach($re as $val)
		{
			$return[$val['id']] = $val;
		}
		return $return;
    }
    
    /**
     * 无限级分类
     * @author ljl
     * @param type $pid 父id
     * @param type $level 层级
     * @return $res 数组
     */
    public function get_type_cate($pid = 0,$bool=false,$level = 0 ,$res = array())
    {
		$pid = (int)$pid;
        $this->set_mall_goods_type_tbl();
        if($bool)
        {
            if($bool=='all')
            {
                $sql = "parents_id='$pid'";
            }else
            {
                $sql = "parents_id='$pid' and ( status='1' or status='3' )";
            }
            
        }else
        {
             $sql = "parents_id='$pid' and status='1' ";
        }
		
		if(defined('TASK_DEBUG') &&  ! $bool )
		{
			$sql = "parents_id='$pid' and status!=2";
		}
        
        $data = $this->findAll($sql,'','step ASC');
        foreach($data as $v)
        {
            $v['level'] = $level;
            $res[] = $v;
            $res = $this->get_type_cate( $v['id'],$bool,$level+1,$res); 
		}
		return $res;
    }
    
    /**
     * 删除属性搜索显示,取数据
     * @param type $type_id
     * @return boolean
     */
    public function del_get_first_cate_get_data()
    {
		$cache_key = $this->_redis_cache_name_prefix;
        POCO::deleteCache($cache_key);
	}
	
	
	 /**
     * 拿第一级分类
     * @author ljl
     * @return $res 数组
     */
	public function get_first_cate()
	{
        $cache_key = $this->_redis_cache_name_prefix;
		$return = POCO::getCache($cache_key);
		if($return)
		{
			return $return;
		}
        
		$this->set_mall_goods_type_tbl();
		$data = $this->findAll("parents_id='2' and (status='1' or status='3')",'','step asc');
        foreach($data as $k => $v)
        {
            $v['name'] = iconv('gbk', 'utf-8', $v['name']);
            $data_new[$v['id']] = $v;
			$second_arys = $this->get_second_cate($v['id']);
			foreach($second_arys as $ks => $vs)
			{
				$data_new[$v['id']]['property'][$vs['id']] = $vs;
			}
            
			if($data_new[$v['id']]['property'])
			{
				foreach($data_new[$v['id']]['property'] as $pk => &$pv)
				{
					$pv['name'] = iconv('gbk', 'utf-8', $pv['name']);
					$third_arys  = $this->get_third_cate($pv['id']);
					foreach($third_arys as $kt => $kv)
					{
						$pv['property_content'][$kv['id']] = $kv;
					}
					foreach($pv['property_content'] as $ck => &$cv)
					{
						
						$cv['name'] = iconv('gbk', 'utf-8', $cv['name']);
						
						$cv['property_content_third'] = $this->get_third_cate($cv['id']);
						
						foreach($cv['property_content_third'] as $kct => &$vct)
						{
							$vct['name'] = iconv('gbk','utf-8',$vct['name']);
						}
					}
				}
			}
            
        }
        
        POCO::setCache($cache_key,$data_new,array('life_time'=>86400));
		
		return $data_new;
	}
    
    /**
     * 拿第二级分类
     * @param type $goods_type_id
     * @return type
     */
    public function get_second_cate($goods_type_id)
    {
        $goods_type_id = (int)$goods_type_id;
        if( ! $goods_type_id )
        {
            return array();
        }
        $this->set_mall_goods_type_attribute_tbl();
        $data = $this->findAll("goods_type_id='$goods_type_id' and parents_id='0' and status='1' and is_search='1'",'','step asc');
        return $data;
    }
    
    /**
     * 拿第三级分类
     * @param type $parents_id
     * @return type
     */
    public function get_third_cate($parents_id)
    {
        $parents_id = (int)$parents_id;
        if( ! $parents_id )
        {
            return array();
        }
        $this->set_mall_goods_type_attribute_tbl();
        $data = $this->findAll("parents_id='$parents_id' and status='1' ",'','step asc');
        return $data;
    }
    
	
    
    
	
	

	
}
