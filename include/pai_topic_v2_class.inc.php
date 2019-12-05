<?php
/*
 * 专题操作类
 */

class pai_topic_v2_class extends POCO_TDG
{
	
	
	/**
	 * 构造函数
	 *
	 */
	public function __construct()
	{
		$this->setServerId ( 101 );
		$this->setDBName ( 'pai_db' );
		$this->setTableName ( 'pai_topic_tbl' );
	}
	
	/*
	 * 添加
	 * 
	 * @param array  $insert_data 
	 * 
	 * return bool 
	 */
	public function add_topic($insert_data)
	{
		
		if (empty ( $insert_data ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':数组不能为空' );
		}
		
		return $this->insert ( $insert_data );
	
	}
    
    public function del_topic($id)
    {
        $id = (int)$id;
        if($id)
        {
            $sql_str = "DELETE FROM pai_db.pai_topic_tbl WHERE id=$id";
            db_simple_getdata($sql_str, TURE, 101);
            return true;    
        }else{
            return false;
        }
        
    }
    
    public function update_effect($id, $state)
    {
        $id = (int)$id;
        if($id)
        {
            if(!$state) $state=0;
            $sql_str = "UPDATE pai_db.pai_topic_tbl SET is_effect = $state 
                        WHERE id=$id";
            db_simple_getdata($sql_str, TURE, 101);
            return true;
        }else{
            return false;
        }
    }
	
	/**
	 * 更新
	 *
	 * @param array $data
	 * @param int $id
	 * @return bool
	 */
	public function update_topic($data, $id)
	{
		if (empty($data)) 
		{
			throw new App_Exception(__CLASS__.'::'.__FUNCTION__.':数组不能为空');
		}
		$id = (int)$id;
		if (empty($id)) 
		{
			throw new App_Exception(__CLASS__.'::'.__FUNCTION__.':ID不能为空');
		}
		
		$where_str = "id = {$id}";
		return $this->update($data, $where_str);
	}
	
	/*
	 * 获取
	 * @param bool $b_select_count
	 * @param string $where_str 查询条件
	 * @param string $order_by 排序
	 * @param string $limit 
	 * @param string $fields 查询字段
	 * 
	 * return array
	 */
	public function get_topic_list($b_select_count = false, $where_str = '', $order_by = 'id DESC', $limit = '0,10', $fields = '*')
	{
		
		if ($b_select_count == true)
		{
			$ret = $this->findCount ( $where_str );
		} else
		{
			$ret = $this->findAll ( $where_str, $limit, $order_by, $fields );
		}
		return $ret;
	}
	
	public function get_topic_info($id)
	{
		//global $my_app_pai;
		$id = ( int ) $id;
		$ret = $this->find ( "id={$id}" );
		$ret['share_text'] = $this->get_share_text($ret);
		if ($id == 89) 
		{
			$ret['rank_content'] = $this->get_topic_info_v2($ret);
		}
		if($ret['is_button']==1)
		{
			include_once ('/disk/data/htdocs232/poco/pai/yue_admin/topic/config/topic_button.fuc.php');

 			$enroll_button = yue_topic_enroll_button($id);
 			
 			$ret['content'] .= $enroll_button;
		}
		return $ret;
	}

	public function get_topic_info_v2($ret)
	{
		global $my_app_pai;	
        $global_header_html = $my_app_pai->webControl('act_topic_style', $ret, true);
        return $global_header_html;
	}
    
    public function get_topic_sign_up_list($type = 'all')
    {
        $sql_str = "SELECT * FROM pai_db.pai_sign_up_tbl ";
        if($type != 'all')
        {
            $sql_str .= " WHERE user_type = '{$type}'";
        }
        
        $result = db_simple_getdata($sql_str, FALSE, 101);
        return $result;
    }
    
    public function add_topic_sign_up($user_name, $user_tel, $user_type)
    {
        $add_time = date('Y-m-d H:i:s');
        $sql_str = "INSERT IGNORE INTO pai_db.pai_sign_up_tbl(user_name, user_tel, user_type, add_time) 
                    VALUES (:x_user_name, :x_user_tel, :x_user_type, :x_add_time)";
        sqlSetParam($sql_str, 'x_user_name', $user_name);
        sqlSetParam($sql_str, 'x_user_tel', $user_tel);
        sqlSetParam($sql_str, 'x_user_type', $user_type);
        sqlSetParam($sql_str, 'x_add_time', $add_time);
        
        db_simple_getdata($sql_str, TRUE, 101);
        return db_simple_get_affected_rows();
    }
    
	public function get_share_text($topic_data)
	{
		$title = '【'.$topic_data['title'].'】火热报名中 ︳约约';
		$content = '上约约，这里有中国最火爆的外拍活动集合';
		$sina_content = '上约约，这里有中国最火爆的外拍活动集合';
		//$share_url = 'http://yp.yueus.com/mobile/app?from_app=1#topic/'.$topic_data['id'];
		$share_url = 'http://www.yueus.com/topic/'.$topic_data['id'];
		$share_img = $topic_data['cover_image'];
		
		$share_text['title'] = $title;
		$share_text['content'] = $content;
		$share_text['sina_content'] = $sina_content.' '.$share_url;
		$share_text['remark'] = '';
		$share_text['url'] = $share_url;
		$share_text['img'] = $share_img; 
		$share_text['user_id'] = '';
		$share_text['qrcodeurl'] = '';
		
		return $share_text;
	}

	public function test2()
	{

		$this->test($id);
	}

	public function test($id)
	{
		global $my_app_pai;	
		if (!$id) 
		{
			return false;
		}
		$id = ( int ) $id;
		$ret = $this->find ( "id={$id}" );
        $global_header_html = $my_app_pai->webControl('act_topic_style', $ret, true);
        return $global_header_html;
	}

}

?>