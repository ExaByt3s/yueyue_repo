<?php
/**
 * 活动报名类
 *
 * @author tom
 * @copyright 2010-12-31
 */



class event_enroll_class extends POCO_TDG
{
    /**
     * 最后一次错误提示
     * @var string
     */
    protected $_last_err_msg = null;
    
    /**
     * 构造函数
     *
     */
    public function __construct()
    {
        //$this->setServerId(false);
        //$this->setDBName('event_db');
        //$this->setTableName('event_enroll_tbl');
    }
    
    /**
     * 设置错误提示
     * @param string $msg
     */
    protected function set_err_msg($msg)
    {
        $this->_last_err_msg = $msg;
    }
    
    /**
     * 获取错误提示
     */
    public function get_err_msg()
    {
        return $this->_last_err_msg;
    }

    /**
     * 获取数据
     *
     * @param int $enroll_id
     * @return array
     */
    public function get_enroll_by_enroll_id($enroll_id)
    {
        $param[] = $enroll_id;
        $ret = curl_event_data('event_enroll_class','get_enroll_by_enroll_id',$param);
        return $ret;
    }
    /**
     * 获取数据
     *
     * @param int $event_id
     * @param int $user_id
     * @return array
     */
    public function get_enroll_by_event_id_and_user_id($event_id,$user_id)
    {
        $param[] = $event_id;
        $param[] = $user_id;
        $ret = curl_event_data('event_enroll_class','get_enroll_by_event_id_and_user_id',$param);
        return $ret;
    }	
    /**
     * 取列表
     *
     * @param string $where_str    查询条件
     * @param bool $b_select_count 是否返回总数：TRUE返回总数 FALSE返回列表
     * @param string $limit        查询条数
     * @param string $order_by     排序条件
     * @return array|int
     */
    public function get_enroll_list($where_str = '', $b_select_count = false, $limit = '0,10', $order_by = 'enroll_id DESC',$fields="*")
    {
        $param[] = $where_str;
        $param[] = $b_select_count;
        $param[] = $limit;
        $param[] = $order_by;
        $param[] = $fields;
        $ret = curl_event_data('event_enroll_class','get_enroll_list',$param);
        return $ret;
    }	
    
    /**
     * 写入数据
     *
     * @param array $data 		活动信息数据
     * 				array(
     * 				'user_id'=>0			报名用户id
     * 				'enroll_location_id'=>0	报名用户所在地区
     * 				'event_id'=>0			活动ID	  				
     * 				'enroll_time'=>0		报名时间	  
     * 				'phone'=>''				报名用户电话，可为空	  
     * 				'enroll_num'=>1			报名人数 				
     * 				'enroll_ip'=>0			报名用户IP	  
     * 				'email'	=>''			报名用户EMAIL，可为空	  
     * 				'remark'=>''			备注，可为空	
     * 				)
     * @return enroll_id
     */
    public function add_enroll($data)
    {
        $param[] = $data;
        $ret = curl_event_data('event_enroll_class','add_enroll',$param);
        return $ret;
    }

    /**
     * 新活动报名接口 20140731
     * 
     * @param array $data 		活动信息数据
     * 	array(
     * 	'user_id'=>0			报名用户id

     
     * 	'event_id'=>0			活动ID	  					  
     * 	'phone'=>''				报名用户电话，可为空	  
     * 	'enroll_num'=>1			报名人数  					  
     * 	'email'	=>''			报名用户EMAIL，可为空	  
     * 	'remark'=>''			备注，可为空
     *  'enroll_ip'=>'',		IP地址  外面可以使用ip2long($_INPUT['IP_ADDRESS'])
     * 	'table_id'=>''		 	场次自增ID 
     * 	)
     * @return int enroll_id 报名ID   如遇到错误  -1 活动已过期 -2该用户已经报名 -3用户为组织者不能报名 -4活动开始中或已结束 -5 场次已关闭
     */
    public function add_enroll_v2($event_data){

        $enroll_obj     		  = POCO::singleton('event_enroll_class');
        $details_obj    		  = POCO::singleton('event_details_class');
        $table_obj                = POCO::singleton('event_table_class');
        $event_data['event_id']   = (int)$event_data['event_id'];
        $event_data['user_id']    = (int)$event_data['user_id'];
        $event_info               = $details_obj->get_event_by_event_id($event_data['event_id']);
        if ( empty($event_data['user_id']) || empty($event_data['event_id']) ) 
        {
            $this->set_err_msg('参数不正确');
            return false;
        }
        /*判断活动是否已经开始或结束*/
        if( G_POCO_EVENT_PAY == 1  ){
            //支付版
            if( $event_info['event_status'] != 0 ){
                //0 正在进行  1已确定 2已结束  3已取消
                return -4;

            }

        }
        else{

            if( $event_info['status'] != 0 ){

                return -4;                
                
            }

        }
        /*判断活动是否已经开始de或结束*/
        $is_over          = $details_obj->check_date_is_over($event_data['event_id']);  //检查活动是否过期
        if( $is_over )
            return -1;
        $is_duplicate 	  = $this->check_duplicate($event_data['user_id'],$event_data['event_id'], 'all',$event_data['table_id']);	//检查是否已经报名
        if( $is_duplicate )
            return -2;
        
        if($event_info['user_id'] == $event_data['user_id'])
            return -3;
        $can_enroll = $table_obj->check_event_table_enroll($event_data['table_id']);
        if( !$can_enroll ){
            //场次已关闭
            return -5;

        }

        $user_info 							= POCO::execute(array('member.get_user_info_by_user_id'), array($event_data['user_id']));
        $event_data['enroll_location_id'] 	= $user_info['location_id'];
        $event_data['enroll_time'] 			= time();
        //这里默认全部待付款，当支付完毕后，根据支付时间再进行正选与候补设定  报名状态，默认0成功，1候补，2放飞机 3待付款  
        $enroll_id = $enroll_obj->add_enroll($event_data);
        return $enroll_id;

    }

