<?php
/**
 * 评论类
 * 
 * @author
 */

class pai_mall_comment_class extends POCO_TDG
{
	
	public function __construct()
	{
		$this->setServerId ( '101' );
		$this->setDBName ( 'mall_db' );
	}
	
	private function set_buyer_tbl()
	{
		$this->setTableName ( 'mall_comment_buyer_tbl' );
	}
	
	private function set_seller_tbl()
	{
		$this->setTableName ( 'mall_comment_seller_tbl' );
	}
	
	/*
	 *  消费者评商家
	 *  $insert_data['from_user_id']  评价人用户ID
	 *	$insert_data['to_user_id']  被评价人用户ID
     *  $insert_data['order_id'] 
	 *	$insert_data['goods_id']
	 *  $insert_data['goods_detail_id']
	 *  $insert_data['overall_score'] 总体评价分数
	 *  $insert_data['match_score']  商品符合分数
	 *  $insert_data['quality_score']  质量分数
	 *  $insert_data['comment']  评价内容
	 */
	public function add_seller_comment($data)
	{

		$data ['from_user_id'] = ( int ) $data ['from_user_id'];
		$data ['to_user_id'] = ( int ) $data ['to_user_id'];
		$data ['order_id'] = ( int ) $data ['order_id'];
		$data ['goods_id'] = ( int ) $data ['goods_id'];
		
		$order_obj = POCO::singleton ( 'pai_mall_order_class' );
		
		if (empty ( $data ['from_user_id'] ) || empty ( $data ['to_user_id'] ) || empty ( $data ['order_id'] )  || empty ( $data ['comment'] ))
		{
			$result ['result'] = - 1;
			$result ['message'] = '参数错误';
			return $result;
		}
		
		$check_is_comment = $this->check_seller_is_comment ( $data ['from_user_id'], $data ['to_user_id'], $data ['order_id'], $data ['goods_id'] );
		if ($check_is_comment)
		{
			$result ['result'] = - 2;
			$result ['message'] = '已评价过该商品';
			return $result;
		}


        if($data ['from_user_id']==$data ['to_user_id'])
        {
            $result['result'] = -1;
            $result['message'] = '自己不能评价自己';
            return $result;
        }
		
		$order_info = $order_obj->get_order_info_by_id($data ['order_id']);
		//检查订单
		if( $data ['from_user_id']!=$order_info['buyer_user_id'] )
		{
			$result['result'] = -3;
			$result['message'] = '非法操作';
			return $result;
		}
		
		$this->set_seller_tbl ();
		
		if($data ['overall_score']>5)
		{
			$data ['overall_score'] = 5;
		}
		
		$insert_data ['from_user_id'] = $data ['from_user_id'];
		$insert_data ['to_user_id'] = $data ['to_user_id'];
        $insert_data ['type_id'] = $order_info ['type_id'];
		$insert_data ['order_id'] = $data ['order_id'];
		$insert_data ['goods_id'] = $data ['goods_id'];
		$insert_data ['stage_id'] = $data ['stage_id'];
		$insert_data ['overall_score'] = $data ['overall_score'];
		$insert_data ['match_score'] = $data ['match_score'];
		$insert_data ['manner_score'] = $data ['manner_score'];
		$insert_data ['quality_score'] = $data ['quality_score'];
		$insert_data ['comment'] = $data ['comment'];
		$insert_data ['is_anonymous'] = $data ['is_anonymous'];
        if($data ['add_time'])
        {
            $insert_data ['add_time'] = $data ['add_time'];
        }
        else
        {
            $insert_data ['add_time'] = time ();
        }

        //$log_arr['insert_data'] = $insert_data;
        //pai_log_class::add_log($log_arr, 'test_comment', 'test_comment');
		
		$ret = $this->insert ( $insert_data );
		
		if ($ret)
		{
			$this->update_profile_score ( $data ['to_user_id'], $data ['overall_score'], $data ['match_score'], $data ['match_score'], $data ['quality_score'] );
			$this->update_goods_score ( $data ['goods_id'], $data ['overall_score'], $data ['match_score'], $data ['match_score'], $data ['quality_score'] );
			
			
			$order_obj->comment_order_for_buyer($data ['order_id'],$data ['from_user_id'],$data ['is_anonymous']);
			
			$result ['result'] = 1;
			$result ['message'] = '评价成功';
			return $result;
		}
		else
		{
			$result ['result'] = - 3;
			$result ['message'] = '评价失败';
			return $result;
		}
	}
	
