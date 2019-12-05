<?php
/*
 * 专题操作类
 */



class pai_topic_class extends POCO_TDG
{

    var $promotion_city_conf = array(0=>"全国",101029001=>"广州",101001001=>"北京",101003001=>"上海",101022001=>"成都",101004001=>"重庆",101015001=>"西安");
    var $promotion_type_conf = array(3=>"化妆服务",5=>"摄影培训",12=>"影棚租赁",31=>"模特服务",40=>"摄影服务",41=>"美食服务",43=>"其他服务");
	
	/**
	 * 构造函数
	 *
	 */
	public function __construct()
	{
		$this->setServerId ( 101 );
		$this->setDBName ( 'pai_db' );
		$this->setTableName ( 'pai_topic_tbl' );
	}

    private function set_topic_tbl()
    {
        $this->setTableName ( 'pai_topic_tbl' );
    }

    private function set_topic_tpl_tbl()
    {
        $this->setTableName ( 'pai_topic_tpl_tbl' );
    }


    private function set_temp_enroll_tbl()
    {
        $this->setTableName ( 'pai_temp_enroll_tbl' );
    }

    private function set_promotion_enroll_tbl()
    {
        $this->setTableName ( 'pai_promotion_enroll_tbl' );
    }

    private function set_promotion_list_tbl()
    {
        $this->setTableName ( 'pai_promotion_topic_tbl' );
    }

    private function set_task_list_tbl()
    {
        $this->setTableName ( 'pai_task_topic_tbl' );
    }

    private function set_task_enroll_tbl()
    {
        $this->setTableName ( 'pai_task_enroll_tbl' );
    }
	
	/*
	 * 添加
	 * 
	 * @param array  $insert_data 
	 * 
	 * return bool 
	 */
	public function add_topic($insert_data)
	{
		$this->set_topic_tbl();
		if (empty ( $insert_data ))
		{
			return false;
		}
		
		return $this->insert ( $insert_data );
	
	}

    public function add_topic_tpl($insert_data)
    {
        $this->set_topic_tpl_tbl();
        if (empty ( $insert_data ))
        {
            return false;
        }

        return $this->insert ( $insert_data );

    }


    /*
     * 待运营专题报名
     */
    public function add_daiyunying_enroll($cellphone=0,$name='',$type_name='')
    {
        $this->set_temp_enroll_tbl();

        if(empty($cellphone) || empty($name) ||empty($type_name))
        {
            return false;
        }

        $insert_data['type'] = "daiyunying";
        $insert_data['phone'] = $cellphone;
        $insert_data['name'] = $name;
        $insert_data['type_name'] = $type_name;
        $insert_data['add_time'] = time();

        return $this->insert ( $insert_data );

    }


    public function get_temp_enroll_list($b_select_count = false, $where_str = '', $order_by = 'id DESC', $limit = '0,10', $fields = '*')
    {
        $this->set_temp_enroll_tbl();
        if ($b_select_count == true)
        {
            $ret = $this->findCount ( $where_str );
        } else
        {
            $ret = $this->findAll ( $where_str, $limit, $order_by, $fields );
        }
        return $ret;
    }

    public function del_topic_tpl($topic_id)
    {
        $topic_id = (int)$topic_id;

        $this->set_topic_tpl_tbl();

        return $this->delete("topic_id={$topic_id}");
    }
    
    public function del_topic($id)
    {
        $id = (int)$id;
        if($id)
        {
            $sql_str = "DELETE FROM pai_db.pai_topic_tbl WHERE id=$id";
            db_simple_getdata($sql_str, TURE, 101);
            return true;    
        }else{
            return false;
        }
        
    }
    
    public function update_effect($id, $state)
    {
        $id = (int)$id;
        if($id)
        {
            if(!$state) $state=0;
            $sql_str = "UPDATE pai_db.pai_topic_tbl SET is_effect = $state 
                        WHERE id=$id";
            db_simple_getdata($sql_str, TURE, 101);
            return true;
        }else{
            return false;
        }
    }
	