    /**
     * 新活动报名接口 20140912
     * @param enroll_data 报名数据   
     * array(
     *  'user_id'=>'',  用户ID  [非空]
     *  'event_id'=>,   活动ID  [非空]
     *  'phone'=>'',    手机号码
     *  'email'=>'',    邮箱
     *  'remark'=>      备注
     * )
     * @param array $sequence_data 活动场次 二维数组
     *  array(                         
     *      'enroll_num'=>''        报名人数                      
     *      'table_id'=>''          场次自增ID 
     *      'coupon_sn'=>''         优惠码
     *  )
     * @return array enroll_id_arr 
     * 错误值 
     * -1 参数错误
     * -2 该活动不存在 
     * -3 活动已处于开始或结束的状态 
     * -4 参与者为组织者  禁参与
     * -5 参与人数非法
     * -6 某一场已经报过名
     * -7 某一个场次已关闭 不允许再报名
     * -8 报名产生错误  报名失败
     * -9
     * -10优惠券使用失败
     * 
     */
    public function add_enroll_v3($enroll_data,$sequence_data){

        $enroll_data['user_id'] = intval($enroll_data['user_id']);
        if ( empty($enroll_data['user_id']) )
        {
            $ret = array( 'status_code'=>-1,'message'=>'user_id 参数不正确' );
            return $ret;
        }
        $enroll_data['event_id'] = intval($enroll_data['event_id']);
        if ( empty($enroll_data['event_id']) )
        {
            $ret = array( 'status_code'=>-1,'message'=>'event_id 参数不正确' );
            return $ret;
        }
        if ( empty($sequence_data) )
        {
            $ret = array( 'status_code'=>-1,'message'=>'sequence_data 参数不正确' );
            return $ret;
        }
        $enroll_obj    = POCO::singleton('event_enroll_class');
        $details_obj   = POCO::singleton('event_details_class');
        $table_obj     = POCO::singleton('event_table_class');
        $event_info    = $details_obj->get_event_by_event_id($enroll_data['event_id']);
        if( empty($event_info) ){

            $ret = array( 'status_code'=>-2,'message'=>'该活动不存在' );
            return $ret;

        }
        if($event_info['event_status'] != 0){

            $ret = array( 'status_code'=>-3,'message'=>'活动已处于开始或结束的状态  不可以再报名' );
            return $ret;

        }

        if($event_info['new_version'] != 2){

            $ret = array( 'status_code'=>-15,'message'=>'该活动不是支付版活动' );
            return $ret;

        }
        if($event_info['user_id'] == $enroll_data['user_id'] ){

            $ret = array( 'status_code'=>-4,'message'=>'不能报名自己发布的活动' );
            return $ret;

        }
        foreach( $sequence_data as $k=>$v ){

            $enroll_num = intval($v['enroll_num']);
            if( empty( $enroll_num ) ){

                $ret = array( 'status_code'=>-5,'message'=>'场次人数不能为空' );
                return $ret;

            }
            $can_enroll = $table_obj->check_event_table_enroll($v['table_id']);
            if( !$can_enroll ){

                $ret = array( 'status_code'=>-7,'message'=>'某一个场次已关闭 不允许再报名' );
                return $ret;

            }

        }

        include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');
        $coupon_obj = POCO::singleton('pai_coupon_class');

        $join_mode_auth = $enroll_obj->check_join_mode_auth( $enroll_data['event_id'],$enroll_data['user_id'] );
        $ip = ip2long($_INPUT['IP_ADDRESS']);
        $user_info            = curl_event_data('event_api_class','get_poco_excute',array("member.get_user_info_by_user_id",array($enroll_data['user_id'])));
        $enroll_location_id   = $user_info['location_id'];

        foreach( $sequence_data as $k=>$v ){

            $enroll_info['user_id']               = $enroll_data['user_id'];    //用户的POCOID　
            $enroll_info['event_id']              = $enroll_data['event_id'];   //活动ID
            $enroll_info['phone']                 = $enroll_data['phone'];
            $enroll_info['email']                 = $enroll_data['email'];
            $enroll_info['remark']                = $enroll_data['remark'];
            $enroll_info['enroll_ip']             = $ip;
            $enroll_info['enroll_location_id']    = $enroll_location_id;
            $enroll_info['enroll_time']           = time();
            $enroll_info['enroll_num']            = $v['enroll_num'];
            $enroll_info['table_id']              = $v['table_id'];
            $enroll_info['original_price']        = $event_info['budget']*$v['enroll_num']; //计算总金额
            $enroll_info['source']                = $enroll_data['source'];

            if( $join_mode_auth ){

                  $enroll_info['status'] = 3;

             }
             else{
                //没有join_mode（参与方式） 权限  需要组织者进行审批
                $enroll_info['status']     = 3;
                $enroll_info['is_accept']  = 0;
                $session            = $key + 1;
                $enroll_user        = curl_event_data('event_api_class','get_poco_excute',array("member.get_user_info_by_user_id",array($enroll_data['user_id'])));
                $msg                = "{$enroll_user} 申请参与你的活动 \"{$event_info['title']}\" 第{$session}场  请审核";
                POCO::execute(array('pm.add_new_notify_msg'), array($event_info['user_id'],'活动通知',$msg,$notify_ext_par));

             }
             $is_duplicate     = $this->check_duplicate($enroll_info['user_id'],$enroll_info['event_id'], 'all',$enroll_info['table_id']);
             if( $is_duplicate ){
                //如果已报名过  则更新旧的资料   防止用户跳转到支付宝后，浏览器后退回来再次报名并支付
                 $where_str  = "user_id = {$enroll_info['user_id']} AND event_id = {$enroll_info['event_id']} AND table_id = {$enroll_info['table_id']}";
                 $row        = curl_event_data('event_enroll_class','find_api',array($where_str));
                if( !empty($row) && $row['pay_status'] == 0 ) {
                    //防止已付款 然后回来再次报名
                    $enroll_id_arr[] = $row['enroll_id'];

                    //处理优惠券
                    $channel_module = 'waipai';
                    $channel_oid = $row['enroll_id'];
                    $module_type = 'waipai';

                    //不使用
                    $coupon_obj->not_use_coupon_by_oid($channel_module, $channel_oid);

                    //使用优惠券
                    if( !empty($v['coupon_sn']) )
                    {
                    	$relate_yue_id = get_relate_yue_id($enroll_info['user_id']);
                    	$event_user_id = get_relate_yue_id($event_info['user_id']);
                    	$param_info = array(
                    		'module_type' => $module_type, //模块类型 waipai yuepai topic
                    		'order_total_amount' => $enroll_info['original_price'], //订单总金额
                    		'model_user_id' => 0, //模特用户ID（约拍、专题）
                    		'org_user_id' => $event_info['org_user_id'], //机构用户ID
                    		'location_id' => $event_info['location_id']*1, //地区ID
                    		'event_id' => $enroll_info['event_id'], //活动ID
                    		'event_user_id' => $event_user_id, //活动组织者用户ID
                    		'seller_user_id' => $event_user_id, //商家用户ID，兼容商城系统
                    	);
                    	$use_ret = $coupon_obj->use_coupon($relate_yue_id, 1, $v['coupon_sn'], $channel_module, $channel_oid, $param_info);
                    	if( $use_ret['result']!=1 )
                    	{
                    		$ret = array( 'status_code'=>-10,'message'=>$use_ret['message'] );
                    		return $ret;
                    	}
                    	$enroll_info['discount_price'] = $use_ret['used_amount'];
                    	$enroll_info['is_use_coupon'] = 1;
                    }
                    else
                    {
                    	//上次可能有使用优惠券，所以这次置零
                    	$enroll_info['discount_price'] = 0;
                    	$enroll_info['is_use_coupon'] = 0;
                    }

                    $this->update_enroll($enroll_info,$row['enroll_id']);

                }
                else{


                    $ret = array( 'status_code'=>-9,'message'=>'报名产生错误  该报名已支付' );
                    return $ret;

                }

             }
             else{

                $enroll_id  = $enroll_obj->add_enroll($enroll_info);
                !empty($enroll_id)&&$enroll_id_arr[]=$enroll_id;
                if(empty($enroll_id_arr)){

                    $ret = array( 'status_code'=>-8,'message'=>'报名产生错误  报名失败' );
                    return $ret;

                 }

                 //处理优惠券
                 $channel_module = 'waipai';
                 $channel_oid = $enroll_id;
                 $module_type = 'waipai';

                 //使用优惠券，更新报名的优惠金额
                 if( !empty($v['coupon_sn']) )
                 {
                 	$relate_yue_id = get_relate_yue_id($enroll_info['user_id']);
                 	$event_user_id = get_relate_yue_id($event_info['user_id']);
                 	$param_info = array(
                 		'module_type' => $module_type, //模块类型 waipai yuepai topic
                 		'order_total_amount' => $enroll_info['original_price'], //订单总金额
                 		'model_user_id' => 0, //模特用户ID（约拍、专题）
                 		'org_user_id' => $event_info['org_user_id'], //机构用户ID
                 		'location_id' => $event_info['location_id']*1, //地区ID
                 		'event_id' => $enroll_info['event_id'], //活动ID
                 		'event_user_id' => $event_user_id, //活动组织者用户ID
                 		'seller_user_id' => $event_user_id, //商家用户ID，兼容商城系统
                 	);
                 	$use_ret = $coupon_obj->use_coupon($relate_yue_id, 1, $v['coupon_sn'], $channel_module, $channel_oid, $param_info);
                 	if( $use_ret['result']!=1 )
                 	{
                 		$ret = array( 'status_code'=>-10,'message'=>$use_ret['message'] );
                 		return $ret;
                 	}
                 	$enroll_data = array(
                 		'discount_price' => $use_ret['used_amount'],
                 		'is_use_coupon' => 1,
                 	);
                 	$this->update_enroll($enroll_data, $enroll_id);
                 }

             }

        }
        $ret = array( 'status_code'=>1,'message'=>'报名成功','enroll_id_arr'=>$enroll_id_arr,'join_mode_auth'=>$join_mode_auth );
        return $ret;

    }
    /**
     * 更新数据
     *
     * @param array $data
     * @param int $enroll_id
     * @return bool
     */
    public function update_enroll($data, $enroll_id)
    {
        $param[] = $data;
        $param[] = $enroll_id;
        $ret = curl_event_data('event_enroll_class','update_enroll',$param);
        return $ret;
        
    }
    
