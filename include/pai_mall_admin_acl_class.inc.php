<?php
/**
 * 权限无限级菜单管理
 * 
 * @author ljl
 */

class pai_mall_admin_acl_class extends POCO_TDG
{
    public $_redis_cache_name_prefix = "G_YUEUS_MALL_GOODS_ACL";
    
	public function __construct()
	{
		$this->setServerId('101');
		$this->setDBName('mall_db');
	}
	
	private function set_mall_admin_acl_tbl()
    {
        $this->setTableName('mall_admin_acl_tbl');
    }
    
    /**
     * 更新所有的子级父级
     */
    public function update_all_parents_childs()
    {
        $this->set_mall_admin_acl_tbl();
        $all_data = $this->get_acl_cate();
        foreach($all_data as $v)
        {
            $update_data = array();
            
            $parents_list = $this->get_all_parents($v['id']);
            $child_list = $this->get_all_childs($v['id']);
            //数组反序
            $parents_list = array_reverse($parents_list);
            $child_list = array_reverse($child_list);
            
            $update_data['children_list'] = implode(",",$child_list);
            $update_data['parent_list'] = implode(",",$parents_list);
            $this->update($update_data,"id='{$v['id']}'");
        }
        
        //删除缓存
        $this->del_md5_child_cache();
        
    }
    
    /**
     * 权限操作无限级分类
     * @param type $pid
     * @param type $level
     * @param type $res
     * @return type
     */
    public function get_acl_cate($pid = 0,$level = 0 ,$res = array())
    {
        $pid = (int)$pid;
        $this->set_mall_admin_acl_tbl();
        $sql = "parent_id='$pid' and status='1' ";
		$data = $this->findAll($sql,'','id asc');
        foreach($data as $v)
        {
            $v['level'] = $level;
            $res[] = $v;
            $res = $this->get_acl_cate( $v['id'],$level+1,$res); 
		}
		return $res;
    }
    
    /**
     * 获取所有子级
     * @param type $id
     * @param type $res
     * @return type
     */
    public function get_all_childs($id,$res = array())
    {
        $this->set_mall_admin_acl_tbl();
        $data = $this->findAll("parent_id='$id' and status='1' ",'','id ASC','id');
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
         $this->set_mall_admin_acl_tbl();
         $data = $this->findAll("id='$id' and status='1'",'','id asc','parent_id');
         foreach($data as $v)
         {
             $res[] = $v['parent_id'];
             $res = $this->get_all_parents($v['parent_id'],$res);
         }
		 
         return $res;
    }
    
    /**
     * 添加更新
     * @param type $post
     * @return boolean
     */
    public function do_save($post)
    {
        $this->set_mall_admin_acl_tbl();
        $data = array();
        $data['name'] = addslashes(trim($post['name']));
        $data['val'] = addslashes(trim($post['val']));
        $data['parent_id'] = (int)$post['parent_id'];
        $data['id'] = (int)$post['id'];
        $data['status'] = 1;
        
        //insert
        if($data['id'] == '')
        {
            $id = $this->insert($data,"IGNORE");
            $this->update_all_parents_childs();
            
        }else  //update
        {
            $rs = $this->update($data, "id='{$data['id']}'");
            $this->update_all_parents_childs();
            
        }
        
        return true;
    }
    
    /**
     * 获取权限详情
     * @param type $id
     * @return boolean
     */
    public function get_admin_acl_info($id)
    {
        $id = (int)$id;
        if( ! $id )
        {
            return false;
        }
        $this->set_mall_admin_acl_tbl();
        return $this->find("id='{$id}'");
    }
    
    /**
     * 删除，有子级的不能删除
     * @param type $id
     * @return boolean
     */
    public function del_one($id)
    {
        $this->set_mall_admin_acl_tbl();
        $one = $this->check_have_parent($id);
        if( $one )
        {
            return false;
        }
        $this->update(array('status'=>2),"id='$id'");
        //更新所有的子级父级
        $this->update_all_parents_childs();
        
        return true;
    }
    