	/**
	 * 更新
	 *
	 * @param array $data
	 * @param int $id
	 * @return bool
	 */
	public function update_topic($data, $id)
	{
        $this->set_topic_tbl();
		if (empty($data)) 
		{
			throw new App_Exception(__CLASS__.'::'.__FUNCTION__.':数组不能为空');
		}
		$id = (int)$id;
		if (empty($id)) 
		{
			throw new App_Exception(__CLASS__.'::'.__FUNCTION__.':ID不能为空');
		}
		
		$where_str = "id = {$id}";
		return $this->update($data, $where_str);
	}
	
	/*
	 * 获取
	 * @param bool $b_select_count
	 * @param string $where_str 查询条件
	 * @param string $order_by 排序
	 * @param string $limit 
	 * @param string $fields 查询字段
	 * 
	 * return array
	 */
	public function get_topic_list($b_select_count = false, $where_str = '', $order_by = 'id DESC', $limit = '0,10', $fields = '*')
	{
        $this->set_topic_tbl();
		if ($b_select_count == true)
		{
			$ret = $this->findCount ( $where_str );
		} else
		{
			$ret = $this->findAll ( $where_str, $limit, $order_by, $fields );
		}
		return $ret;
	}
	
	public function get_topic_info($id)
	{
        $this->set_topic_tbl();
		$id = ( int ) $id;
		$ret = $this->find ( "id={$id}" );
		$ret['share_text'] = $this->get_share_text($ret);
		$rank_content = $this->get_topic_info_v2($ret);
		$ret['content_v2'] .= $rank_content;


        $tpl_arr = $this->get_topic_tpl_list($id);
        $tpl_arr = poco_iconv_arr($tpl_arr,'GBK', 'UTF-8');
        $ret['tpl_json'] = json_encode($tpl_arr);



		if($ret['is_button']==1)
		{
			include_once ('/disk/data/htdocs232/poco/pai/yue_admin/topic/config/topic_button.fuc.php');
            if($ret['version_type']=='old')
            {
                $enroll_button = yue_topic_enroll_button($id);
            }
            else
            {
                $enroll_button = mall_topic_enroll_button($id);
            }
 			$ret['content_v2'] .= $enroll_button;
		}
		return $ret;
	}

	public function get_topic_info_v2($ret)
	{
		global $tpl;
		global $my_app_pai;	
        $global_header_html = $my_app_pai->webControl('act_topic_style', $ret, true);
        unset($ret);
        return $global_header_html;
	}

    public function get_topic_sign_up_list($type = 'all')
    {
        $sql_str = "SELECT * FROM pai_db.pai_sign_up_tbl ";
        if($type != 'all')
        {
            $sql_str .= " WHERE user_type = '{$type}'";
        }
        
        $result = db_simple_getdata($sql_str, FALSE, 101);
        return $result;
    }
    
    public function add_topic_sign_up($user_name, $user_tel, $user_type)
    {
        $add_time = date('Y-m-d H:i:s');
        $sql_str = "INSERT IGNORE INTO pai_db.pai_sign_up_tbl(user_name, user_tel, user_type, add_time) 
                    VALUES (:x_user_name, :x_user_tel, :x_user_type, :x_add_time)";
        sqlSetParam($sql_str, 'x_user_name', $user_name);
        sqlSetParam($sql_str, 'x_user_tel', $user_tel);
        sqlSetParam($sql_str, 'x_user_type', $user_type);
        sqlSetParam($sql_str, 'x_add_time', $add_time);
        
        db_simple_getdata($sql_str, TRUE, 101);
        return db_simple_get_affected_rows();
    }
    
