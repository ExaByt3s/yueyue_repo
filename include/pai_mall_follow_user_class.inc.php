<?php
/*
 * 用户关注类
 */

class pai_mall_follow_user_class extends POCO_TDG
{

	/**
	 * 构造函数
	 *
	 */
	public function __construct()
	{
		$this->setServerId ( 101 );
		$this->setDBName ( 'mall_db' );
	}


    private function set_follow_user_tbl()
    {
        $this->setTableName('mall_follow_user_tbl');
    }

    private function set_follow_goods_tbl()
    {
        $this->setTableName('mall_follow_goods_tbl');
    }

	/*
	 * 添加用户关注
	 *
	 * @param int    $follow_user_id    关注人用户ID
	 * @param int    $be_follow_user_id 被关注人用户ID
	 *
	 * return array
	 */
	public function add_user_follow($follow_user_id, $be_follow_user_id)
	{
		$follow_user_id = ( int ) $follow_user_id;
		$be_follow_user_id = ( int ) $be_follow_user_id;

		if (empty ( $follow_user_id ) || empty ( $be_follow_user_id ))
		{
            $result['result'] = -1;
            $result['message'] = '参数错误';
			return $result;
		}

        if($follow_user_id==$be_follow_user_id)
        {
            $result['result'] = -2;
            $result['message'] = '自己不能关注自己';
            return $result;
        }

        $mall_seller_obj = POCO::singleton ( 'pai_mall_seller_class' );
        $check_seller_exist = $mall_seller_obj->get_seller_info ( $be_follow_user_id, 2 );
        if(!$check_seller_exist)
        {
            $result['result'] = -2;
            $result['message'] = '被关注人不是商家';
            return $result;
        }

        $check_follow = $this->check_user_follow($follow_user_id, $be_follow_user_id);
        if($check_follow)
        {
            $result['result'] = -2;
            $result['message'] = '你已关注过该用户了';
            return $result;
        }

        $type_id_str = $mall_seller_obj->get_first_profile_type_id_by_user_id($be_follow_user_id);

        $insert_data ['type_id_str'] = $type_id_str;
		$insert_data ['follow_user_id'] = $follow_user_id;
		$insert_data ['be_follow_user_id'] = $be_follow_user_id;
		$insert_data ['follow_type'] = "collect";
		$insert_data ['add_time'] = time ();

        $this->set_follow_user_tbl();

		$ret = $this->insert ( $insert_data );

        if($ret)
        {
            $result['result'] = 1;
            $result['message'] = '关注成功';
            return $result;
        }
        else
        {
            $result['result'] = -1;
            $result['message'] = '关注失败';
            return $result;
        }

	}


    /*
     * 添加已成交用户
     *
     * @param int    $buyer_user_id    买家ID
     * @param int    $seller_user_id 卖家ID
     *
     * return array
     */
    public function add_user_deal($buyer_user_id, $seller_user_id)
    {
        $buyer_user_id = ( int ) $buyer_user_id;
        $seller_user_id = ( int ) $seller_user_id;

        if (empty ( $buyer_user_id ) || empty ( $seller_user_id ))
        {
            $result['result'] = -1;
            $result['message'] = '参数错误';
            return $result;
        }

        if($buyer_user_id==$seller_user_id)
        {
            $result['result'] = -2;
            $result['message'] = '自己不能购买自己';
            return $result;
        }

        $mall_seller_obj = POCO::singleton ( 'pai_mall_seller_class' );
        $check_seller_exist = $mall_seller_obj->get_seller_info ( $seller_user_id, 2 );
        if(!$check_seller_exist)
        {
            $result['result'] = -2;
            $result['message'] = '被关注人不是商家';
            return $result;
        }


        $type_id_str = $mall_seller_obj->get_first_profile_type_id_by_user_id($seller_user_id);

        $insert_data ['type_id_str'] = $type_id_str;
        $insert_data ['follow_user_id'] = $buyer_user_id;
        $insert_data ['be_follow_user_id'] = $seller_user_id;
        $insert_data ['follow_type'] = "deal";
        $insert_data ['add_time'] = time ();

        $this->set_follow_user_tbl();

        $this->insert ( $insert_data,"IGNORE" );

        $result['result'] = 1;
        $result['message'] = '添加成功';
        return $result;


    }

