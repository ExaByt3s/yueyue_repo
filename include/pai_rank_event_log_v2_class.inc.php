<?php
/**
 * @desc:   ����ҳ������ҳ����v2�桾log��
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/7/20
 * @Time:   18:05
 * version: 2.0
 */

class pai_rank_event_log_v2_class extends POCO_TDG
{
	
	
	/**
	 * ���캯��
	 *
	 */
	public function __construct()
	{
		$this->setServerId ( 101 );
		$this->setDBName ( 'pai_log_db' );
		$this->setTableName ( 'pai_rank_event_v2_log' );
	}
	
	/*
	 * ���
	 * 
	 * @param array  $insert_data 
	 * 
	 * return bool 
	 */
	public function add_info($insert_data)
	{
		
		if (empty ( $insert_data ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':���鲻��Ϊ��' );
		}
		
		return $this->insert ( $insert_data );
	
	}
	 
	/*
	 * ��ȡ
	 * @param bool $b_select_count
	 * @param string $where_str ��ѯ����
	 * @param string $order_by ����
	 * @param string $limit 
	 * @param string $fields ��ѯ�ֶ�
	 * 
	 * return array
	 */
	public function get_rank_event_log_list($b_select_count = false, $where_str = '', $order_by = 'id DESC', $limit = '0,20', $fields = '*')
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

    /**
     * ��ϵ�л�����
     * @param int $id
     * @return array
     * @throws App_Exception
     */
    public function get_unserialize_rank_event_info($id)
	{
		$id = ( int ) $id;
		if (empty($id)) 
		{
			throw new App_Exception(__CLASS__.'::'.__FUNCTION__.':ID����Ϊ��');
		}
		$ret = $this->find ( "id={$id}",'','data_log' );
		return unserialize($ret['data_log']);
	}

	
}

?>