	public function get_share_text($topic_data)
	{
		$title = '【'.$topic_data['title'].'】 ︳约约';
		$content = '时间，就该浪费在美好的事情上。';
		$sina_content = '【'.$topic_data['title'].'】 ︳约约，时间，就该浪费在美好的事情上。';
		//$share_url = 'http://yp.yueus.com/mobile/app?from_app=1#topic/'.$topic_data['id'];
		$share_url = 'http://www.yueus.com/topic/'.$topic_data['id'];
		$share_url_v3 = 'http://www.yueus.com/topic_v3/'.$topic_data['id'];
		$share_img = $topic_data['cover_image'];
		
		$share_text['title'] = $title;
		$share_text['content'] = $content;
		$share_text['sina_content'] = $sina_content.' '.$share_url;
		$share_text['remark'] = '';
		$share_text['url'] = $share_url;
		$share_text['url_v3'] = $share_url_v3;
		$share_text['img'] = $share_img; 
		$share_text['user_id'] = '';
		$share_text['qrcodeurl'] = $share_url;
		
		return $share_text;
	}

    public function get_topic_tpl_list($topic_id)
    {


        $this->set_topic_tpl_tbl();

        $goods_obj = POCO::singleton ( 'pai_mall_goods_class' );

        include_once ('/disk/data/htdocs232/poco/pai/mall/user/api_rest.php');


        $topic_id = (int)$topic_id;
        $where_str = "topic_id={$topic_id}";
        $ret = $this->findAll ( $where_str, "0,1000", "sort asc,id asc", "*" );
        foreach($ret as $k=>$val)
        {
            unset($ret[$k]['custom_data']);
            if($val['tpl_type']=='goods_tpl1')
            {
                $custom_data = unserialize($val['custom_data']);
                foreach($custom_data as $ck=>$c_val)
                {
                    $goods_info = $goods_obj->get_goods_info($c_val['goods_id']);
                    $custom_data[$ck]['img_url'] =yueyue_resize_act_img_url($goods_info['goods_data']['images'], '260');
                    $custom_data[$ck]['price'] = "￥".$goods_info['goods_data']['prices'];
                    $custom_data[$ck]['title'] =$goods_info['goods_data']['titles'];
                    $custom_data[$ck]['score'] = $goods_info['goods_data']['average_score']*20;
                    $custom_data[$ck]['goods_url'] = mall_yueyue_app_to_http($c_val['goods_url']);
                }

                $ret[$k]['custom_data']['list'] = $custom_data;
            }
            elseif($val['tpl_type']=='goods_tpl2' || $val['tpl_type']=='goods_tpl3')
            {
                $custom_data = unserialize($val['custom_data']);
                $goods_info = $goods_obj->get_goods_info($custom_data['goods_id']);
                $ret[$k]['custom_data']['img_url'] = yueyue_resize_act_img_url($goods_info['goods_data']['images'], '260');
                $ret[$k]['custom_data']['price'] = "￥".$goods_info['goods_data']['prices'];
                $ret[$k]['custom_data']['goods_id'] = $custom_data['goods_id'];
                $ret[$k]['custom_data']['goods_text'] = $custom_data['goods_text'];
                $ret[$k]['custom_data']['goods_url'] = mall_yueyue_app_to_http($custom_data['goods_url']);
                $ret[$k]['custom_data']['title'] = $goods_info['goods_data']['titles'];
                $ret[$k]['custom_data']['score'] = $goods_info['goods_data']['average_score']*20;
            }
            elseif($val['tpl_type']=='list_tpl2')
            {
                $custom_data = unserialize($val['custom_data']);

                $ret[$k]['custom_data'] = $custom_data;
                $ret[$k]['custom_data']['button'] = mall_yueyue_app_to_http($custom_data['button']);
            }
            else
            {
                $ret[$k]['custom_data'] = unserialize($val['custom_data']);
            }
        }

        return $ret;
    }


