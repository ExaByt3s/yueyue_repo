<?php
/**
 * 接收短信运营商里用户返回的信息类
 *
 * @author 黄石汉
 */

class sms_service_receive_class extends POCO_TDG
{
	/**
	 * 构造函数
	 */
	public function __construct()
	{
		$this->setServerId(false);
		$this->setDBName('sms_service_db');
	}
	
	/**
	 * 指定表
	 */
	private function set_sms_receive_tbl()
	{
		$this->setTableName('sms_receive_tbl');
	}
	
	/**
	 * 添加
	 * @param array $data
	 * @return int
	 */
	public function add_receive($data)
	{
		if( !is_array($data) || empty($data) )
		{
			return 0;
		}
		$this->set_sms_receive_tbl();
		return $this->insert($data, 'IGNORE');
	}

	/**
	 * 修改
	 * @param array $data
	 * @param int $receive_id
	 * @return boolean
	 */
	public function update_receive($data, $receive_id)
	{
		//检查参数
		$receive_id = intval($receive_id);
		if( !is_array($data) || empty($data) || $receive_id<1 )
		{
			return false;
		}
		//保存
		$this->set_sms_receive_tbl();
		$affected_rows = $this->update($data, "receive_id={$receive_id}");
		return $affected_rows>0?true:false;
	}
	
	/**
	 * 获取信息
	 * @param int $receive_id
	 * @return array
	 */
	public function get_receive_info($receive_id)
	{
		$receive_id = intval($receive_id);
		if( $receive_id<1 )
		{
			return array();
		}
		$this->set_sms_receive_tbl();
		return $this->find("receive_id={$receive_id}");
	}

	/**
	 * 获取批次分类列表
	 * @param $product_type
	 * @param bool $b_select_count
	 * @param string $where_str
	 * @param string $order_by
	 * @param string $limit
	 * @param string $fields
	 * @return array|int
	 */
	public function get_receive_list($product_type, $b_select_count=false, $where_str='', $order_by='receive_id ASC', $limit='0,20', $fields='*')
	{
		//检查参数
		$product_type = intval($product_type);
		
		//整理查询条件
		$sql_where = '';
		if( $product_type>1 )
		{
			if( strlen($sql_where)>0 ) $sql_where .= ' AND ';
			$sql_where .= "product_type={$product_type}";
		}
		if( strlen($where_str)>0 )
		{
			if( strlen($sql_where)>0 ) $sql_where .= ' AND ';
			$sql_where .= $where_str;
		}
		
		//查询
		$this->set_sms_receive_tbl();
		if( $b_select_count )
		{
			return $this->findCount($sql_where);
		}
		return $this->findAll($sql_where, $limit, $order_by, $fields);
	}
	
	/**
	 * 提交接收
	 * @param int $product_type
	 * @param string $cellphone
	 * @param string $message
	 * @param string $service_num
	 * @param array $more_info array('msgid'=>'', 'receive_date'=>'')
	 * @return array
	 */
	public function submit_receive($product_type, $cellphone, $message, $service_num, $more_info=array())
	{
		$result = array('result'=>0, 'message'=>'', 'receive_id'=>0);
		
		//检查参数
		$msgid = trim($more_info['msgid']);
		$receive_date = $more_info['receive_date'];
		$message = trim($message);
		$cellphone = trim($cellphone);
		if( $product_type<1 || !preg_match('/^1\d{10}$/', $cellphone) )
		{
			$result['result'] = -1;
			$result['message'] = '参数错误';
			return $result;
		}
		
		$data = array(
			'product_type' => $product_type,
			'msgid' => $msgid,
			'cellphone' => $cellphone,
			'message' => $message,
			'service_num' => $service_num,
			'receive_date' => $receive_date,
			'add_time' => time(),
		);
		// 上行信息入库
		$receive_id = $this->add_receive($data);
		if( $receive_id<1 )
		{
			$result['result'] = -2;
			$result['message'] = '保存失败';
			return $result;
		}
		
		//判断如果为TY就增加黑名单 ，如果是DY，就定阅，删除黑名单
		$sms_service_blacklist_obj = POCO::singleton('sms_service_blacklist_class');
		$ret = $sms_service_blacklist_obj->sms_blacklist($data);

		if ( $ret )
		{
			$result['result'] = 1;
			$result['message'] = '成功';
			$result['receive_id'] = $receive_id;
			return $result;
		}
	}
	

}