    /*
     * 根据用户ID更新数据
     */
    public function update_enroll_by_user_id($data,$user_id)
    {
        $param[] = $data;
        $param[] = $user_id;
        $ret = curl_event_data('event_enroll_class','update_enroll_by_user_id',$param);
        return $ret;
    }
    
    /**
     * 删除数据
     *
     * @param int $enroll_id
     * @return bool
     */
    public function del_enroll($enroll_id)
    {
        $enroll_id = (int)$enroll_id;
        if( $enroll_id < 1 )
        {
            $this->set_err_msg('enroll_id不正确');
            return false;
        }
        $param[] = $enroll_id;
        $ret = curl_event_data('event_enroll_class','del_enroll',$param);
        return $ret;
    }


    /**
     * 更新报名状态
     *
     * @param int $enroll_id
     * @param int $value	0表示成功，1表示候补，2表示放飞机
     * @return bool
     */
    public function update_enroll_status($enroll_id, $value=0)
    {
        $param[] = $enroll_id;
        $param[] = $value;
        $ret = curl_event_data('event_enroll_class','update_enroll_status',$param);
        return $ret;
        
    }	
    
    /**
     * 检查用户在同一个活动中是否已经报名
     *
     * @param int $user_id
     * @param int $event_id
     * @param string $status
     * @return bool
     */
    public function check_duplicate($user_id,$event_id,$status="all", $table_id=0)
    {

        $param[] = $user_id;
        $param[] = $event_id;
        $param[] = $status;
        $param[] = $table_id;
        $ret = curl_event_data('event_enroll_class','check_duplicate',$param);
        return $ret;
    }	
    