    /*
     * 获取促销专题列表
     */
    public function get_promotion_list($b_select_count = false, $where_str = '', $order_by = 'id DESC', $limit = '0,10', $fields = '*')
    {
        $this->set_promotion_list_tbl();


        global $yue_login_id;
        //特殊ID所有都显示
        if(in_array($yue_login_id,array(100001,100004,128281,204887)))
        {
            $where_str = '';
        }

        if ($b_select_count == true)
        {
            $ret = $this->findCount ( $where_str );
        } else
        {
            $ret = $this->findAll ( $where_str, $limit, $order_by, $fields );
            foreach($ret as $k=>$val)
            {
                $ret[$k]['img'] = yueyue_resize_act_img_url($val['img'], '260');
                $ret[$k]['event_time'] = date("Y-m-d",$val['begin_time'])."到".date("Y-m-d",$val['end_time']);
                $ret[$k]['topic_id'] =$val['id'];
                $ret[$k]['is_enroll'] =$this->is_enroll_promotion($val['id']);

                $count_enroll=$this->count_promotion_enroll($val['id']);
                $ret[$k]['join_text'] = "已有{$count_enroll}人参加";

                $event_city_arr = explode(",",$val['event_city']);
                foreach($event_city_arr as $location_id)
                {
                    $city_text_arr[]=$this->promotion_city_conf[$location_id];
                }
                $ret[$k]['city_text'] = implode(",",$city_text_arr);

                unset($city_text_arr);


            }
        }
        return $ret;
    }


    public function add_promotion_topic($insert_data)
    {
        $this->set_promotion_list_tbl();

        if (empty ( $insert_data ))
        {
            return false;
        }

        return $this->insert ( $insert_data );
    }


    public function update_promotion_topic($data, $id)
    {
        $this->set_promotion_list_tbl();

        if (empty($data)) {
            return false;
        }

        $id = (int)$id;

        if (empty($id)) {
            return false;
        }

        $where_str = "id = {$id}";
        return $this->update($data, $where_str);
    }


    public function del_promotion_topic($id)
    {
        $this->set_promotion_list_tbl();

        $id = (int)$id;
        return $this->delete("id=$id");
    }

    /*
     * 获取促销详情
     */
    public function get_promotion_detail($topic_id)
    {
        $this->set_promotion_list_tbl();

        $topic_id = (int)$topic_id;
        $ret = $this->find ( "id={$topic_id}" );

        $count_enroll=$this->count_promotion_enroll($topic_id);

        $ret['topic_id'] = $topic_id;
        $ret['img'] = yueyue_resize_act_img_url($ret['img'], '260');
        $ret['join_text'] = "已有{$count_enroll}人参加";
        $ret['event_time'] = date("Y-m-d",$ret['begin_time'])."到".date("Y-m-d",$ret['end_time']);
        $ret['is_enroll'] = $this->is_enroll_promotion($topic_id);

        $event_city_arr = explode(",",$ret['event_city']);
        foreach($event_city_arr as $location_id)
        {
            $city_text_arr[]=$this->promotion_city_conf[$location_id];
        }
        $ret['city_text'] = implode(",",$city_text_arr);

        $type_arr = explode(",",$ret['join_type']);
        foreach($type_arr as $type_id)
        {
            $type_text_arr[]=$this->promotion_type_conf[$type_id];
        }
        $ret['type_text'] = implode(",",$type_text_arr);

        return $ret;
    }