    /*
     * 添加商品关注
     *
     * @param int    $user_id    关注人用户ID
     * @param int    $goods_id 关注商品ID
     *
     * return array
     */
    public function add_goods_follow($user_id, $goods_id)
    {
        $user_id = ( int ) $user_id;
        $goods_id = ( int ) $goods_id;

        if (empty ( $user_id ) || empty ( $goods_id ))
		{
            $result['result'] = -1;
            $result['message'] = '参数错误';
            return $result;
        }

        $check_follow = $this->check_goods_follow($user_id, $goods_id);
        if($check_follow)
        {
            $result['result'] = -2;
            $result['message'] = '你已关注过该商品了';
            return $result;
        }


        $goods_obj = POCO::singleton('pai_mall_goods_class');

        $goods_info = $goods_obj->get_goods_info($goods_id);

		$insert_data ['user_id'] = $user_id;
        $insert_data ['type_id'] = $goods_info['goods_data']['type_id'];
		$insert_data ['goods_id'] = $goods_id;
		$insert_data ['add_time'] = time ();

        $this->set_follow_goods_tbl();

		$ret = $this->insert ( $insert_data );

        if($ret)
        {
            $result['result'] = 1;
            $result['message'] = '关注成功';
            return $result;
        }
        else
        {
            $result['result'] = -1;
            $result['message'] = '关注失败';
            return $result;
        }

	}

	/*
	 * 获取用户关注数据
	 * @param bool $b_select_count
	 * @param string $where_str 查询条件
	 * @param string $order_by 排序
	 * @param string $limit
	 * @param string $fields 查询字段
	 *
	 * return array
	 */
	public function get_user_follow_list($b_select_count = false, $where_str = '', $order_by = 'user_id DESC', $limit = '0,10', $fields = '*')
	{
        $this->set_follow_user_tbl();

		if ($b_select_count == true)
		{
			$ret = $this->findCount ( $where_str );
		} else
		{
			$ret = $this->findAll ( $where_str, $limit, $order_by, $fields );
		}
		return $ret;
	}

    /*
     * 获取商品关注数据
     * @param bool $b_select_count
     * @param string $where_str 查询条件
     * @param string $order_by 排序
     * @param string $limit
     * @param string $fields 查询字段
     *
     * return array
     */
    public function get_goods_follow_list($b_select_count = false, $where_str = '', $order_by = 'goods_id DESC', $limit = '0,10', $fields = '*')
    {
        $this->set_follow_goods_tbl();

        if ($b_select_count == true)
        {
            $ret = $this->findCount ( $where_str );
        } else
        {
            $ret = $this->findAll ( $where_str, $limit, $order_by, $fields );
        }
        return $ret;
    }