     /**
     * 单用户的报名列表
     *
     * @param int $user_id
     * @param string $status	报名状态，默认全部，0表示报名成功，1表示报名申请中，2表示报名不通过
     * @param bool $b_select_count
     * @param string $limit
     * @param string $order_by
     * @return array
     */

    public function get_enroll_list_by_user_id($user_id, $status = '', $b_select_count = false, $limit = "0,10", $order_by = "enroll_id DESC")
    {
        $param[] = $user_id;
        $param[] = $status;
        $param[] = $b_select_count;
        $param[] = $limit;
        $param[] = $order_by;
        $ret = curl_event_data('event_enroll_class','get_enroll_list_by_user_id',$param);
        return $ret;
    }

     /**
     * 用户的某场活动报名列表
     *
     * @param int    $user_id
     * @param string $event_id
     * @param bool   $b_select_count
     * @param string $limit
     * @param string $order_by
     * @return array
     */

    public function get_event_enroll_list($user_id,$event_id,$b_select_count = false, $limit = "0,10", $order_by = "enroll_id DESC"){

        $event_id = (int)$event_id;
        $user_id = (int)$user_id;
        if( $event_id<1 || $user_id<1)
        {
            $this->set_err_msg('参数不正确');
            return false;
        }

		if($user_id)  $where_str = "user_id = {$user_id}"; 
		if($event_id) $where_str .= " AND event_id = {$event_id}";

        $rows = $this->get_enroll_list($where_str,$b_select_count,$limit,$order_by);
        return $rows;

    }

