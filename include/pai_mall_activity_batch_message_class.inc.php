<?php
/*
 * 活动群发信息操作类
 */

class pai_mall_activity_batch_message_class extends POCO_TDG
{

    private $times = 3;
	
	/**
	 * 构造函数
	 *
	 */
	public function __construct()
	{
        $this->setServerId('101');
        $this->setDBName('mall_db');
	}

    
    private function set_mall_activity_batch_message_tbl()
    {
        $this->setTableName('mall_activity_batch_message_tbl');
    }
	
	/*
	 * 添加
	 * 
	 * @param array  $insert_data 
	 * 
	 * return bool 
	 */
	public function add_message_log($insert_data)
	{
		
		if (empty ( $insert_data ))
		{
			return false;
		}

        $this->set_mall_activity_batch_message_tbl();
		return $this->insert ( $insert_data );
	}

    /*
     * 统计发送次数
     * @param int $user_id
     * @param int $goods_id
     * @param int $stage_id
     */
    public function count_send_times($user_id,$goods_id,$stage_id)
    {
        $user_id = (int)$user_id;
        $goods_id = (int)$goods_id;
        $stage_id = (int)$stage_id;

        $this->set_mall_activity_batch_message_tbl();

        $sql_where = "user_id={$user_id} and goods_id={$goods_id} and stage_id={$stage_id}";
        return $this->findCount($sql_where);
    }

    /*
     * 剩余发送次数
     * @param int $user_id
     * @param int $goods_id
     * @param int $stage_id
     */
    public function send_times_left($user_id,$goods_id,$stage_id)
    {
        $user_id = (int)$user_id;
        $goods_id = (int)$goods_id;
        $stage_id = (int)$stage_id;

        $count_times = $this->count_send_times($user_id,$goods_id,$stage_id);

        $left_times = $this->times-$count_times;

        if($left_times<0)
        {
            $left_times=0;
        }

        return $left_times;
    }

    /*
     * 群发活动消息
     * @param int $user_id 发送人ID
     * @param int $goods_id 活动ID
     * @param int $stage_id 场次ID
     * @param string $content 发送内容
     * @return array
     */
    public function send_message($user_id,$goods_id,$stage_id,$content)
    {

        $user_id = (int)$user_id;
        $goods_id = (int)$goods_id;
        $stage_id = (int)$stage_id;

        if(empty($user_id) || empty($goods_id) ||empty($stage_id))
        {
            $result['result']=-1;
            $result['message']= "参数错误";
            return $result;
        }

        if(empty($content))
        {
            $result['result']=-1;
            $result['message']= "发送内容不能为空";
            return $result;
        }

        $goods_info=POCO::singleton('pai_mall_goods_class')->get_goods_info($goods_id);

        if($user_id!=$goods_info['goods_data']['user_id'])
        {
            $result['result']=-1;
            $result['message']= "你不是该活动发起人，不能群发信息";
            return $result;
        }

        $count_times = $this->count_send_times($user_id,$goods_id,$stage_id);
        if($count_times>=$this->times)
        {
            $result['result']=-1;
            $result['message']= "活动每场次只能群发信息{$this->times}次";
            return $result;
        }


        $order_list = POCO::singleton('pai_mall_order_class')->get_order_list_by_activity_stage($goods_id, $stage_id,-1);

        foreach($order_list as $val)
        {
            $user_id_arr[] = $val['buyer_user_id'];
        }

        $unique_arr = array_unique($user_id_arr);

        if(empty($unique_arr))
        {
            $result['result']=-1;
            $result['message']= "这场活动还没有人报名哦";
            return $result;
        }

        $msg_ret = POCO::singleton('pai_event_mass_message_class')->start_mass_message($user_id,'yueseller',$unique_arr,$content,'text');

        if($msg_ret['code']==1)
        {
            $insert_log_data['user_id'] = $user_id;
            $insert_log_data['goods_id'] = $goods_id;
            $insert_log_data['stage_id'] = $stage_id;
            $insert_log_data['content'] = $content;
            $insert_log_data['add_time'] = time();

            $this->add_message_log($insert_log_data);

            $result['result']=1;
            $result['message']= "发送成功";
            return $result;
        }
        else
        {
            $result['result']=-1;
            $result['message']= "发送失败";
            return $result;
        }
    }



	

}

?>