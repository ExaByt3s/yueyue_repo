 <?php
	/**
	 * �������
	 *
	 * @author �ʻ�
	 * @copyright 2014-06-18
	 */
	

	
	class event_table_class extends POCO_TDG {
		/**
		 * ���һ�δ�����ʾ
		 * @var string
		 */
		protected $_last_err_msg = null;
		
		/**
		 * ���캯��
		 *
		 */
		public function __construct() {
			//$this->setServerId ( false );
			//$this->setDBName ( 'event_db' );
			//$this->setTableName ( 'event_table_tbl' );
		}

		
		/**
		 * ��ȡ�����
		 * 
		 * @param int $event_id �ID
		 * @param int $table_id ����ID
		 * */
		public function get_event_table($event_id, $table_id = 0) {
            $param[] = $event_id;
            $param[] = $table_id;
            $ret = curl_event_data('event_table_class','get_event_table',$param);
            return $ret;
		}

		/**
		 * ��ȡ�û�ܿɱ�������
		 *
		 * @param int $event_id �ID
		 *
		 * */
		public function get_event_table_num($event_id) {

            $param[] = $event_id;
            $ret = curl_event_data('event_table_class','get_event_table_num',$param);
            return $ret;
		}
		
		/*
         * ��ȡ����ID��Ӧ�ĳ���
    	 * @param int $event_id �ID
    	 * @return array  keyΪtable_id  valueΪ�ڼ���
     	*/
		public function get_event_table_num_array($event_id) {
            $param[] = $event_id;
            $ret = curl_event_data('event_table_class','get_event_table_num_array',$param);
            return $ret;
		}

		/**
		 * ��ȡ�û�ܿɱ�������
		 *
		 * @param int $event_id �ID
		 *
		 * */
		public function get_table_num($event_id, $table_id) {
            $param[] = $event_id;
            $param[] = $table_id;
            $ret = curl_event_data('event_table_class','get_table_num',$param);
            return $ret;
		}

		
		/*
      * ���ó����Ƿ�ɱ���
	  * @param int $table_id ����ID
	  * return bool
      */
		public function check_event_table_enroll($table_id) {

            $param[] = $table_id;
            $ret = curl_event_data('event_table_class','check_event_table_enroll',$param);
            return $ret;
		}


		/*
		 * ���ݻIDͳ�Ƴ���ȫ������
	     * @param int $event_id �ID
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