    /**
     * 单用户的报名列表
     *
     * @param int $user_id
     * @param string $status    报名状态，默认全部，0表示正选  1表示候补 3表示待付款
     * @param bool $b_select_count
     * @param string $limit
     * @param string $order_by
     * @return array
     */

    public function get_enroll_list_by_user_id_v2($user_id, $status=0, $b_select_count = false, $limit = "0,10", $order_by = "enroll_id DESC")
    {
        $user_id = (int)$user_id;
        if( $user_id < 1 )
        {
            $this->set_err_msg('user_id不正确');
            return false;
        }
        $where_str     = "user_id = {$user_id}";
        if( !is_array($status) )
            $status    = array($status);
        $status        = array_map('intval',$status);
        $status_gather = implode( ',',$status);
        $where_str    .= " AND status IN({$status_gather})";
        $rows          = $this->get_enroll_list($where_str,$b_select_count,$limit,$order_by);
        return $rows;

    }    	
     /**
     * 单活动的报名列表
     *
     * @param int $event_id
     * @param string $status		报名状态，默认全部，0表示报名成功，1表示报名申请中/替补，2表示放飞机/不通过，3表示正式+候补
     * @param bool $b_select_count
     * @param string $limit
     * @param string $order_by
     * @return array
     */

    public function get_enroll_list_by_event_id($event_id, $status = '', $b_select_count = false, $limit = "0,10", $order_by = "enroll_id DESC")
    {
        $event_id = (int)$event_id;
        if( $event_id < 1 )
        {
            $this->set_err_msg('event_id不正确');
            return false;
        }
        $where_str = "event_id = {$event_id}";
        if($status!=='')
        {
            $status = (int)$status;
            if (in_array($status, array(0, 1, 2))) 
            {
                $where_str .= " AND status = {$status}";
            }else{
                if($status==3)
                {
                    $where_str .= " AND status < 2";	//正式报名与候补
                }else{
                    $this->set_err_msg('status不正确');
                    return false;
                }
            }
        }

        $rows = $this->get_enroll_list($where_str,$b_select_count,$limit,$order_by);
        return $rows;
    }

     /**
     * 单活动的报名成功数
     *
     * @param int $event_id
     * @return int $count
     */

    public function get_enroll_count_by_event_id($event_id,$status=0)
    {
        $event_id = (int)$event_id;
        if( $event_id < 1 )
        {
            $this->set_err_msg('event_id不正确');
            return false;
        }
        $where_str = "event_id = {$event_id}";
        if($status!=="")
        {
            $status = (int)$status;
            if (in_array($status, array(0, 1, 2)))
            {
                $where_str .= " AND status = {$status}";
            }else{
                if($status==3)
                {
                    $where_str .= " AND status < 2";	//正式报名与候补
                }else{
                    return false;
                }
            }

        }
        $count = $this->get_enroll_list($where_str,false,'0,1','','sum(enroll_num) as c');
        $total = (int)$count[0]['c'];
        return $total;
    }

    
     /**
     * 单活动的场次报名成功数
     *
     * @param int $event_id
     * @param array $status
     * @param int $table_id
     * @return int $count
     * 
     */

