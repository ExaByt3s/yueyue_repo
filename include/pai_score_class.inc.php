<?php
class pai_score_class extends POCO_TDG 
{
	/**
	 * 构造函数
	 *
	 */
	public function __construct() {
		$this->setServerId ( 101 );
		$this->setDBName ( 'pai_score_db' );
		$this->setTableName ( 'pai_operate_queue_tbl' );
	}
	
	
	public function get_user_score_list($b_select_count = false, $where_str = '', $order_by = 'user_id DESC', $limit = '0,10', $fields = '*') {
		
		if ($b_select_count == true) {
			$ret = $this->findCount ( $where_str );
		} else{
			$ret = $this->findAll ( $where_str, $limit, $order_by ,$fields);
		}
		return $ret;
	}
    
    
    /**
     * 
     * 添加积分记录入队列
     * @param $user_id      用户ID
     * @param $operate      操作(消费：consume; 收入：income；评价： evaluate; 注册:regedit;更新模特资料：update_model)
     * @param $operate_num  操作值
     * @param $remark       备注
     * 
     * */
    public function add_operate_queue($user_id, $operate, $operate_num = 1, $remark = '') {
        $user_id = ( int ) $user_id;
        
        if (empty ( $user_id )) {
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':用户ID不能为空' );
		}
        
        if (empty ( $operate )) {
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':操作不能为空' );
		}
        
        if($user_id && $operate)
        {
            $insert_data['user_id']         = $user_id;
            $insert_data['operate']         = $operate;
            $insert_data['operate_num']     = $operate_num;
            $insert_data['remark']          = $remark;
            $insert_data['operate_time']   = date('Y-m-d H:i:s');
            
            $this->insert ( $insert_data ); 
            return TRUE;             
        }
        
        return FALSE;
    }
    
    /**
     * 
     * 获取用户积分
     * @param &user_id 用户ID 
     * 
     * */
    public function get_user_score_level($user_id)
    {
    	$user_score = $this->get_user_score_list(false,"user_id=".$user_id);
        //$array_result['level'] = $user_score[0]['level'];
		$array_result['level'] = $user_score[0]['level']?$user_score[0]['level']:1;
        $array_result['score'] = $user_score[0]['recently_score'];
        return $array_result;
    }
    
    /**
     * 
     * 获取用户积分排名
     * @param $location_id 地区ID 
     * @param $limit 获取条数
     * */
    public function get_user_score_top($location_id = 0, $limit = '0, 10')
    {
        $sql_str  = "SELECT L.user_id, L.score as num, L.level 
                    FROM pai_db.pai_score_rank_tbl L, pai_db.pai_user_tbl R 
                    WHERE L.user_id = R.user_id AND R.role='model'";
        if($location_id) $sql_str .= " AND R.location_id LIKE '$location_id%'";
        
        $sql_str .= " ORDER BY L.score DESC LIMIT " . $limit;
        $result   = db_simple_getdata($sql_str, FALSE,101);
        
	    foreach($result as $k=>$val){
			$result[$k]['nickname'] = get_user_nickname_by_user_id($val ['user_id']);;
			$result[$k]['user_icon'] = get_user_icon($val['user_id'], 165);
		}
		 
        return $result;

    }
    
}

?>