	/*
	 * 是否已关注该用户
	 *
	 * @param int    $follow_user_id    关注人用户ID
	 * @param int    $be_follow_user_id 被关注人用户ID
	 *
	 * return bool
	 */
	public function check_user_follow($follow_user_id, $be_follow_user_id)
	{
		$follow_user_id = ( int ) $follow_user_id;
		$be_follow_user_id = ( int ) $be_follow_user_id;

		if (empty ( $follow_user_id ) || empty ( $be_follow_user_id ))
		{
			return false;
		}

		$where_str = "follow_user_id={$follow_user_id} and be_follow_user_id={$be_follow_user_id} and follow_type='collect'";

		$ret = $this->get_user_follow_list ( true, $where_str );

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
     * 是否已关注该商品
     *
     * @param int    $user_id    关注人用户ID
     * @param int    $goods_id 商品ID
     *
     * return bool
     */
    public function check_goods_follow($user_id, $goods_id)
    {
        $user_id = ( int ) $user_id;
        $goods_id = ( int ) $goods_id;

        if (empty ( $user_id ) || empty ( $goods_id ))
        {
            return false;
        }

        $where_str = "user_id={$user_id} and goods_id={$goods_id}";

        $ret = $this->get_goods_follow_list ( true, $where_str );

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
	 * 取消关注用户
	 *
	 * @param int    $follow_user_id    关注人用户ID
	 * @param int    $be_follow_user_id 被关注人用户ID
	 *
	 * return array
	 */
	public function cancel_user_follow($follow_user_id, $be_follow_user_id)
	{
		$follow_user_id = ( int ) $follow_user_id;
		$be_follow_user_id = ( int ) $be_follow_user_id;

        if (empty ( $follow_user_id ) || empty ( $be_follow_user_id ))
        {
            $result['result'] = -1;
            $result['message'] = '参数错误';
            return $result;
        }

        $this->set_follow_user_tbl();

		$where_str = "follow_user_id={$follow_user_id} and be_follow_user_id={$be_follow_user_id}";

		$ret = $this->delete ( $where_str );

        if($ret)
        {
            $result['result'] = 1;
            $result['message'] = '取消关注成功';
            return $result;
        }
        else
        {
            $result['result'] = -1;
            $result['message'] = '取消关注失败';
            return $result;
        }

	}


    /*
     * 取消关注商品
     *
     * @param int    $user_id    关注人用户ID
     * @param int    $_id 被关注人用户ID
     *
     * return array
     */
    public function cancel_goods_follow($user_id, $goods_id)
    {
        $user_id = ( int ) $user_id;
        $goods_id = ( int ) $goods_id;

        if (empty ( $user_id ) || empty ( $goods_id ))
        {
            $result['result'] = -1;
            $result['message'] = '参数错误';
            return $result;
        }

        $this->set_follow_goods_tbl();

        $where_str = "user_id={$user_id} and goods_id={$goods_id}";

        $ret = $this->delete ( $where_str );

        if($ret)
        {
            $result['result'] = 1;
            $result['message'] = '取消关注成功';
            return $result;
        }
        else
        {
            $result['result'] = -1;
            $result['message'] = '取消关注失败';
            return $result;
        }

    }

	/*
	 * 根据用户ID获取关注人数
	 * @param string $follow_type  获取类型  collect为已收藏  deal为已成交
	 * @param bool $b_select_count  是否取总数
	 * @param int $user_id
	 * @param int $type_id  筛选的服务ID
	 * @param string $order_by  排序 add_time为按更新时间，user_last_update_time为按用户更新时间
	 * @param string $limit
	 */
    function get_follow_by_user_id($follow_type='collect',$b_select_count = false,$user_id, $type_id='', $order_by='add_time', $limit = '0,10')
    {
        $seller_obj = POCO::singleton('pai_mall_seller_class');

        $user_id = ( int ) $user_id;
        $type_id = ( int ) $type_id;

        if(!in_array($follow_type,array("collect","deal")))
        {
            $follow_type = "collect";
        }

        $where_str = "follow_user_id={$user_id} AND follow_type='{$follow_type}'";

        if($type_id)
        {
            $where_str .= " AND type_id_str REGEXP '(,|^){$type_id}(,|$)'";
        }

        if(!in_array($order_by,array('add_time','user_last_update_time')))
        {
            $order_by = "add_time";
        }

        $ret = $this->get_user_follow_list ( $b_select_count, $where_str, "{$order_by} DESC,id DESC", $limit, 'be_follow_user_id  as user_id,type_id_str' );
        foreach ( $ret as $k => $val )
        {
            $ret [$k] ['nickname'] = get_seller_nickname_by_user_id ($val ['user_id'] );
            $ret [$k] ['user_icon'] = get_seller_user_icon($val ['user_id'], 165 );

            $user_result = $seller_obj->get_seller_info($val ['user_id'], 2);
            $profile = $user_result['seller_data']['profile'][0];
            $ret [$k] ['comment_score'] = $profile['average_score'];
            $ret [$k] ['cover'] = $profile['cover'];

            $ret [$k] ['city_name'] = get_poco_location_name_by_location_id($profile ['location_id'] );

            $ret [$k] ['onsale_num'] = (int)$profile['onsale_num'];

            if($user_result['seller_data']['status']==2)
            {
                $ret [$k] ['is_close'] = 1;
            }
            else
            {
                $ret [$k] ['is_close'] = 0;
            }

        }

        return $ret;
    }

    /*
     * 获取用户关注的商品
     * @param bool $b_select_count 是否取总数
     * @param int $user_id
     * @param int $type_id 服务ID
     * @param int $order_by 排序 add_time为按更新时间，goods_last_update_time为按商品更新时间
     * @param string $limit
     */
    function get_follow_goods_by_user_id($b_select_count = false,$user_id,$type_id=0, $order_by='add_time', $limit = '0,10')
    {
        $goods_obj = POCO::singleton('pai_mall_goods_class');

        $user_id = ( int ) $user_id;
        $type_id = ( int ) $type_id;

        $where_str = "user_id={$user_id}";

        if($type_id)
        {
            $where_str .= " AND type_id={$type_id}";
        }

        if(!in_array($order_by,array('add_time','goods_last_update_time')))
        {
            $order_by = "add_time";
        }

        $ret = $this->get_goods_follow_list ( $b_select_count, $where_str, "{$order_by} DESC,id DESC", $limit, '*' );
        foreach ( $ret as $k => $val )
        {
            $goods_info = $goods_obj->get_goods_info($val['goods_id']);

            $ret[$k]['goods_name'] = $goods_info['goods_data']['titles'];
            $ret[$k]['image'] = $goods_info['goods_data']['images'];

            $count_price_arr = count($goods_info['goods_data']['prices_de']);
            if($count_price_arr>1)
            {
                $unit = " 起";
            }
            else
            {
                $unit = "";
            }
            $ret[$k]['prices'] = $goods_info['goods_data']['prices'];
            $ret[$k]['format'] = $goods_info['goods_prices_list'][0]['name_val'];
            $ret[$k]['prices_unit'] = $unit;

            $ret[$k]['average_score'] = $goods_info['goods_data']['average_score'];

            $ret[$k]['is_show'] = $goods_info['goods_data']['is_show'];

            if($goods_info['goods_data']['type_id']==42)
            {
                $activity_info = POCO::singleton('pai_mall_api_class')->get_goods_id_activity_info($val['goods_id']);
                $ret[$k]['f_time'] = $activity_info['f_time'];
                $ret[$k]['e_time'] = $activity_info['e_time'];
            }
            else
            {
                $ret[$k]['f_time'] = "";
                $ret[$k]['e_time'] = "";
            }


        }

        return $ret;
    }

	/*
	 * 根据用户ID获取粉丝人数
	 * @param int $user_id
	 * @param bool $b_select_count
	 * @param string $limit
	 */
	function get_fans_by_user_id($user_id, $b_select_count = false, $limit = '0,10')
	{

		$user_id = ( int ) $user_id;
		$where_str = "be_follow_user_id={$user_id}";

		if($b_select_count)
        {
			$ret = $this->get_user_follow_list ( $b_select_count, $where_str);

		}else{
			$ret = $this->get_user_follow_list ( $b_select_count, $where_str, 'add_time DESC', $limit, 'follow_user_id as user_id' );

			foreach ( $ret as $k => $val )
			{
				$ret [$k] ['nickname'] = get_user_nickname_by_user_id ($val ['user_id'] );
				$ret [$k] ['user_icon'] = get_user_icon ($val ['user_id'], 165 );

			}
		}

		return $ret;
	}

    /*
     * 更新商家最近上架时间
     * @param int $user_id
     */
    function update_seller_last_update_time($user_id)
    {
        $user_id = (int)$user_id;

        $this->set_follow_user_tbl();

        $update_arr['user_last_update_time'] = time();
        return $this->update($update_arr,"be_follow_user_id={$user_id}");
    }

    /*
     * 更新商家服务
     * @param int $user_id
     */
    function update_seller_type_id($user_id)
    {
        $user_id = (int)$user_id;

        $this->set_follow_user_tbl();


        $mall_seller_obj = POCO::singleton ( 'pai_mall_seller_class' );

        $type_id_str = $mall_seller_obj->get_first_profile_type_id_by_user_id($user_id);

        $update_arr['type_id_str'] = $type_id_str;
        return $this->update($update_arr,"be_follow_user_id={$user_id}");


    }

    /*
     * 更新商品最近上架时间
     * @param int $goods_id
     */
    function update_goods_last_update_time($goods_id)
    {
        $goods_id = (int)$goods_id;

        $this->set_follow_goods_tbl();

        $update_arr['goods_last_update_time'] = time();
        return $this->update($update_arr,"goods_id={$goods_id}");

    }

}

?>