    public function get_enroll_count_by_event_id_v2($event_id,$status=array(0,1),$table_id=0)
    {
        $param[] = $event_id;
        $param[] = $status;
        $param[] = $table_id;
        $ret = curl_event_data('event_enroll_class','get_enroll_count_by_event_id_v2',$param);
        return $ret;
    }
    
    /**
     * 检查活动报名是否已满
     *
     * @param int $enroll_num	报名人数
     * @param int $event_id		报名对象活动id
     * @return bool
     */

    public function check_is_full($enroll_num=1,$event_id, $table_id,$status=array(0,1))
    {
        $param[] = $enroll_num;
        $param[] = $event_id;
        $param[] = $table_id;
        $param[] = $status;
        $ret = curl_event_data('event_enroll_class','check_is_full',$param);
        return $ret;
    }
            

    /**
     * 同步更新活动报名人数到活动正式表
     *
     * @param int $event_id	活动id
     * @return bool
     */

    public function update_event_join_count_by_event_id($event_id)
    {
        $param[] = $event_id;
        $ret = curl_event_data('event_enroll_class','update_event_join_count_by_event_id',$param);
        return $ret;
    }	
    
    /**
     * 更新活动active_time
     *
     * @param int $event_id	活动id
     * @return bool
     */

    public function update_event_active_time_by_event_id($event_id)
    {
        $param[] = $event_id;
        $ret = curl_event_data('event_enroll_class','update_event_active_time_by_event_id',$param);
        return $ret;
    }	
    
    
    /**
     * 增加活动动态
     *
     * @param int $event_id		活动id
     * @param int $user_id		用户id
     * @return bool
     */

    public function update_event_feed_by_event_id_and_user_id($event_id,$user_id)
    {
        $param[] = $event_id;
        $param[] = $user_id;
        $ret = curl_event_data('event_enroll_class','update_event_feed_by_event_id_and_user_id',$param);
        return $ret;
    }	

    /**
     * 根据活动ID和用户ID，获取报名资料
     *
     * @param int $event_id
     * @param int $user_id
     * @return array
     */
    public function get_enroll_info_by_event_id_and_user_id($event_id,$user_id)
    {
        $param[] = $event_id;
        $param[] = $user_id;
        $ret = curl_event_data('event_enroll_class','get_enroll_info_by_event_id_and_user_id',$param);
        return $ret;
    }

    
    /**
     * 根据活动ID和用户ID，获取报名资料(该用户所有报名资料)
     *
     * @param int $event_id
     * @param int $user_id
     * @return array
     */
    public function get_enroll_info_by_event_id_and_user_id_v2($event_id,$user_id)
    {
        $param[] = $event_id;
        $param[] = $user_id;
        $ret = curl_event_data('event_enroll_class','get_enroll_info_by_event_id_and_user_id_v2',$param);
        return $ret;
    }	
    
    
    
    /**
     * 根据活动ID和用户ID，场次ID，获取报名资料
     *
     * @param int $event_id
     * @param int $user_id
     * @param int $table_id
     * @return array
     */
    public function get_enroll_info_by_event_id_and_user_id_and_table_id($event_id,$user_id,$table_id)
    {
        $param[] = $event_id;
        $param[] = $user_id;
        $param[] = $table_id;
        $ret = curl_event_data('event_enroll_class','get_enroll_info_by_event_id_and_user_id_and_table_id',$param);
        return $ret;
    }
    

    
    /**
     * 检查活动报名者是否符合报名资格
     *
     * @param int $user_id		用户id
     * @param int $event_id		报名对象活动id
     * @param string $join_mode	报名方式
     * @return bool	
     */

    public function check_join_mode_enroll($user_id, $event_id)
    {
        $param[] = $user_id;
        $param[] = $event_id;
        $ret = curl_event_data('event_enroll_class','check_join_mode_enroll',$param);
        return $ret;
    }


    /**
     * 检查活动报名是否过期,是否已经报名
     *
     * @param int $user_id		用户id
     * @param int $event_id		报名对象活动id
     * @return string
     * 返回“D”表示此活动此用户已经报过名;
     * 返回“O”表示已经报名时间过期，报名失败;
     * 返回“N”表示未报名;
     * 返回“F”表示此活动报名人数已满，报名失败;
     *
     */
    public function check_enroll_full($user_id, $event_id)
    {
        $param[] = $user_id;
        $param[] = $event_id;
        $ret = curl_event_data('event_enroll_class','check_enroll_full',$param);
        return $ret;
    }