    /*
     * 报名服务列表
     */
    public function get_enroll_service_list($topic_id,$user_id,$limit='0,10')
    {
        $topic_id = (int)$topic_id;
        $user_id = (int)$user_id;

        if(empty($user_id))
        {
            return array();
        }

        $promotion_detail = $this->get_promotion_detail($topic_id);

        $goods_obj = POCO::singleton ( 'pai_mall_goods_class' );

        //$data=array('user_id'=>$user_id,"is_show"=>1,"");
        //$ret = $goods_obj->user_search_goods_list($data,$limit = '0,100');
        //$ret = $goods_obj->search_goods_list_by_fulltext($data,$limit);

        if($promotion_detail["event_city"]!=0)
        {
            $promotion_detail["event_city"] = $promotion_detail["event_city"].",0";
        }

        //约美食 影棚
        $join_type = $promotion_detail["join_type"];
        $join_type_arr = explode(',',$join_type);
        foreach($join_type_arr as $type_id)
        {
            if(in_array($type_id,array(41,12)))
            {
                $location_type_id_arr[] = $type_id;
            }
            else
            {
                $not_location_type_id_arr[] = $type_id;
            }
        }



        if($location_type_id_arr)
            $promotion_search_arr['type_id'] = implode(',',$location_type_id_arr);

        $promotion_search_arr['location_id'] = $promotion_detail["event_city"];

        if($not_location_type_id_arr)
            $promotion_search_arr['not_location_type_id'] = implode(',',$not_location_type_id_arr);

        //$data=array("show"=>1,"promotion_search"=>$promotion_search_arr);
        $data=array("show"=>1);
        $ret = $goods_obj->user_goods_list($user_id,$data, false,  'goods_id DESC', $limit);



        //参加地区判断
/*        if(!preg_match("/全国/",$promotion_detail['event_city']))
        {
            $mall_seller_obj = POCO::singleton('pai_mall_seller_class');
            $seller_info = $mall_seller_obj->get_seller_info($user_id,2);
            $location_id =$seller_info['seller_data']['location_id'];
            $location_arr = get_poco_location_name_by_location_id ( $location_id,true,true );
            $city = str_replace("市","",$location_arr['level_1']['name']);

            if(!preg_match("/{$city}/",$promotion_detail['event_city']))
            {
                $result['result'] = -2;
                $result['message'] = '该活动只限'.$promotion_detail['event_city']."地区";
                return $result;
            }
        }*/


        $type_obj = POCO::singleton('pai_mall_goods_type_attribute_class');
        $type_name_list = $type_obj -> get_type_attribute_cate(0);
        foreach($type_name_list as $val)
        {
            $type_name[$val['id']] = $val;
        }

        $type_name_array = $this->promotion_type_conf;


        $promotion_enroll_arr = $this->promotion_enroll_arr($topic_id,$user_id);

        foreach($ret as $key=>$value)
        {
            $prices_list_de = unserialize($value['prices_list']);

            $new_ret[$key]['title'] = "[".$type_name_array[$value['type_id']]."] ".$value['titles'];
            $new_ret[$key]['images'] = $value['images'];
            $new_ret[$key]['goods_id'] = $value['goods_id'];

            if($prices_list_de)
            {
                if($value['type_id']==41)
                {
                    $food_type_name = $goods_obj->get_goods_prices_list($value['goods_id']);
                }

                $i=0;
                foreach($prices_list_de as $key_de => $val_de)
                {
                    $food_type_tmp_name = array();
                    if($val_de>0)
                    {
                        if($value['type_id']==41)
                        {
                            //美食服务特殊处理规格
                            $food_type_tmp_name = explode("|@|",$food_type_name[$i]['name']);


                            $price_text = $food_type_tmp_name[0]."￥".$food_type_name[$i]['prices'];

                        }
                        else
                        {

                            $price_text = $type_name[$key_de]['name']."￥".$val_de;
                        }

                        $is_select = $promotion_enroll_arr[$topic_id][$value['goods_id']][$key_de]['is_select'];
                        if(!$is_select) $is_select=0;

                        $num = $promotion_enroll_arr[$topic_id][$value['goods_id']][$key_de]['num'];
                        if(!$num) $num=1;

                        $price_group_arr['price_text'] = $price_text;
                        $price_group_arr['is_select'] = $is_select;
                        $price_group_arr['num'] = $num;
                        $price_group_arr['type_key'] = $key_de;

                        $new_ret[$key]['price_arr'][] = $price_group_arr;

                        unset($price_group_arr);
                    }

                    $i++;
                }

            }
            else
            {
                $is_select = $promotion_enroll_arr[$topic_id][$value['goods_id']][0]['is_select'];
                if(!$is_select) $is_select=0;

                $num = $promotion_enroll_arr[$topic_id][$value['goods_id']][0]['num'];
                if(!$num) $num=1;

                $price_group_arr['price_text'] = '￥'.$value['prices'];
                $price_group_arr['is_select'] = $is_select;
                $price_group_arr['num'] = $num;
                $price_group_arr['type_key'] = 0;

                $new_ret[$key]['price_arr'][] = $price_group_arr;

                unset($price_group_arr);
            }
        }

        return $new_ret;
    }

