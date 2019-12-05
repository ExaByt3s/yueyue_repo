<?php
/**
 * 用户手机发信息黑名单
 *
 * @author 黄石汉
 */

class sms_service_blacklist_class extends POCO_TDG
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
	 * 指定黑名单表
	 */
	private function set_sms_blacklist_tbl()
	{
		$this->setTableName('sms_blacklist_tbl');
	}

	/**
	 * 指定指令表
	 */
	private function set_sms_cmd_tbl()
	{
		$this->setTableName('sms_cmd_tbl');
	}

	/**
	 * 通过信息内容获取指令信息
	 *
	 * @param int $cmd_val
	 * @return bool
	 */
	public function get_cmd_info_by_val($cmd_val)
	{
		if (empty($cmd_val))
		{
			return false;
		}
		$this->set_sms_cmd_tbl();
		$ret = $this->find("cmd_val='{$cmd_val}'");
		return $ret;
	}

	/**
	 * 通过指令ID获取信息
	 *
	 * @param int $cmd_id
	 * @return bool
	 */
	public function get_cmd_info($cmd_id)
	{
		$cmd_id = ( int ) $cmd_id;
		if (empty($cmd_id))
		{
			trace("非法参数 cmd_id 只能为整数",basename(__FILE__)." 行:".__LINE__." 方法:".__METHOD__);
			return false;
		}
		$this->set_sms_cmd_tbl();
		$ret = $this->find("cmd_id={$cmd_id}");
		return $ret;
	}

	/**
	 * 向指令库里增加指令信息
	 * @param $insert_data
	 * @param string $insert_type
	 * @return bool|int
	 */
	public function add_cmd($insert_data,$insert_type='IGNORE')
	{
		if( !is_array($insert_data) || empty($insert_data) )
		{
			return false;
		}
		$this->set_sms_cmd_tbl();
		return $this->insert($insert_data ,$insert_type);
	}

	/**
	 * 通过ID删除
	 *
	 * @param int    $cmd_id
	 * @return bool
	 */
	public function del_cmd($cmd_id)
	{
		$cmd_id = (int)$cmd_id;
		if (empty($cmd_id))
		{
			trace("非法参数 cmd_id 只能为整数",basename(__FILE__)." 行:".__LINE__." 方法:".__METHOD__);
			return false;
		}
		$this->set_sms_cmd_tbl();
		$where_str = " cmd_id={$cmd_id} ";
		return $this->delete($where_str);

	}

	/**
	 * 更新指令库
	 * @param array $data
	 * @param int   $cmd_id
	 * @return int
	 */
	public function modify_cmd($data,$cmd_id)
	{
		if( !is_array($data) || empty($data) || empty($cmd_id) )
		{
			return false;
		}
		$where_str = " cmd_id = {$cmd_id} ";
		$this->set_sms_cmd_tbl();
		$ret = $this->update($data,$where_str);
		return $ret;

	}

	/**
	 * 获取黑名单信息
	 * @param int $cellphone
	 * @return array
	 */
	public function get_blacklist_info($cellphone)
	{
		if ( !preg_match('/^1\d{10}$/', $cellphone) )
		{
			return array();
		}
		$this->set_sms_blacklist_tbl();
		return $this->find("cellphone={$cellphone}");
	}

	/**
	 * 添加 黑名单号码
	 * @param $cellphone
	 * @return int
	 */
	private function add($cellphone)
	{
		if ( !preg_match('/^1\d{10}$/', $cellphone) )
		{
			return 0;
		}
		$this->set_sms_blacklist_tbl();

		$data = array('cellphone' => $cellphone, 'add_time' => time());
		return $this->insert($data, 'IGNORE');
	}

	/**
	 * 删除黑名单信息
	 * @param int $cellphone
	 * @return array
	 */
	private function del($cellphone)
	{
		if ( !preg_match('/^1\d{10}$/', $cellphone) )
		{
			return array();
		}
		$this->set_sms_blacklist_tbl();
		return $this->delete("cellphone={$cellphone}");
	}


	/**
	 * 处理黑名单信息
	 * @param $data
	 * @return bool
	 */
	public function sms_blacklist($data)
	{
		$ret = false;
		$cmd_val = strtoupper($data['message']);
		$cellphone = $data['cellphone'];
		// 获取指令列表
		$cmd_list = $this->get_cmd_info_by_val($cmd_val);
		// 指令动作
		$action = isset($cmd_list['exec_val'])?$cmd_list['exec_val']:'';

		//判断动作是否为空  与 判断类库是否有此方法
		if (!empty($action) && in_array($action,get_class_methods ( 'sms_service_blacklist_class' )))
		{
			$ret = $this->$action($cellphone);
		}

		return $ret;
	}

}