	/*
	 * 更新商家资料评价分数
	 * @param int $seller_id 
	 * @param int $overall_score 
	 * @param int $match_score 
	 * @param int $manner_score 
	 * @param int $quality_score 
	 */
	private function update_profile_score($user_id, $overall_score, $match_score, $manner_score, $quality_score)
	{
		$overall_score = ( int ) $overall_score;
		$match_score = ( int ) $match_score;
		$manner_score = ( int ) $manner_score;
		$quality_score = ( int ) $quality_score;
		$user_id = ( int ) $user_id;
		
		if (empty ( $user_id ))
		{
			return false;
		}
		
		$sql = "update mall_db.mall_seller_profile_tbl set 
		       total_overall_score=total_overall_score+{$overall_score},
		       total_match_score=total_match_score+{$match_score},
		       total_manner_score=total_manner_score+{$manner_score},
		       total_quality_score=total_quality_score+{$quality_score},review_times=review_times+1
		       where user_id={$user_id}";
		return db_simple_getdata ( $sql, false, 101 );
	}
	
	/*
	 * 更新商品评价分数
	 * @param int $goods_id 
	 * @param int $overall_score 
	 * @param int $match_score 
	 * @param int $manner_score 
	 * @param int $quality_score 
	 */
	private function update_goods_score($goods_id, $overall_score, $match_score, $manner_score, $quality_score)
	{
		$overall_score = ( int ) $overall_score;
		$match_score = ( int ) $match_score;
		$manner_score = ( int ) $manner_score;
		$quality_score = ( int ) $quality_score;
		$goods_id = ( int ) $goods_id;
		
		if (empty ( $goods_id ))
		{
			return false;
		}
		$sql = "update mall_db.mall_goods_tbl set 
		       total_overall_score=total_overall_score+{$overall_score},
		       total_match_score=total_match_score+{$match_score},
		       total_manner_score=total_manner_score+{$manner_score},
		       total_quality_score=total_quality_score+{$quality_score},review_times=review_times+1
		       where goods_id={$goods_id}";
		return db_simple_getdata ( $sql, false, 101 );
	
	}
	
	/*
	 * 商家评消费者
	 */
	public function add_buyer_comment($data)
	{
		$data ['from_user_id'] = ( int ) $data ['from_user_id'];
		$data ['to_user_id'] = ( int ) $data ['to_user_id'];
		$data ['order_id'] = ( int ) $data ['order_id'];
		$data ['goods_id'] = ( int ) $data ['goods_id'];
		
		$order_obj = POCO::singleton ( 'pai_mall_order_class' );
		$user_data_obj = POCO::singleton ( 'pai_user_data_class' );
		
		
		if (empty ( $data ['from_user_id'] ) || empty ( $data ['to_user_id'] ) || empty ( $data ['order_id'] )  || empty ( $data ['comment'] ))
		{
			$result ['result'] = - 1;
			$result ['message'] = '参数错误';
			return $result;
		}
		
		$check_is_comment = $this->check_buyer_is_comment ( $data ['from_user_id'], $data ['to_user_id'], $data ['order_id'], $data ['goods_id'] );
		if ($check_is_comment)
		{
			$result ['result'] = - 2;
			$result ['message'] = '已评价过该商品';
			return $result;
		}

        if($data ['from_user_id']==$data ['to_user_id'])
        {
            $result['result'] = -1;
            $result['message'] = '自己不能评价自己';
            return $result;
        }
		
		$order_info = $order_obj->get_order_info_by_id($data ['order_id']);
		//检查订单
		if( $data ['from_user_id']!=$order_info['seller_user_id'] )
		{
            $log_data['from_user_id'] = $data ['from_user_id'];
            $log_data['seller_user_id'] = $order_info['seller_user_id'];
            $log_data['order_id'] = $data['order_id'];
            pai_log_class::add_log($log_data, 'add_buyer_comment', 'comment_data');

			$result['result'] = -3;
			$result['message'] = '非法操作';
			return $result;
		}
		
		$this->set_buyer_tbl ();
		
		if($data ['overall_score']>5)
		{
			$data ['overall_score'] = 5;
		}
		
		$insert_data ['from_user_id'] = $data ['from_user_id'];
		$insert_data ['to_user_id'] = $data ['to_user_id'];
        $insert_data ['type_id'] = $order_info ['type_id'];
		$insert_data ['order_id'] = $data ['order_id'];
		$insert_data ['goods_id'] = $data ['goods_id'];
        $insert_data ['stage_id'] = $data ['stage_id'];
		$insert_data ['overall_score'] = $data ['overall_score'];
		$insert_data ['comment'] = $data ['comment'];
		$insert_data ['is_anonymous'] = $data ['is_anonymous'];
        if($data ['add_time'])
        {
            $insert_data ['add_time'] = $data ['add_time'];
        }
        else
        {
            $insert_data ['add_time'] = time ();
        }

		
		$ret = $this->insert ( $insert_data );
		
		if ($ret)
		{
			
			$order_obj->comment_order_for_seller($data ['order_id'],$data ['from_user_id'],$data ['is_anonymous']);
			$user_data_obj->update_comment_score($data ['to_user_id']);
			
			$result ['result'] = 1;
			$result ['message'] = '评价成功';
			return $result;
		}
		else
		{
			$result ['result'] = - 3;
			$result ['message'] = '评价失败';
			return $result;
		}
	}
	
