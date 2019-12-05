<?php
//引入活动类
include_once('/disk/data/htdocs232/poco/event/poco_app_common.inc.php');

class pai_event_details_act_class extends event_details_class
{    
	/**
	 * 最后一次错误提示
	 * @var string
	 */
	protected $_last_err_msg = null;
	
	/**
	 * 活动资料数组
	 *
	 * @var array
	 */
	protected $_event_detail_arr = array();
    protected $enroll_obj;
    protected $system_obj;
    protected $check_obj;
    
	/**
	 * 构造函数
	 *
	 */
	public function __construct()
	{
		$this->setServerId(false);
		$this->setDBName('event_db');
		$this->setTableName('event_check_tbl');
        $this->enroll_obj = POCO::singleton('event_enroll_class');
        $this->system_obj = POCO::singleton('event_system_class');
        $this->check_obj  = POCO::singleton('event_check_class');
	}
}

?>