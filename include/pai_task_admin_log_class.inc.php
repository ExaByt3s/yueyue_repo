<?php
/**
 * 操作日志
 * 
 * @author KOKO
 * log type数字说明 
 * 1开头的4位数表示需求操作 ,如1001:需求审核;1002:需求外发(全);1003:需求外发(手动);1004:删除需求;1005:修改过期时间
 */

class pai_task_admin_log_class extends POCO_TDG
{

	private $type_name = array(
		                       1001=>'需求审核通过',
		                       1002=>'需求外发(手动)',
		                       1003=>'需求外发(全)',
		                       1004=>'删除需求',
		                       1005=>'修改过期时间',
		                       1006=>'需求审核不通过',
		                       1007=>'确认打款',
		                       1008=>'备注',		                       
		                       1009=>'系统自动成单',
							   
		                       2001=>'商品审核',		                       
		                       2002=>'商品上下架',		                       
		                       2003=>'添加商品',		                       
		                       2004=>'修改商品',		                       
		                       2005=>'删除商品',
		                       2006=>'商品审核通过',		                       
		                       2007=>'商品审核不通过',
		                       2008=>'商品上架',
		                       2009=>'商品下架',
		                       2010=>'活动编辑审核通过',
		                       2011=>'活动编辑审核不通过',

		                       3001=>'添加商家',
		                       3002=>'修改商家',
		                       3003=>'删除商家',
		                       3004=>'修改资料',
		                       3005=>'商家评级',

		                       4001=>'订单关闭',
		                       4002=>'订单接受',
		                       4003=>'订单签到',
		                       );

	/**
	 * 构造函数
	 */
	public function __construct()
	{
		$this->setServerId(101);
		$this->setDBName('pai_log_db');
	}
	
	/**
	 * 指定表
	 */
	private function set_task_admin_log_tbl()
	{
		$this->setTableName('task_admin_log_tbl');
	}
	
	/**
	 * 获取最后一条
	 * @param array $data
	 * @return array
	 */
	public function get_log_by_type_last($data)
	{
		$last = array();
		$return = $this->get_log_by_type($data);
		if($return)
		{
			$last = $return[0];			
		}
		return $last;		
	}
	
	/**
	 * 获取
	 * @param array $data
	 * @return array
	 */
	public function get_log_by_type($data)
	{
		$list = array();
		$type_id = (int)$data['type_id'];
		$action_type = (int)$data['action_type'];
		$action_id = (int)$data['action_id'];

		$this->set_task_admin_log_tbl();
		$where_str =1;
		$where_str .=$action_type?" AND `action_type` = {$action_type}":"";
		$where_str .=$type?" AND `type_id` = {$type_id}":"";
		$where_str .=$action_id?" AND `action_id` = {$action_id}":"";
		$list = $this->findAll($where_str,'','id desc');
		if($list)
		{
			foreach($list as $key => $val)
			{
				$list[$key]['type_name'] = $this->type_name[$val['type_id']];
			}
		}
		return $list;
	}
	
	
	/*
	 * 获取列表
	 * @param bool $b_select_count
	 * @param string $where_str 查询条件
	 * @param string $order_by 排序
	 * @param string $limit 
	 * @param string $fields 查询字段
	 * 
	 * return array
	 */
	public function get_log_list($b_select_count = false, $where_str = '', $order_by = 'add_time DESC', $limit = '0,10', $fields = '*')
	{
		$this->set_task_admin_log_tbl();
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
	 * 添加log
	 * @param string $admin_id
	 * @param string $type_id
	 * @param string $action_type 
	 * @param string $detail
	 * @param string $note
	 * @param string $action_id
	 * @return bool
	 */
	public function add_log($admin_id,$type_id,$action_type,$details=array(),$note='',$action_id=0)
	{
		$data = array(
			'admin_id' => (int)$admin_id,
			'type_id' => (int)$type_id,
			'action_type' => (int)$action_type,
			'action_id' => (int)$action_id,
			'note' => $note,
			'add_time' => time(),
			'details' => serialize($details),	
		);
		$this->set_task_admin_log_tbl();
		$this->insert($data, 'REPLACE');
		return true;
	}
}