    /**
     * 检查活动报名是否过期,是否已经报名
     *
     * @param int $user_id		用户id
     * @param int $event_id		报名对象活动id
     * @param int $enroll_num	报名人数
     * @return string	返回“O”表示已经报名时间过期，报名失败;返回“D”表示此活动此用户已经报过名，报名失败;返回“N”表示未报名，可以报名
     */


    public function check_enroll($user_id, $event_id)
    {
        $param[] = $user_id;
        $param[] = $event_id;
        $ret = curl_event_data('event_enroll_class','check_enroll',$param);
        return $ret;
    }		


    /**
     *
     * 获取报名金额
     *
     *
     * */
     public function get_enroll_cost($enroll_id)
    {
        $param[] = $enroll_id;
        $ret = curl_event_data('event_enroll_class','get_enroll_cost',$param);
        return $ret;

    }

    /**
     * 通过多个enroll_id 获取报名金额信息
     * @param  array  $enroll_id_arr
     * @param  string $pay_status  
     * @return array
     */
    function get_enroll_cost_by_arr( $enroll_id_arr,$pay_status=0 ){

        $param[] = $enroll_id_arr;
        $param[] = $pay_status;
        $ret = curl_event_data('event_enroll_class','get_enroll_cost_by_arr',$param);
        return $ret;
    }

    /**
     * 获取某报名对应的场次
     *
     * @param int $enroll_id 场次ID
     * @return  $enroll_rel_table 场次
     *
     * */       
    public function get_event_table_by_enroll_id($enroll_id){

        $param[] = $enroll_id;
        $ret = curl_event_data('event_enroll_class','get_event_table_by_enroll_id',$param);
        return $ret;

    }

