<?php
/**
 * Ȩ���û�����
 * 
 * @author ljl
 */

class pai_mall_admin_user_class extends POCO_TDG
{
    public $_redis_cache_name_prefix = "G_YUEUS_MALL_GOODS_ACL_USER_";
    
	public function __construct()
	{
		$this->setServerId('101');
		$this->setDBName('mall_db');
	}
	
	private function set_mall_admin_acl_tbl()
    {
        $this->setTableName('mall_admin_acl_tbl');
    }
    
    private function set_mall_admin_tbl()
    {
        $this->setTableName('mall_admin_tbl');
    }
    
    
    /*
	 * �̼��б�
	 * @param bool $b_select_count
	 * @param string $where_str 
	 * @param string $order_by
	 * @param string $limit 
	 * @param string $fields 
	 * return array
	 */
	public function get_admin_list($b_select_count = false, $where_str = '', $order_by = 'id DESC', $limit = '0,10', $fields = '*')
	{
		$this->set_mall_admin_tbl();
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
     * ɾ���û�
     * @param type $id
     * @return boolean
     */
    public function del_one($id)
    {
        $id = (int)$id;
        if( ! $id )
        {
            return false;
        }
        $this->set_mall_admin_tbl();
        $rs = $this->update(array('status'=>0),"id='{$id}'");
        
        //ɾ������
        $user_one = $this->get_one($id);
        $this->del_acl_user_cache($user_one['user_id']);
        
        return $rs;
    }
    
    /**
     * �����༭
     * @param type $post
     * @return boolean
     */
    public function do_save($post)
    {
        if(empty($post))
        {
            return false;
        }
        $this->set_mall_admin_tbl();
        $data['id'] = (int)$post['id'];
        $data['user_id'] = (int)$post['user_id'];
        $data['status'] = (int)$post['status'];
        $data['acl'] = addslashes(trim($post['acl']));
        
        if($data['id'] == '')
        {
            unset($data['id']);
            $this->insert($data,"INGNORE");
        }else
        {
            $this->update($data,"id='{$data['id']}'");
        }
        
        //ɾ������
        $this->del_acl_user_cache($post['user_id']);
        
        return true;
        
    }
    
    /**
     * ��ȡ����
     * @param type $id
     * @return boolean
     */
    public function get_one($id)
    {
        $id = (int)$id;
        if( ! $id )
        {
            return false;
        }
        $this->set_mall_admin_tbl();
        return $this->find("id='{$id}'");
    }
    
    /**
     * �趨����
     * @param type $user_id
     * @return boolean
     */
    public function set_acl_user_cache($user_id)
    {
        $user_id = (int)$user_id;
        if( ! $user_id )
        {
            return false;
        }
        $this->set_mall_admin_tbl();
        $user_one = $this->find("user_id='$user_id' and status='1'");
        if( ! empty($user_one) )
        {
           POCO::setCache($this->_redis_cache_name_prefix."{$user_id}", $user_one, array('life_time'=>30*86400));
        }else
        {
            return false;
        }
            
    }
    
    /**
     * ������
     * @param type $user_id
     * @return boolean
     */
    public function get_acl_user_cache($user_id)
    {
        $user_id = (int)$user_id;
        if( ! $user_id )
        {
            return false;
        }
        $cache_one = array();
        $cache_one = POCO::getCache($this->_redis_cache_name_prefix."{$user_id}");
        if($cache_one)
        {
           return $cache_one;
        }else
        {
            $this->set_acl_user_cache($user_id);
            return POCO::getCache($this->_redis_cache_name_prefix."{$user_id}");
        }
        
        
    }
    
    /**
     * ɾ������
     * @param type $user_id
     * @return boolean
     */
    public function del_acl_user_cache($user_id)
    {
        $user_id = (int)$user_id;
        if( ! $user_id )
        {
            return false;
        }
        
        POCO::deleteCache($this->_redis_cache_name_prefix."{$user_id}");
    }
    
    
    
	
    
    
	
	

	
}