    /*
     * 促销报名
     */
    public function add_promotion_enroll($topic_id,$user_id,$goods_id,$type_key,$num,$type_text,$type)
    {
        $this->set_promotion_enroll_tbl();

        $topic_id = (int)$topic_id;
        $user_id = (int)$user_id;
        $goods_id = (int)$goods_id;
        $type_key = (int)$type_key;
        $num = (int)$num;

        if($num==0 || $type=='delete')
        {
            $this->delete_promotion_enroll($topic_id,$user_id,$goods_id,$type_key);
            $result['result'] = 1;
            $result['message'] = "删除成功";
            return $result;
        }

        if(empty($user_id) || empty($goods_id) || empty($topic_id))
        {
            $result['result'] = -1;
            $result['message'] = '参数错误';
            return $result;
        }


        //$promotion_detail = $this->get_promotion_detail($topic_id);

        //参加地区判断
/*        if(!preg_match("/全国/",$promotion_detail['event_city']))
        {
            $mall_seller_obj = POCO::singleton('pai_mall_seller_class');
            $seller_info = $mall_seller_obj->get_seller_info($user_id,2);
            $location_id =$seller_info['seller_data']['location_id'];
            $location_arr = get_poco_location_name_by_location_id ( $location_id,true,true );
            $city = str_replace("市","",$location_arr['level_1']['name']);

            if(!preg_match("/{$city}/",$promotion_detail['event_city']))
            {
                $result['result'] = -2;
                $result['message'] = '该活动只限'.$promotion_detail['event_city']."地区";
                return $result;
            }
        }*/

        //判断品类
/*        $type_name_array = array(3=>"化妆服务",5=>"摄影培训",12=>"影棚租赁",31=>"模特服务",40=>"摄影服务",41=>"美食服务",43=>"其他服务");

        $join_type_arr = explode(",",$promotion_detail['join_type']);
        foreach($join_type_arr as $val)
        {
            $join_type[$val]=1;
            $type_name_arr[] = $type_name_array[$val];
        }
        $type_name_str = implode(",",$type_name_arr);

        $goods_obj = POCO::singleton('pai_mall_goods_class');

        $goods_ret=$goods_obj->get_goods_info($goods_id);
        $type_id = $goods_ret['goods_data']['type_id'];
        if(!$join_type[$type_id])
        {
            $result['result'] = -2;
            $result['message'] = '该活动只限'.$type_name_str."品类";
            return $result;
        }*/


        $goods_obj = POCO::singleton('pai_mall_goods_class');

        $goods_ret=$goods_obj->get_goods_info($goods_id);
        $type_id = $goods_ret['goods_data']['type_id'];

        $data['topic_id'] = $topic_id;
        $data['user_id'] = $user_id;
        $data['goods_id'] = $goods_id;
        $data['type_id'] = $type_id;
        $data['type_key'] = $type_key;
        $data['type_text'] = $type_text;
        $data['num'] = $num;
        $data['add_time'] = time();
        $this->insert($data ,"REPLACE");

        $result['result'] = 1;
        $result['message'] = "添加成功";
        return $result;

    }