    /**
     *
     *更新报名支付状态 0=>待支付 1=>已支付 2=>已取消
     *
     **/
    public function update_enroll_pay_status($enroll_id, $status)
    {
        $enroll_id = (int)$enroll_id;
        $where_str = "enroll_id = $enroll_id";
        switch($status)
        {	
            case 0:
                $data['pay_status'] = 0;
                $data['pay_time']	= time();
                curl_event_data('event_enroll_class','update_api',array($data,$where_str));
                break;
            case 1:
                $data['pay_status']         = 1;
                $data['pay_time']	        = time();
                $enroll_info                = $this->get_enroll_by_enroll_id($enroll_id);
                //检查报名人数（正选及候补）是否已满
                $return                     = $this->check_is_full($enroll_info['enroll_num'],$enroll_info['event_id'],$enroll_info['table_id'],array(0));
                $return ? $data['status']   = 1:$data['status'] = 0;
                $ret                        = curl_event_data('event_enroll_class','update_api',array($data,$where_str));


                $log_arr['ret'] = $ret;
                $log_arr['enroll_id'] = $enroll_id;
                $log_arr['status'] = $status;
                pai_log_class::add_log($log_arr, 'ret', 'update_enroll');

                if( $ret ){
                    //生成二维码
                    include_once ('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');
                    $code_obj       = POCO::singleton('pai_activity_code_class');
                    $event_obj      = POCO::singleton('event_details_class');
                    $pai_sms_obj    = POCO::singleton('pai_sms_class');
                    $pai_user_obj   = POCO::singleton('pai_user_class');
                    $pai_config_obj                = POCO::singleton('pai_config_class' );
                    $special_event_arr             = $pai_config_obj->big_waipai_event_id_arr();
                    $special_big_waipai_event_arr  = $pai_config_obj->big_waipai_event_id_arr('big_waipai');
                    $event_info         = $event_obj->get_event_by_event_id($enroll_info['event_id']);
                    $enroll_table       = $this->get_event_table_by_enroll_id($enroll_id);
                    $enroll_user_id     = get_relate_yue_id($enroll_info['user_id']);
                    $enroll_user_phone  = $pai_user_obj->get_phone_by_user_id($enroll_user_id);
                    $event_user_id      = get_relate_yue_id($event_info['user_id']);
                    //ecpay_log_class::add_log(array_merge($enroll_info,array('share_event_id'=>$_REQUEST['share_event_id'],'share_phone'=>$_REQUEST['share_phone'])), __FILE__ . '::' . __LINE__, 'info20150306');
                    

                    if( $data['status'] == 0 ){
                        //正选才需要发  
                        $table_obj     = POCO::singleton('event_table_class');
                        //该场次限制的人数
                        $limit_num     = $table_obj->get_table_num($enroll_info['event_id'],$enroll_info['table_id']);
                        //获取该场次已参加的人数
                        $enroll_count  = $this->get_enroll_count_by_event_id_v2($enroll_info['event_id'],array(0),$enroll_info['table_id']);
                        $enroll_count -= $enroll_info['enroll_num'];
                        //接口返回的是总人数  要减去当前参加的；得出除当前参与的已参加的人数。
                        //ecpay_log_class::add_log(array('limit_num'=>$limit_num,'enroll_count'=>$enroll_count,'enroll_info'=>$enroll_info), __FILE__ . '::' . __LINE__, '210data1');

                        if( ($limit_num > $enroll_count) && ($enroll_count+$enroll_info['enroll_num']) >= $limit_num ){
                            //发信息给组织者  通知他人数已达标
                            $app_msg    = "你发布的{$event_info['title']}活动第{$enroll_table}场报名人数已达到你的要求。";
                            $url        = "/mall/user/act/detail.php?event_id={$enroll_info['event_id']}";
                            send_message_for_10002 ( $event_user_id,$app_msg,$url );

                        }

                    }

					//暂时处理   二维码表需要记pai 的 user_id
                    $code_obj->create_multi_code($enroll_info['enroll_num'],$event_user_id,$event_info['event_id'],$enroll_id);
                    //获取验证码
                    /*special_event_arr 特殊的活动不需要扫码  不需要发短信*/
                    $activity_code_arr = $code_obj->get_code_by_enroll_id($enroll_id);
                    if( !empty($activity_code_arr)  )
                    {
                        $leader_info_arr=unserialize($event_info['leader_info']);
                        $leader_info = $leader_info_arr[0]['mobile'];

                         foreach( $activity_code_arr as $k=>$v ){

                             //$v['code'] = activity_code_transfer($v['code']);


                             if( $data['status'] == 0 ){
                                //正选要  短信+app小助手
                                $group_key   = 'G_PAI_WAIPAI_CODE_NUMBER';
                                $sms_data    = array(

                                     'event_title'   => $event_info['title'],
                                     'activity_code' => $v['code'],
                                     'table_num'     => $enroll_table,
                                     'cellphone'     => $leader_info

                                );
                                $pai_sms_obj->send_sms($enroll_user_phone, $group_key,$sms_data);
                                $app_msg   = "恭喜你,{$event_info['title']}活动的第{$enroll_table}场报名成功,到场可通过APP出示二维码或提供数字密码{$v['code']}签到，组织者联系方式：".$leader_info;
                                $url = "/mall/user/act/detail.php?event_id={$enroll_info['event_id']}";
                                send_message_for_10002 ( $enroll_user_id,$app_msg,$url,'yuebuyer' );
  
                             }
                             elseif( $data['status'] == 1 ){

                                 $app_msg   = "恭喜你,已成为活动{$event_info['title']}第{$enroll_table}场的候补，到场可通过APP出示二维码或提供数字密码{$v['code']}签到，组织者联系方式：".$leader_info;

                                 $url = "/mall/user/act/detail.php?event_id={$enroll_info['event_id']}";
                                 send_message_for_10002 ( $enroll_user_id,$app_msg,$url,'yuebuyer' );
                                 
                                $group_key   = 'G_PAI_WAIPAI_CODE_NUMBER_B';
                                $sms_data    = array(

                                     'event_title'   => $event_info['title'],
                                     'activity_code' => $v['code'],
                                     'table_num'     => $enroll_table,
                                     'cellphone'     => $leader_info

                                );
                                $pai_sms_obj->send_sms($enroll_user_phone, $group_key,$sms_data);
                             }

                         }

                    }

                }
                return $ret;
                break;
            case 2:
                $data['pay_status'] = 2;
                $data['pay_time']	= time();
                curl_event_data('event_enroll_class','update_api',array($data,$where_str));
                break;
            default:
                return false;

        }
    }

    public function update_enroll_join_event($enroll_id, $status)
    {
        $enroll_id = (int)$enroll_id;
        $where_str = "enroll_id = $enroll_id";

        $data['join_event'] = $status;
        return curl_event_data('event_enroll_class','update_api',array($data,$where_str));
    }

    
    /**
     * 检查用户是否具备参与活动的权限
     * @param int $event_id       活动ID
     * @param int $enroll_user_id 参与者ID
     * @return bool
     */
    public function check_join_mode_auth( $event_id,$enroll_user_id ){

        $param[] = $event_id;
        $param[] = $enroll_user_id;
        $ret = curl_event_data('event_enroll_class','check_join_mode_auth',$param);
        return $ret;

    }
    
    public function sum_enroll_num($event_id,$table_id=0,$status='0')
    {

        $param[] = $event_id;
        $param[] = $table_id;
        $param[] = $status;
        $ret = curl_event_data('event_enroll_class','sum_enroll_num',$param);
        return $ret;
    }
    
}
?>