	/*
	 * 是否已评价商品
	 * @param int $from_user_id 
	 * @param int $to_user_id 
	 * @param int $order_id 
	 * @param int $goods_id 
	 * @return bool
	 */
	public function check_seller_is_comment($from_user_id, $to_user_id, $order_id, $goods_id)
	{
		$this->set_seller_tbl ();
		
		$from_user_id = ( int ) $from_user_id;
		$to_user_id = ( int ) $to_user_id;
		$order_id = ( int ) $order_id;
		$goods_id = ( int ) $goods_id;
		
		$where_str = "from_user_id={$from_user_id} and to_user_id={$to_user_id} and order_id={$order_id} and goods_id={$goods_id}";
		$ret = $this->findCount ( $where_str );
		
		if ($ret)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	/*
	 * 是否已评价消费者
	 * @param int $from_user_id 
	 * @param int $to_user_id 
	 * @param int $order_id 
	 * @param int $goods_id 
	 * @return bool
	 */
	public function check_buyer_is_comment($from_user_id, $to_user_id, $order_id, $goods_id)
	{
		$this->set_buyer_tbl ();
		
		$from_user_id = ( int ) $from_user_id;
		$to_user_id = ( int ) $to_user_id;
		$order_id = ( int ) $order_id;
		$goods_id = ( int ) $goods_id;
		
		$where_str = "from_user_id={$from_user_id} and to_user_id={$to_user_id} and order_id={$order_id} and goods_id={$goods_id}";
		$ret = $this->findCount ( $where_str );
		
		if ($ret)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	/*
	 * 消费者评商家列表
	 * @param int $user_id 商家用户ID
	 * @param bool $b_select_count
	 * @param string $where_str 
	 * @param string $order_by
	 * @param string $limit 
	 * @param string $fields 
	 * return array
	 */
	public function get_seller_comment_list($user_id,$b_select_count = false, $where_str = '', $order_by = 'comment_id DESC', $limit = '0,10', $fields = '*')
	{
		$sql_where = '';

		if( $user_id>0 )
		{
			if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
			$sql_where .= "to_user_id={$user_id}";
		}
        else
        {
            return array();
        }
		
		if (strlen($where_str) > 0)
		{
			if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
			$sql_where .= $where_str;
		}
		
		$this->set_seller_tbl ();
		if ($b_select_count == true)
		{
			$ret = $this->findCount ( $sql_where );
		}
		else
		{
			$ret = $this->findAll ( $sql_where, $limit, $order_by, $fields );
		}
		return $ret;
	}


    /*
     * 消费者评商家列表
     * @param int $user_id 商家用户ID
     * @param int $goods_id 商品ID
     * @param bool $b_select_count
     * @param string $order_by
     * @param string $limit
     * @param string $fields
     * return array
     */
    public function get_seller_comment_list_by_user_id_or_goods_id($user_id,$goods_id,$b_select_count = false, $order_by = 'comment_id DESC', $limit = '0,10', $fields = '*')
    {
        $sql_where = '';

        if(empty($user_id) && empty($goods_id))
        {
            return false;
        }

        if( $user_id>0 )
        {
            if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
            $sql_where .= "to_user_id={$user_id}";
        }

        if( $goods_id>0 )
        {
            if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
            $sql_where .= "goods_id={$goods_id}";
        }


        $this->set_seller_tbl ();
        if ($b_select_count == true)
        {
            $ret = $this->findCount ( $sql_where );
        }
        else
        {
            $ret = $this->findAll ( $sql_where, $limit, $order_by, $fields );
            $ret=$this->fill_seller_data_list($ret);
        }
        return $ret;
    }
	
	/*
	 * 商家评消费者列表
	 * @param int $user_id 消费者用户ID
	 * @param bool $b_select_count
	 * @param string $where_str 
	 * @param string $order_by
	 * @param string $limit 
	 * @param string $fields 
	 * return array
	 */
	public function get_buyer_comment_list($user_id, $b_select_count = false, $where_str = '', $order_by = 'comment_id DESC', $limit = '0,10', $fields = '*')
	{
		$sql_where = '';
		
		if( $user_id>0 )
		{
			if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
			$sql_where .= "to_user_id={$user_id}";
		}
        else
        {
            return array();
        }
		
		if (strlen($where_str) > 0)
		{
			if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
			$sql_where .= $where_str;
		}
		
		$this->set_buyer_tbl ();
		if ($b_select_count == true)
		{
			$ret = $this->findCount ( $sql_where );
		}
		else
		{
			$ret = $this->findAll ( $sql_where, $limit, $order_by, $fields );
		}
		return $ret;
	}


     /*
     * 商家评消费者列表
     * @param int $user_id 消费者用户ID
     * @param int $goods_id 商品ID
     * @param bool $b_select_count
     * @param string $order_by
     * @param string $limit
     * @param string $fields
     * return array
     */
    public function get_buyer_comment_list_by_user_id_or_goods_id($user_id, $goods_id,$b_select_count = false, $order_by = 'comment_id DESC', $limit = '0,10', $fields = '*')
    {
        $sql_where = '';

        if(empty($user_id) && empty($goods_id))
        {
            return false;
        }

        if( $user_id>0 )
        {
            if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
            $sql_where .= "to_user_id={$user_id}";
        }

        if( $goods_id>0 )
        {
            if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
            $sql_where .= "goods_id={$goods_id}";
        }

        $this->set_buyer_tbl ();
        if ($b_select_count == true)
        {
            $ret = $this->findCount ( $sql_where );
        }
        else
        {
            $ret = $this->findAll ( $sql_where, $limit, $order_by, $fields );
        }
        return $ret;
    }


    private function fill_seller_data_list($list)
    {
        if(!is_array($list))
        {
            return array();
        }

        foreach($list as $k=>$val)
        {
            if($val['is_anonymous']==1)
            {
                $list[$k]['buyer_nickname'] = "匿名";
                $list[$k]['buyer_user_icon'] = "http://yue-icon-d.yueus.com/1/10000_86.jpg";
            }
            else
            {
                $list[$k]['buyer_nickname'] = get_user_nickname_by_user_id($val['from_user_id']);
                $list[$k]['buyer_user_icon'] = get_user_icon($val['from_user_id'], 64);
            }

        }

        return $list;
    }
	
	/*
	 * 获取消费者对商家订单的商品评价
	 * @param int $order_id
	 * @param int $goods_id
	 * @return array
	 */
	public function get_seller_comment_info($order_id,$goods_id)
	{
		$order_id = (int)$order_id;
		$goods_id = (int)$goods_id;
		$this->set_seller_tbl ();
		
		$sql_where = "";
		
		if( $order_id>0 )
		{
			if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
			$sql_where .= "order_id={$order_id}";
		}
		
		if( $goods_id>0 )
		{
			if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
			$sql_where .= "goods_id={$goods_id}";
		}
		
		
		$ret = $this->find ( $sql_where );
		return $ret;
	}
	
	
	/*
	 * 获取商家对消费者订单的商品评价
	 * @param int $order_id
	 * @param int $goods_id
	 * @return array
	 */
	public function get_buyer_comment_info($order_id,$goods_id)
	{
		$order_id = (int)$order_id;
		$goods_id = (int)$goods_id;
		$this->set_buyer_tbl ();
		
		$sql_where = "";
		
		if( $order_id>0 )
		{
			if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
			$sql_where .= "order_id={$order_id}";
		}
		
		if( $goods_id>0 )
		{
			if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
			$sql_where .= "goods_id={$goods_id}";
		}
		
		$ret = $this->find ( $sql_where );
		return $ret;
	}
	
	/*
	 * 格式化输出分数给前端
	 */
	public function format_comment_score($num)
	{
		if($num)
		{
			$overall_score_has_star = intval ( $num );
			$overall_score_miss_star = 5 - $overall_score_has_star;
		
			for($i = 0; $i < 5; $i ++)
			{
				if ($overall_score_has_star > 0)
				{
					$format_comment ['stars_list'] [$i] ['is_red'] = 1;
			
					$overall_score_has_star --;
				}
				else
				{
					$format_comment ['stars_list'] [$i] ['is_red'] = 0;
				
					$overall_score_miss_star --;
				}
			}
			
			return $format_comment;
		}
	}
	
	/*
	 * 商家评价总分排名列表
	 * @param int $before_day  多少天前
	 * @param int $limit
	 */
    public function seller_comment_rank_list($before_day=7,$limit=10)
    {
        $limit = (int)$limit;
        $before_day = (int)$before_day;
        if(!$limit) $limit = 10;
        if(!$before_day) $before_day = 7;

        $date_time = strtotime("-{$before_day} day");

        $sql = "select to_user_id as user_id,count(*) as c,sum(overall_score) as sum_score from mall_db.mall_comment_seller_tbl where add_time>{$date_time} group by user_id order by sum_score desc limit {$limit}";
        $ret = $this->query($sql);
        return $ret;
    }
}