    /*
     * 报名删除
     */
    public function delete_promotion_enroll($topic_id,$user_id,$goods_id,$type_key)
    {
        $this->set_promotion_enroll_tbl();

        $topic_id = (int)$topic_id;
        $user_id = (int)$user_id;
        $goods_id = (int)$goods_id;
        $type_key = (int)$type_key;

        $where ="topic_id={$topic_id} and user_id={$user_id} and goods_id={$goods_id} and type_key={$type_key}";
        $this->delete($where);

        $result['result'] = 1;
        $result['message'] = "删除成功";
        return $result;
    }


    /*
     * 删除报名
     */
    public function delete_promotion_user_enroll($topic_id,$user_id)
    {
        $this->set_promotion_enroll_tbl();

        $topic_id = (int)$topic_id;
        $user_id = (int)$user_id;

        if(empty($topic_id) || empty($user_id))
        {
            $result['result'] = -1;
            $result['message'] = "参数错误";
            return $result;
        }

        $where ="topic_id={$topic_id} and user_id={$user_id}";
        $this->delete($where);

        $result['result'] = 1;
        $result['message'] = "取消报名成功";
        return $result;
    }


    public function promotion_enroll_arr($topic_id,$user_id)
    {
        $topic_id = (int)$topic_id;
        $user_id = (int)$user_id;

        $this->set_promotion_enroll_tbl();

        $where = "topic_id={$topic_id} and user_id={$user_id}";
        $ret = $this->findAll($where);

        if(!$ret) return array();


        foreach($ret as $val)
        {
            $enroll_arr[$topic_id][$val['goods_id']][$val['type_key']]['num'] = $val['num'];
            $enroll_arr[$topic_id][$val['goods_id']][$val['type_key']]['is_select'] = 1;
        }


        return $enroll_arr;
    }

