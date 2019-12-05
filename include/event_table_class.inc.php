 <?php
	/**
	 * 活动场次类
	 *
	 * @author 肥华
	 * @copyright 2014-06-18
	 */
	

	
	class event_table_class extends POCO_TDG {
		/**
		 * 最后一次错误提示
		 * @var string
		 */
		protected $_last_err_msg = null;
		
		/**
		 * 构造函数
		 *
		 */
		public function __construct() {
			//$this->setServerId ( false );
			//$this->setDBName ( 'event_db' );
			//$this->setTableName ( 'event_table_tbl' );
		}

		
		/**
		 * 获取活动场次
		 * 
		 * @param int $event_id 活动ID
		 * @param int $table_id 场次ID
		 * */
		public function get_event_table($event_id, $table_id = 0) {
            $param[] = $event_id;
            $param[] = $table_id;
            $ret = curl_event_data('event_table_class','get_event_table',$param);
            return $ret;
		}

		/**
		 * 获取该活动总可报名人数
		 *
		 * @param int $event_id 活动ID
		 *
		 * */
		public function get_event_table_num($event_id) {

            $param[] = $event_id;
            $ret = curl_event_data('event_table_class','get_event_table_num',$param);
            return $ret;
		}
		
		/*
         * 获取场次ID对应的场次
    	 * @param int $event_id 活动ID
    	 * @return array  key为table_id  value为第几场
     	*/
		public function get_event_table_num_array($event_id) {
            $param[] = $event_id;
            $ret = curl_event_data('event_table_class','get_event_table_num_array',$param);
            return $ret;
		}

		/**
		 * 获取该活动总可报名人数
		 *
		 * @param int $event_id 活动ID
		 *
		 * */
		public function get_table_num($event_id, $table_id) {
            $param[] = $event_id;
            $param[] = $table_id;
            $ret = curl_event_data('event_table_class','get_table_num',$param);
            return $ret;
		}

		
		/*
      * 检查该场次是否可报名
	  * @param int $table_id 场次ID
	  * return bool
      */
		public function check_event_table_enroll($table_id) {

            $param[] = $table_id;
            $ret = curl_event_data('event_table_class','check_event_table_enroll',$param);
            return $ret;
		}


		/*
		 * 根据活动ID统计场次全部人数
	     * @param int $event_id 活动ID
		 */
		public function sum_table_num($event_id){
            $param[] = $event_id;
            $ret = curl_event_data('event_table_class','sum_table_num',$param);
            return $ret;
		}
		
		public function get_table_info($table_id)
		{
            $param[] = $table_id;
            $ret = curl_event_data('event_table_class','get_table_info',$param);
            return $ret;
		}
	
	}
	?>