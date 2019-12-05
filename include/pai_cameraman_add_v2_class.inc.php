<?php


/**
 * ��Ӱʦ����汾2
 * @authors xiao xiao (xiaojm@yueus.com)
 * @date    2015��4��24��
 * @version 2
 */
class pai_cameraman_add_v2_class extends POCO_TDG
{
	
	/**
	 * ���캯��
	 *
	 */
	public function __construct()
	{
		$this->setServerId ( 101 );
		$this->setDBName ( 'pai_user_library_db' );
		$this->setTableName ( 'cameraman_info_tbl' );
	}

	/*
	 * �����Ӱʦ������Ϣ
	 * 
	 * @param data   ����
	 * 
	 * return id 
	 */
	public function add_info($data)
	{
		if(!is_array($data) || empty($data))
		{
			throw new App_Exception (__CLASS__ . '::' . __FUNCTION__ . ':���ݲ���Ϊ��');
		}
		return $this->insert($data,"IGNORE");
	}
	
	/*
	 * ��������
	 * 
	 * $data [array]     ��Ҫ���µ�����
	 * $user_id  [int]       �û�ID
	 * return [boolean]  true|false
	 * **/
	public function update_info($data,$user_id)
	{
		$user_id = intval($user_id);
		if($user_id < 1)
		{
			throw new App_Exception (__CLASS__ . '::' . __FUNCTION__ . ':ID����Ϊ��');
		}
		if(!is_array($data) || empty($data))
		{
			throw new App_Exception (__CLASS__ . '::' . __FUNCTION__ . ':���ݲ���Ϊ��');
		}
		if($this->get_info($user_id))
		{
			$where_str = "user_id = {$user_id}";
			return $this->update($data, $where_str);
		}
		else 
		{
			$data['user_id'] = $user_id;
			return $this->add_info($data);
		}
		
	}
	
	
	/**
	 * ����Ӱʦ��������
	 * $user_id [int] �û�ID
	 * return array $data
	 * */
	public function get_info($user_id)
	{
		$user_id = intval($user_id);
		if($user_id <1) return false;
		$where_str = "user_id = {$user_id}";
		return $this->find($where_str);
	}
	
	
	/*
     * ��Ӱʦ�б�
     * @param $b_select_count [boolean] true|false ��ѯ���������б�
     * @param $where_str      [string]             ��ѯ����
     * @param $order_by       [string]             ����
     * @param $limit          [string]             ѭ������
     * @param $fields         [string]             �ֶ���
     * return array $ret
	*/
	public function get_list($b_select_count = false, $where_str = '', $order_by = 'user_id DESC', $limit = '0,10', $fields = '*')
	{
		if ($b_select_count == true)
		{
			$sql_str = "SELECT COUNT(DISTINCT(C.user_id)) AS ct FROM pai_user_library_db.cameraman_info_tbl C LEFT JOIN pai_db.pai_user_tbl P ON P.user_id=C.user_id";
			if(strlen($where_str)>0) $sql_str .= ' WHERE ';
			$sql_str .= "{$where_str}";
			$ret = db_simple_getdata($sql_str,true,101);
			return $ret['ct'];
			//return $this->findCount ( $where_str );
		}
		$sql_str = "SELECT C.*,P.nickname,P.cellphone,P.location_id FROM pai_user_library_db.cameraman_info_tbl C LEFT JOIN pai_db.pai_user_tbl P ON P.user_id=C.user_id";
		if(strlen($where_str)>0) $sql_str .= ' WHERE ';
		$sql_str .= "{$where_str} GROUP BY C.user_id ORDER BY {$order_by} limit {$limit}";
		$ret = db_simple_getdata($sql_str,false,101);
		return $ret;
	}
	
	/*
	 * ��Ӱʦ�����б�
	 * @param $b_select_count [boolean] true|false ��ѯ���������б�
	 * @param $label_id       [string]             ��ǩ
	 * @param $f_start_time   [string]             ������ʼʱ��
	 * @param $f_end_time     [string]             ��������ʱ��
	 * @param $where_str      [string]             ��ѯ����
	 * @param $order_by       [string]             ����
	 * @param $limit          [string]             ѭ������
	 * @param $fields         [string]             �ֶ���
	 * return array $ret
	 */
	public function get_search_list($b_select_count = false,$label_id='',$f_start_time='',$f_end_time='', $where_str = '', $order_by = 'C.user_id DESC', $limit = '0,10', $fields = '*')
	{
		$label_id = trim($label_id);
		if(strlen($label_id) >0)
		{
			if(strlen($where_str)>0) $where_str .= ' AND ';
			$where_str .= "L.label_id IN (".mysql_escape_string($label_id).")";
		}
		if(strlen($f_start_time)>0)
		{
			if(strlen($where_str)>0) $where_str .=' AND ';
			$where_str .= "F.follow_time >= '{$f_start_time}'";
		}
		if (strlen($f_end_time)>0)
		{
			if(strlen($where_str)>0) $where_str .= ' AND ';
			$where_str .= "F.follow_time <= '{$f_end_time}'";
		}
		
		//echo $sql_str;
		if ($b_select_count == true)
		{
			$sql_str = "SELECT COUNT(DISTINCT(C.user_id)) AS ct FROM pai_user_library_db.cameraman_info_tbl C LEFT JOIN pai_user_library_db.cameraman_label_tbl L ON C.user_id=L.user_id LEFT JOIN pai_user_library_db.cameraman_follow_infornation_tbl F ON F.user_id= C.user_id,pai_db.pai_user_tbl P WHERE P.user_id=C.user_id";
			if(strlen($where_str) >0) $sql_str .= ' AND ';
			$sql_str .= "{$where_str}";
			$ret = db_simple_getdata($sql_str,true,101);
			return $ret['ct'];
		}
		$sql_str = "SELECT C.*,P.location_id,P.nickname,P.cellphone FROM pai_user_library_db.cameraman_info_tbl C LEFT JOIN pai_user_library_db.cameraman_label_tbl L ON C.user_id=L.user_id LEFT JOIN pai_user_library_db.cameraman_follow_infornation_tbl F ON F.user_id= C.user_id,pai_db.pai_user_tbl P WHERE P.user_id=C.user_id";
		if(strlen($where_str) >0) $sql_str .= ' AND ';
	    $sql_str .= "{$where_str} GROUP BY C.user_id ORDER BY {$order_by} limit  {$limit}";
		//echo $sql_str;
	    $ret = db_simple_getdata($sql_str,false,101);
		return $ret;
	}
	

}

?>