    /*
     * 是否参加过促销
     */
    public function is_enroll_promotion($topic_id)
    {

        $this->set_promotion_enroll_tbl();

        global $yue_login_id;

        $topic_id = (int)$topic_id;
        $yue_login_id = (int)$yue_login_id;

        $where = "topic_id={$topic_id} and user_id={$yue_login_id}";
        $ret=$this->find($where);

        if($ret)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /*
     * 统计促销报名人数
     */
    public function count_promotion_enroll($topic_id)
    {
        $this->set_promotion_enroll_tbl();

        $topic_id = (int)$topic_id;
        $where_str = "topic_id={$topic_id}";
        return $this->findCount ( $where_str );

    }


    /*
     * 促销报名列表
     */
    public function get_promotion_enroll_list($b_select_count = false, $where_str = '', $order_by = 'id DESC', $limit = '0,10', $fields = '*')
    {
        $this->set_promotion_enroll_tbl();

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
     * 接单中心列表
     */
    public function get_task_list($b_select_count = false, $where_str = '', $order_by = 'id DESC', $limit = '0,10', $fields = '*')
    {
        $this->set_task_list_tbl();
        if ($b_select_count == true)
        {
            $ret = $this->findCount ( $where_str );
        } else
        {
            $ret = $this->findAll ( $where_str, $limit, $order_by, $fields );

            foreach($ret as $k=>$val)
            {
                $ret[$k]['img'] = yueyue_resize_act_img_url($val['img'], '260');

                $count_enroll = $this->count_task_enroll($val['id']);

                $is_enroll = $this->is_task_enroll($val['id']);
                $ret[$k]['is_enroll'] = (int)$is_enroll;
                $ret[$k]['enroll_text'] = "已有{$count_enroll}人参加";
            }
        }
        return $ret;
    }



    /*
     * 接单中心详情
     */
    public function get_task_detail($topic_id)
    {
        $this->set_task_list_tbl();

        $topic_id = (int)$topic_id;

        $ret = $this->find ( "id={$topic_id}" );

        $is_enroll = $this->is_task_enroll($topic_id);
        $ret['is_enroll'] = (int)$is_enroll;


        $count_enroll = $this->count_task_enroll($topic_id);
        $ret['enroll_text'] = "已有{$count_enroll}人参加";

        return $ret;
    }


    public function update_task_topic($data, $id)
    {
        $this->set_task_list_tbl();

        if (empty($data)) {
            return false;
        }

        $id = (int)$id;

        if (empty($id)) {
            return false;
        }

        $where_str = "id = {$id}";
        return $this->update($data, $where_str);
    }

    public function add_task_topic($insert_data)
    {
        $this->set_task_list_tbl();

        if (empty ( $insert_data ))
        {
            return false;
        }



        return $this->insert ( $insert_data );
    }


    /*
     * 接单报名
     */
    public function add_task_enroll($topic_id,$user_id,$remark='')
    {
        $this->set_task_enroll_tbl();

        $topic_id = (int)$topic_id;
        $user_id = (int)$user_id;

        if (empty ( $topic_id ) || empty ( $user_id ))
        {
            $result['result'] = -1;
            $result['message'] = "参数错误";
            return $result;
        }

        $type_array = array(md5("化妆服务")=>3,md5("摄影培训")=>5,md5("影棚租赁")=>12,md5("模特服务")=>31,md5("摄影服务")=>40);

        $task_info = $this->get_task_detail($topic_id);
        $task_type = $task_info['type'];
        $type_id = $type_array[md5($task_type)];

        $obj = POCO::singleton('pai_mall_seller_class');
        $seller_info = $obj->get_seller_info($user_id,2);
        $seller_type_id = $seller_info['seller_data']['profile'][0]['type_id'];
        $seller_type_id_arr = explode(',',$seller_type_id);

        foreach($seller_type_id_arr as $val_type_id)
        {
            if($val_type_id==$type_id)
            {
                $is_match = 1;
                break;
            }
        }

        if(!$is_match)
        {
            $result['result'] = -2;
            $result['message'] = "对不起，你暂未认证该品类，不能报名参加";
            return $result;
        }

        if($this->is_task_enroll($topic_id))
        {
            $result['result'] = -2;
            $result['message'] = "你已报名过了";
            return $result;
        }

        $insert_data['topic_id'] = $topic_id;
        $insert_data['user_id'] = $user_id;
        $insert_data['remark'] = $remark;
        $insert_data['add_time'] = time();

        $this->insert ( $insert_data );

        $result['result'] = 1;
        $result['message'] = "恭喜您报名成功，如果客户选中您，将会通过约约客服信息通知";
        return $result;

    }

    public function delete_task_enroll($topic_id,$user_id)
    {
        $this->set_task_enroll_tbl();

        $topic_id = (int)$topic_id;
        $user_id = (int)$user_id;

        if (empty ( $topic_id ) || empty ( $user_id ))
        {
            $result['result'] = -1;
            $result['message'] = "参数错误";
            return $result;
        }

        $this->delete("topic_id={$topic_id} and user_id={$user_id}");

        $result['result'] = 1;
        $result['message'] = "取消报名成功";
        return $result;
    }

    /*
     * 接单是否已报名
     */
    public function is_task_enroll($topic_id)
    {
        $this->set_task_enroll_tbl();
        $topic_id = (int)$topic_id;

        global $yue_login_id;
        $yue_login_id = (int)$yue_login_id;

        $ret = $this->find ( "topic_id={$topic_id} and user_id={$yue_login_id}" );

        if($ret)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /*
     * 接单报名列表
     */
    public function get_task_enroll_list($b_select_count = false, $where_str = '', $order_by = 'id DESC', $limit = '0,10', $fields = '*')
    {
        $this->set_task_enroll_tbl();

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
     * 统计接单人数
     */
    public function count_task_enroll($topic_id)
    {
        $this->set_task_enroll_tbl();

        $topic_id = (int)$topic_id;
        $where_str = "topic_id={$topic_id}";
        return $this->findCount ( $where_str );

    }
}

?>