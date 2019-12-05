<?php
class pai_date_act_class extends POCO_TDG
{
	public function __construct()
	{
		$this->setServerId ( 1 );
		$this->setDBName ( 'event_db' );
		$this->setTableName ( 'event_date_tbl' );
	}
    
    public function get_date_list($user_id, $date_status = 'all', $source = 'all', $begin_time, $end_time, $limit = '0,10')
    {
        if($user_id)
        {
            $sql_str = "SELECT * FROM event_db.event_date_tbl WHERE from_date_id=$user_id ";
            
            if($date_status != 'all')
            {
                $sql_str .= " AND date_status='{$date_status}'";
            }
            
            if($source != 'all')
            {
                $sql_str .= " AND source='{$source}'";
            }
            
            if($begin_time)
            {
                $sql_str .= " AND date_time>=$begin_time";
            }
            
            if($end_time)
            {
                $sql_str .= " AND date_time<=$end_time"; 
            }
            
            $sql_str .= " LIMIT " . $limit;
            
            $result = db_simple_getdata($sql_str, FALSE);
            
            return $result;
            
        }
    }
    
    /**
     * 获取邀请列表，使用了优惠券 并且 某段时间签到的
     * @param int $model_user_id
     * @param int $begin_time
     * @param int $end_time
     * @param bool $b_select_count
     * @return array|int
     */
    public function get_date_list_by_coupon_and_checked($model_user_id, $begin_time, $end_time, $b_select_count=false)
    {
    	$model_user_id = intval($model_user_id);
    	$begin_time = intval($begin_time);
    	$end_time = intval($end_time);
    	if( $model_user_id<1 || $begin_time<1 || $end_time<1 || $begin_time>$end_time )
    	{
    		return $b_select_count ? 0 : array();
    	}
    	
    	//获取邀请列表
    	$event_date_obj = POCO::singleton('event_date_class');
    	$date_list_tmp = $event_date_obj->get_all_event_date(false, "to_date_id={$model_user_id} AND date_status='confirm' AND is_use_coupon=1", 'date_id ASC', '0,99999999');
    	if( empty($date_list_tmp) )
    	{
    		return $b_select_count ? 0 : array();
    	}
    	
    	//整理邀请列表、报名ID
    	$data_list = array();
    	$enroll_id_str = '';
    	foreach($date_list_tmp as $val)
    	{
    		$enroll_id_tmp = intval($val['enroll_id']);
    		$data_list[$enroll_id_tmp] = $val;
    		$enroll_id_str .= $enroll_id_tmp . ',';
    	}
    	$enroll_id_str = trim($enroll_id_str, ',');
    	unset($date_list_tmp);
    	
    	//获取签到记录
    	$where_str = "enroll_id IN ({$enroll_id_str}) AND is_checked=1 AND {$begin_time}<=update_time AND update_time<={$end_time}";
    	$activity_code_obj = POCO::singleton('pai_activity_code_class');
    	$code_list = $activity_code_obj->get_code_list($b_select_count, $where_str, 'id ASC', '0,99999999');
    	if( $b_select_count )
    	{
    		return $code_list;
    	}
    	
    	//整理结果列表
    	$result_list = array();
    	foreach($code_list as $val)
    	{
    		$enroll_id_tmp = intval($val['enroll_id']);
    		$result_list[] = $data_list[$enroll_id_tmp];
    	}
    	return $result_list;
    }
    
}

?>