    /**
     *检查是否有父级
     * @param type $id
     * @return type
     */
    public function check_have_parent($id)
    {
        $this->set_mall_admin_acl_tbl();
        return $this->find("parent_id='$id' and status='1'");
    }
    
    /**
     * 获取val 的md5并拿子级
     * @return type
     */
    public function get_md5_row_and_child_list()
    {
       $md5_child_cache = $this->get_md5_child_cache();
       if( ! empty($md5_child_cache) )
       {
           return $md5_child_cache;
       }
       $this->set_mall_admin_acl_tbl();
       $md5_rs = $rs = $parent_list = $md5_child_rs = array();
       $list = $this->findAll("parent_id='0'");
       if( ! empty($list) )
       {
           foreach($list as $k => $v)
           {
               $parent_list[] = $v['id'];
           } 
       }
       if( ! empty($parent_list) )
       {
           $where = implode(",",$parent_list);
           $rs = $this->findAll("parent_id in ($where)");
       } 
       
       if( ! empty($rs) )
       {
           foreach($rs as $k => $v)
           {
               $md5_rs[md5($v['val'])] = $v;
           } 
       }
       if($md5_rs)
       {
            $md5_child_rs = $this->get_child_cate($md5_rs);
            $this->set_md5_child_cache($md5_child_rs,86400);
       }
       
       
       return $md5_child_rs;
       
    }
    
    public function set_md5_child_cache($data,$life_time)
    {
       POCO::setCache($this->_redis_cache_name_prefix, $data, array('life_time'=>$life_time));
    }
    
    public function get_md5_child_cache()
    {
         $return = POCO::getCache($this->_redis_cache_name_prefix);
    }
    
    public function del_md5_child_cache()
    {
        POCO::deleteCache($this->_redis_cache_name_prefix);
    }
    
    public function get_child_cate($ary)
    {
        foreach($ary as $k => $v)
        {
            if( ! empty($v['children_list']) )
            {
                $child_ary = explode(",",$v['children_list']);
                foreach($child_ary as $ck => $cv)
                {
                    $this->set_mall_admin_acl_tbl();
                    $child_one = array();
                    $child_one = $this->find("id='{$cv}'");
                    if( ! $ary[$k]['child_data'][md5($child_one['val'])] )
                    {
                        $ary[$k]['child_data'][md5($child_one['val'])] = $child_one;
                    }
                    
                    if($child_one['children_list'])
                    {
                        $ary[$k]['child_data'] = $this->get_child_cate( $ary[$k]['child_data'] );
                    }
                }
                
            }
        }
        return $ary;
    }
            
    
    /**
     * 数据插入从配置文件
     * @return boolean
     */
    public function insert_admin_acl()
    {
        exit;
        $admin_power_config = pai_mall_load_config('admin_power');
        ljl_dump($admin_power_config);
        
        $this->set_mall_admin_acl_tbl();
        foreach($admin_power_config as $k => $v)
        {
            
                //类型单元
                if(in_array($k,array('goods','seller','order','user')))
                {
                    $unit = array();
                    $unit = array(
                      'name'=>$k,
                      'parent_id'=>0,
                      'val'=>$k,  
                    );
                    $id = '';
                    $id = $this->insert($unit,"IGNORE");
                    
                }
                
                if( $id )
                {
                    foreach($v as $vk => $vv)
                    {
                        //file单元
                        if( ! empty($vv['file']) )
                        {
                            $unit = array();
                            $unit = array(
                                'name'=>$vv['name'],
                                'parent_id'=>$id,
                                'val'=>$vv['file'],
                            );
                            $id = '';
                            $id = $this->insert($unit,"IGNORE");
                        }
                        
                        if( $id )
                        {
                            if( ! empty($vv['action']) )
                            {
                                foreach($vv['action'] as $vak => $vav)
                                {
                                    //action单元
                                    $unit = array();
                                    $unit = array(
                                        'name'=>$vav['name'],
                                        'parent_id'=>$id,
                                        'val'=>$vav['action_type'],
                                    );
                                    $this->insert($unit,"IGNORE");
                                }
                            }
                            
                            
                        }
                    }
                }
            
                
        }
        return true;
        
        
    }
    
	
    
    
	
	

	
}
